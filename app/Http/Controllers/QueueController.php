<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    public function index()
    {
        $queues = Queue::whereDate('created_at', today())
                       ->orderBy('number', 'desc')
                       ->get();

        return view('queue.index', compact('queues'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'order_type'  => 'required|in:dine_in,take_away',
            'table_number'=> 'nullable|integer|min:1',
            'customer_name'=> 'nullable|string',
        ]);

        $lastNumber = Queue::whereDate('created_at', today())
                           ->max('number') ?? 0;

        $queue = Queue::create([
            'number'        => $lastNumber + 1,
            'customer_name' => $request->customer_name,
            'order_type'    => $request->order_type,
            'table_number'  => $request->order_type === 'dine_in' ? $request->table_number : null,
            'status'        => 'menunggu',
        ]);

        return back()->with('success', 'Nomor antrian berhasil dibuat!');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:dine_in,take_away',
        ]);

        $last = Queue::orderBy('number', 'desc')->first();
        $nextNumber = $last ? $last->number + 1 : 1;

        Queue::create([
            'number' => $nextNumber,
            'status' => 'menunggu',
            'type'   => $request->type,
        ]);

        return back()->with('success', 'Nomor antrian berhasil dibuat');
    }

    public function call($id)
    {
        Queue::where('status', 'dipanggil')->update(['status' => 'menunggu']);
        $queue = Queue::findOrFail($id);
        $queue->status = 'dipanggil';
        $queue->save();

        return back()->with([
            'success' => 'Memanggil nomor ' . $queue->number,
            'called_name' => $queue->customer_name,
            'called_number' => $queue->number
        ]);
    }

    public function recall($id)
    {
        $queue = Queue::findOrFail($id);
        $queue->updated_at = now();
        $queue->save();

        return back()->with('success', 'Nomor antrian dipanggil ulang!');
    }

    public function callSpecific(Request $request)
    {
        $request->validate([
            'number' => 'required|integer|min:1',
        ]);

        $queue = Queue::where('number', $request->number)
                      ->where('status', 'menunggu')
                      ->first();

        if (! $queue) {
            return back()->with('error', 'Nomor antrian tidak ditemukan atau sudah diterima!');
        }

        Queue::where('status', 'dipanggil')->update(['status' => 'menunggu']);

        $queue->status = 'dipanggil';
        $queue->updated_at = now();
        $queue->save();

        return back()
            ->with('openActionModal', true)
            ->with('called_number', $queue->number)
            ->with('called_id', $queue->id)
            ->with('success', 'Nomor antrian ' . $queue->number . ' dipanggil!');
    }

    public function receive($id)
    {
        $queue = Queue::findOrFail($id);
        $queue->status = 'diterima';
        $queue->save();

        return back()->with('success', 'Nomor ' . $queue->number . ' sudah diterima customer');
    }

    public function cancel($id)
    {
        $queue = Queue::findOrFail($id);
        $queue->status = 'cancel';
        $queue->save();

        return back()->with('success', 'Nomor ' . $queue->number . ' dibatalkan');
    }

    public function display()
    {
        $current = Queue::where('status', 'dipanggil')
                        ->orderBy('updated_at', 'desc')
                        ->first();

        $waiting = Queue::where('status', 'menunggu')
                        ->orderBy('number', 'asc')
                        ->take(5)
                        ->get();

        return view('queue.display', compact('current', 'waiting'));
    }
}
