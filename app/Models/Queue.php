<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Queue extends Model {
    use HasFactory;
    protected $fillable=['number','status', 'customer_name', 'order_type', 'table_number'];
}
