<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Antrian</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background: #f5f5f5; }
        .card { border-radius: 18px; }
        .modal-bg {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.4);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1050;
        }
        .modal-box {
            background: white;
            width: 320px;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
        }
        .modal-input {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>
</head>
<body class="p-5">
    <div class="container">
        <div class="card p-4 shadow-lg mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">📢 Sistem Antrian</h2>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">Logout</button>
                </form>
            </div>

            @if(session('success'))
                <div class="alert alert-success" style="text-align: center; font-weight:bold">{{ session('success') }}</div>
            @elseif(session('error'))
                <div class="alert alert-danger" style="text-align: center; font-weight: bold">{{ session('error') }}</div>
            @endif

            <div class="d-flex justify-content-center gap-3 mb-3">
                <button id="openCreateModal" class="btn btn-primary btn-lg">Buat Nomor Antrian</button>
                <button id="openCallModal" class="btn btn-outline-primary btn-lg">Panggil Nomor Spesifik</button>
            </div>

            <p class="text-center text-muted mb-0">Klik "Buat Nomor Antrian" untuk mengisi nama dan memilih tipe pesanan.</p>
        </div>

        <div class="card p-4 shadow-lg">
            <h4 class="mb-3">Daftar Antrian Hari Ini</h4>

            <div class="table-responsive" style="max-height:400px; overflow:auto;">
                <table class="table mt-3 mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Customer</th>
                            <th>Table Number</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($queues as $q)
                            <tr>
                                <td>{{ $q->number }}</td>
                                <td>{{ $q->customer_name ?? '-' }}</td>
                                <td>{{ $q->table_number ?? '-' }}</td>
                                <td>
                                    @if($q->order_type == 'dine_in')
                                        <span class="badge bg-primary">Dine In</span>
                                    @else
                                        <span class="badge bg-info">Take Away</span>
                                    @endif
                                </td>
                                <td>
                                    @switch($q->status)
                                        @case('menunggu')   <span class="badge bg-warning">Menunggu</span> @break
                                        @case('dipanggil')  <span class="badge bg-primary">Dipanggil</span> @break
                                        @case('diterima')   <span class="badge bg-success">Diterima</span> @break
                                        @case('cancel')     <span class="badge bg-danger">Dibatalkan</span> @break
                                    @endswitch
                                </td>
                                <td>
                                    @if($q->status == 'menunggu')
                                        <a href="{{ route('queue.call', $q->id) }}" class="btn btn-sm btn-primary">Panggil</a>
                                    @endif

                                    @if($q->status == 'dipanggil')
                                        <a href="{{ route('queue.receive', $q->id) }}" class="btn btn-sm btn-success">Diterima</a>
                                        <a href="{{ route('queue.recall', $q->id) }}" class="btn btn-sm btn-secondary">Panggil Ulang</a>
                                    @endif

                                    @if($q->status != 'cancel' && $q->status != 'diterima')
                                        <a href="{{ route('queue.cancel', $q->id) }}" class="btn btn-sm btn-danger">Cancel</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow">
          <div class="modal-header">
            <h5 class="modal-title">Buat Nomor Antrian</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

            <form action="{{ route('queue.generate') }}" method="POST">
                @csrf
                <div class="modal-body">

                    <input type="text" name="customer_name" class="form-control mb-3"
                        placeholder="Nama Customer (opsional)">

                    <select name="order_type" id="orderType" class="form-select mb-3">
                        <option value="take_away">Take Away</option>
                        <option value="dine_in">Dine In</option>
                    </select>

                    <!-- Input nomor meja -->
                    <input type="number" name="table_number" id="tableNumberInput"
                        class="form-control"
                        placeholder="Nomor Meja"
                        min="1"
                        required>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Buat</button>
                </div>
            </form>
        </div>
      </div>
    </div>

    <div class="modal fade" id="callModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow">
          <div class="modal-header">
            <h5 class="modal-title">Panggil Nomor Tertentu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <form method="POST" action="{{ route('queue.callSpecific') }}">
            @csrf
            <div class="modal-body">
              <input type="number" name="number" class="form-control" placeholder="Masukkan nomor..." required>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary">Panggil</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="modal fade" id="actionModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow">
          <div class="modal-header">
            <h5 class="modal-title">Nomor Dipanggil: <span id="calledNumber"></span></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body text-center">
            <a id="recallBtn" href="#" class="btn btn-info btn-lg mb-2 w-100">🔁 Panggil Ulang</a>
            <a id="receivedBtn" href="#" class="btn btn-success btn-lg w-100">✔ Diterima</a>
          </div>
        </div>
      </div>
    </div>

<script>
document.getElementById('openCreateModal').addEventListener('click', () => {
    new bootstrap.Modal(document.getElementById('createModal')).show();
});

document.getElementById('openCallModal').addEventListener('click', () => {
    new bootstrap.Modal(document.getElementById('callModal')).show();
});

@if(session('called_id'))
    document.getElementById('calledNumber').innerText = "{{ session('called_number') }}";
    document.getElementById('recallBtn').href = "/queue/recall/{{ session('called_id') }}";
    document.getElementById('receivedBtn').href = "/queue/receive/{{ session('called_id') }}";
    new bootstrap.Modal(document.getElementById('actionModal')).show();
@endif
</script>

@if(session('openActionModal'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('calledNumber').innerText = "{{ session('called_number') }}";
    document.getElementById('recallBtn').href = "/queue/recall/{{ session('called_id') }}";
    document.getElementById('receivedBtn').href = "/queue/receive/{{ session('called_id') }}";
    let modal = new bootstrap.Modal(document.getElementById('actionModal'));
    modal.show();
});
</script>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    const orderType = document.getElementById('orderType');
    const tableInput = document.getElementById('tableNumberInput');

    function toggleTableInput() {
        if (orderType.value === 'dine_in') {
            tableInput.style.display = 'block';
            tableInput.required = true;
        } else {
            tableInput.style.display = 'none';
            tableInput.required = false;
            tableInput.value = "";
        }
    }

    orderType.addEventListener('change', toggleTableInput);
    toggleTableInput(); // running saat load awal
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
