<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Display Antrian - Fullscreen</title>

    <!-- Auto refresh setiap 5 detik -->
    <meta http-equiv="refresh" content="5">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        /* === FULLSCREEN NO SCROLL === */
        html, body {
            height: 100%;
            margin: 0;
            overflow: hidden;
            background: linear-gradient(135deg, #0d6efd, #6610f2);
            font-family: "Segoe UI", sans-serif;
            color: white;
        }

        /* === CURRENT NUMBER (BIG CENTERED) === */
        .current-wrapper {
            height: 65vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .current-label {
            font-size: 48px;
            opacity: 0.9;
        }

        .current-number {
            font-size: 180px;
            font-weight: 900;
            line-height: 1;
        }

        .customer-name {
            font-size: 48px;
            margin-top: 10px;
            font-weight: 500;
        }

        .table-number {
            font-size: 38px;
            margin-top: 10px;
            opacity: 0.85;
        }

        /* === WAITING LIST BAR (BOTTOM) === */
        .waiting-bar {
            height: 35vh;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 20px 40px;
            border-radius: 30px 30px 0 0;
        }

        .waiting-title {
            font-size: 32px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .waiting-number {
            font-size: 65px;
            font-weight: 800;
        }

        .waiting-name {
            font-size: 24px;
            opacity: 0.85;
        }

        /* status colors */
        .status-menunggu { color: #ff4d4d; }
        .status-dipanggil { color: #ffea00; }
        .status-diterima { color: #4caf50; }

    </style>
</head>

<body>

    <!-- CURRENT NUMBER -->
    <div class="current-wrapper">
        <div class="current-label">Sedang Dipanggil</div>

        <div class="current-number">
            {{ $current->number ?? '-' }}
        </div>

        @if($current && $current->customer_name)
            <div class="customer-name">
                {{ strtoupper($current->customer_name) }}
            </div>
        @endif

        @if($current && $current->table_number)
            <div class="table-number">
                Meja {{ $current->table_number }}
            </div>
        @endif
    </div>

    <!-- WAITING LIST -->
    <div class="waiting-bar">
        <div class="waiting-title text-center">
            Antrian Berikutnya
        </div>

        @if($waiting->count() == 0)
            <p class="text-center">Tidak ada antrian berikutnya</p>
        @else
            <div class="d-flex justify-content-between text-center">
                @foreach($waiting->take(4) as $q)
                    <div class="flex-fill">
                        <div class="waiting-number status-{{ $q->status }}">
                            {{ $q->number }}
                        </div>

                        @if($q->customer_name)
                            <div class="waiting-name">
                                {{ strtoupper($q->customer_name) }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <script>
    // Ambil data dari Laravel
    const currentNumber = "{{ $current->number ?? '' }}";
    const currentTable = "{{ $current->table_number ?? '' }}";
    const currentName = "{{ $current->customer_name ?? '' }}";

    // Nomor terakhir yang sudah dibacakan
    const lastCalled = localStorage.getItem("last_called_number");

    if (currentNumber && currentNumber !== lastCalled) {

        let text = "";

        // Jika ada nama customer → gunakan format khusus
        if (currentName) {
            text = `Nomor antrian ${currentNumber} atas nama ${currentName}, pesanan siap diambil`;
        } else {
            // Tanpa nama
            text = `Nomor antrian ${currentNumber}, pesanan siap diambil`;
        }

        // Tambahkan lokasi meja jika ada
        if (currentTable) {
            text += ` dengan nomor meja ${currentTable}`;
        }

        // Fungsi bicara
        function speak(text) {
            const msg = new SpeechSynthesisUtterance(text);
            msg.lang = "id-ID";
            msg.rate = 0.9;
            msg.pitch = 1;
            msg.volume = 1;
            speechSynthesis.speak(msg);
        }

        speak(text);

        // Simpan agar tidak dibaca ulang
        localStorage.setItem("last_called_number", currentNumber);
    }
</script>


</body>
</html>
