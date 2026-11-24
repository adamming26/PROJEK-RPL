@extends('layouts.app')

@section('title', 'Pindai QR Code')
@section('page_title', 'Pemindai QR Code')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Pindai QR Code</h2>
    <p class="text-sm text-slate-500">Validasi QR Code saat penyerahan dan pengembalian, lalu konfirmasi transaksinya.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

    <!-- Kolom Kiri: Kamera Scanner & Kemungkinan Hasil (Matches Bab III Wireframe 3.10) -->
    <div class="lg:col-span-7 space-y-6">

        <!-- Card Pemindai Kamera -->
        <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200">
            <!-- Tabs Penyerahan / Pengembalian -->
            <div class="flex items-center gap-2 mb-6">
                <a href="{{ route('dosen.pindai-qr', ['tab' => 'penyerahan']) }}"
                   class="flex-1 py-2.5 text-center rounded-xl font-bold text-xs transition-all {{ $tab === 'penyerahan' ? 'bg-blue-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Penyerahan Fasilitas
                </a>
                <a href="{{ route('dosen.pindai-qr', ['tab' => 'pengembalian']) }}"
                   class="flex-1 py-2.5 text-center rounded-xl font-bold text-xs transition-all {{ $tab === 'pengembalian' ? 'bg-blue-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Pengembalian Fasilitas
                </a>
            </div>

            <!-- Scanner Camera Box -->
            <div class="relative bg-slate-900 rounded-2xl overflow-hidden border border-slate-200 min-h-[300px] flex flex-col items-center justify-center text-white mb-4 shadow-inner">
                <div id="qr-reader" class="w-full max-w-sm"></div>
                <div id="scanner-placeholder" class="p-8 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-white/10 flex items-center justify-center mx-auto mb-3 text-blue-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <p class="text-sm font-semibold text-slate-200 mb-2">Arahkan kamera ke QR Code Mahasiswa</p>
                    <button type="button" onclick="startScanner()" id="btn-start-camera" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 font-bold text-xs shadow-lg transition-all">
                        Nyalakan Kamera
                    </button>
                </div>
            </div>

            <!-- Controls & Manual Input Option -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="restartScanner()" class="px-4 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Pindai ulang
                </button>

                <!-- Input manual token untuk testing atau jika kamera tidak diizinkan -->
                <form method="GET" action="{{ route('dosen.pindai-qr') }}" class="flex items-center gap-2 w-full sm:w-auto">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <input type="text" name="token" id="input-token" value="{{ $token }}" placeholder="Masukkan token manual..."
                           class="w-full sm:w-56 px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-slate-800 text-white font-bold text-xs hover:bg-slate-900 transition-colors whitespace-nowrap">
                        Periksa
                    </button>
                </form>
            </div>
        </div>

        <!-- Card Kemungkinan Hasil Validasi (Matches Bab III Wireframe 3.10) -->
        <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">
                Kemungkinan hasil validasi
            </h3>

            <div class="space-y-3">
                <div class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-200/60">
                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold inline-block mb-1">
                        QR valid
                    </span>
                    <p class="text-xs text-slate-600">
                        Masih berlaku (dalam rentang 15 menit) dan belum digunakan. Detail transaksi tampil di sebelah kanan untuk dikonfirmasi.
                    </p>
                </div>

                <div class="p-3.5 rounded-2xl bg-rose-50/70 border border-rose-200/60">
                    <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-800 text-[10px] font-bold inline-block mb-1">
                        QR kedaluwarsa
                    </span>
                    <p class="text-xs text-slate-600">
                        Masa berlaku 15 menit sudah habis. Transaksi ditolak oleh sistem dan mahasiswa harus membuat QR Code baru.
                    </p>
                </div>

                <div class="p-3.5 rounded-2xl bg-amber-50/70 border border-amber-200/60">
                    <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold inline-block mb-1">
                        QR sudah digunakan
                    </span>
                    <p class="text-xs text-slate-600">
                        QR Code dinonaktifkan seketika setelah berhasil dipindai dan dikonfirmasi untuk mencegah pemindaian ganda.
                    </p>
                </div>
            </div>
        </div>

    </div>

    <!-- Kolom Kanan: Detail Transaksi & Form Konfirmasi (Matches Bab III Wireframe 3.10) -->
    <div class="lg:col-span-5 space-y-6">

        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xs border border-slate-200" id="transaction-panel">
            <h3 class="text-base font-bold text-slate-900 pb-3 mb-4 border-b border-slate-100">Detail transaksi</h3>

            <div id="validation-alert" class="hidden mb-4 p-3.5 rounded-2xl text-xs font-semibold"></div>

            @if($validationResult && $validationResult['valid'])
                @php
                    $qr = $validationResult['qr'];
                    $peminjaman = $qr->peminjaman;
                @endphp

                <!-- Info Badges -->
                <div class="flex items-center gap-2 mb-4">
                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-bold">
                        QR valid
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200 text-[11px] font-bold">
                        {{ $qr->jenis_qr === 'PEMINJAMAN' ? 'Penyerahan' : 'Pengembalian' }}
                    </span>
                </div>

                <dl class="space-y-2 text-xs mb-6">
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Peminjam:</dt>
                        <dd class="font-bold text-slate-900">{{ $peminjaman->user->nama }} ({{ $peminjaman->user->nomor_induk }})</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Fasilitas:</dt>
                        <dd class="font-bold text-slate-900">{{ $peminjaman->nama_fasilitas }} ({{ $peminjaman->jumlah }} unit)</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Waktu Jadwal:</dt>
                        <dd class="font-semibold text-slate-800">{{ $peminjaman->tanggal_peminjaman->format('d/m/Y') }} ({{ substr($peminjaman->jam_mulai, 0, 5) }} - {{ substr($peminjaman->jam_selesai, 0, 5) }})</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Masa Berlaku:</dt>
                        <dd class="font-bold text-amber-700">Berlaku {{ $qr->remaining_seconds }} detik lagi. Menjadi nonaktif setelah dipindai.</dd>
                    </div>
                </dl>

                @if($tab === 'penyerahan' || $qr->jenis_qr === 'PEMINJAMAN')
                    <!-- FORM KONFIRMASI PENYERAHAN -->
                    <form method="POST" action="{{ route('dosen.proses-penyerahan') }}" class="space-y-4 pt-4 border-t border-slate-100">
                        @csrf
                        <input type="hidden" name="token" value="{{ $qr->token }}">

                        <div class="p-3.5 rounded-2xl bg-blue-50/70 border border-blue-200/60 text-xs text-blue-900 leading-relaxed">
                            Pastikan fisik barang / izin ruangan diserahkan kepada <strong>{{ $peminjaman->user->nama }}</strong>. Stok inventaris akan berkurang otomatis sebanyak {{ $peminjaman->jumlah }} unit.
                        </div>

                        <button type="submit" class="w-full py-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition-all">
                            Konfirmasi Penyerahan Fasilitas
                        </button>
                    </form>

                @else
                    <!-- FORM KONFIRMASI PENGEMBALIAN (Matches Wireframe 3.10) -->
                    <form method="POST" action="{{ route('dosen.proses-pengembalian') }}" class="space-y-4 pt-4 border-t border-slate-100">
                        @csrf
                        <input type="hidden" name="token" value="{{ $qr->token }}">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="kondisi_barang" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kondisi barang</label>
                                <select name="kondisi_barang" id="kondisi_barang" required
                                        class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none bg-white">
                                    <option value="BAIK">Baik</option>
                                    <option value="RUSAK">Rusak</option>
                                    <option value="TIDAK LENGKAP">Tidak lengkap</option>
                                </select>
                            </div>

                            <div>
                                <label for="jumlah_kembali" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jumlah dikembalikan</label>
                                <input type="number" name="jumlah_kembali" id="jumlah_kembali" value="{{ $peminjaman->jumlah }}" min="1" max="{{ $peminjaman->jumlah }}" required
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label for="catatan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Catatan</label>
                            <input type="text" name="catatan" id="catatan" placeholder="Opsional (misal: kabel agak kotor, dsb)..."
                                   class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                        </div>

                        <button type="submit" class="w-full py-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition-all">
                            Konfirmasi Transaksi Pengembalian
                        </button>
                    </form>
                @endif

            @elseif($validationResult && !$validationResult['valid'])
                <div class="p-6 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-center">
                    <div class="w-12 h-12 rounded-full bg-rose-100 flex items-center justify-center mx-auto mb-2 text-rose-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </div>
                    <h4 class="font-bold text-xs uppercase tracking-wider mb-1">Validasi Gagal</h4>
                    <p class="text-xs">{{ $validationResult['message'] }}</p>
                    <a href="{{ route('dosen.pindai-qr', ['tab' => $tab]) }}" class="inline-block mt-4 px-4 py-1.5 rounded-xl bg-rose-600 text-white font-bold text-xs">
                        Pindai Ulang
                    </a>
                </div>

            @else
                <!-- Default Placeholder before scan -->
                <div class="py-12 text-center text-slate-400">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-3 text-slate-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <p class="text-xs text-slate-500 font-medium">Belum ada QR Code yang dipindai.</p>
                    <p class="text-[11px] text-slate-400 mt-1">Nyalakan kamera atau masukkan token untuk memvalidasi transaksi.</p>
                </div>
            @endif
        </div>

    </div>

</div>

@push('scripts')
<script>
    let html5QrcodeScanner = null;

    function startScanner() {
        document.getElementById('scanner-placeholder').classList.add('hidden');

        html5QrcodeScanner = new Html5Qrcode("qr-reader");

        const config = { fps: 10, qrbox: { width: 220, height: 220 } };

        html5QrcodeScanner.start({ facingMode: "environment" }, config, onScanSuccess)
            .catch(err => {
                console.error("Camera access error:", err);
                alert("Tidak dapat mengakses kamera perangkat (" + err + "). Anda dapat menggunakan kolom 'Masukkan token manual' di bawah.");
                document.getElementById('scanner-placeholder').classList.remove('hidden');
            });
    }

    function onScanSuccess(decodedText, decodedResult) {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.stop();
        }

        // Tampilkan token dan redirect untuk validasi backend
        document.getElementById('input-token').value = decodedText;
        const tab = "{{ $tab }}";
        window.location.href = "{{ route('dosen.pindai-qr') }}?tab=" + tab + "&token=" + encodeURIComponent(decodedText);
    }

    function restartScanner() {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.stop().then(() => {
                startScanner();
            }).catch(() => {
                startScanner();
            });
        } else {
            startScanner();
        }
    }
</script>
@endpush
@endsection
