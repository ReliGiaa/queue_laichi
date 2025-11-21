<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Display Antrian</title>

    <meta http-equiv="refresh" content="5"> <!-- AUTO REFRESH -->

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        body {
            background: #f8f9fa;
            font-family: "Segoe UI", sans-serif;
        }

        .current-box {
            background: #0d6efd;
            color: white;
            padding: 40px;
            border-radius: 20px;
        }

        .current-number {
            font-size: 120px;
            font-weight: bold;
        }

        .label-big {
            font-size: 32px;
            opacity: 0.9;
        }

        .customer-name {
            font-size: 40px;
            margin-top: -10px;
            font-weight: 500;
            opacity: 0.95;
        }

        .waiting-box {
            background: #ffffff;
            padding: 25px;
            border-radius: 15px;
            border: 1px solid #ddd;
        }

        .waiting-number {
            font-size: 48px;
            font-weight: bold;
            color: red;
        }

        .waiting-name {
            font-size: 20px;
            color: #333;
            margin-top: 5px;
            opacity: 0.85;
        }

        .table-number {
            font-size: 24px;
            color: #444;
        }
    </style>
</head>

<body class="p-5">

    <div class="container">

        <!-- CURRENTLY CALLED -->
        <div class="current-box text-center mb-5 shadow-lg">
            <div class="label-big">Sedang Dipanggil</div>

            <div class="current-number">
                {{ $current->number ?? '-' }}
            </div>

            <!-- Nama Customer -->
            @if($current && $current->customer_name)
                <div class="customer-name">
                    {{ strtoupper($current->customer_name) }}
                </div>
            @endif

            <!-- Nomor Meja -->
            @if($current && $current->table_number)
                <div style="font-size:32px; margin-top:5px;">
                    Meja {{ $current->table_number }}
                </div>
            @endif
        </div>

        <!-- WAITING LIST -->
        <div class="waiting-box shadow-sm">
            <h3 class="mb-3 text-center">Antrian Berikutnya</h3>

            @if($waiting->count() == 0)
                <p class="text-center text-muted">Tidak ada antrian</p>
            @else
                <div class="row text-center">
                    @foreach($waiting as $q)
                        <div class="col-4 mb-4">

                            <div class="waiting-number">{{ $q->number }}</div>

                            <!-- Nama Customer -->
                            @if($q->customer_name)
                                <div class="waiting-name">
                                    {{ strtoupper($q->customer_name) }}
                                </div>
                            @endif

                            <!-- Meja -->
                            @if($q->table_number)
                                <div class="table-number">Meja {{ $q->table_number }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

</body>
</html>
