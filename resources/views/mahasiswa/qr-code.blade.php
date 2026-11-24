@extends('layouts.app')

@section('title', 'QR Code ' . ($jenisQr === 'PEMINJAMAN' ? 'Peminjaman' : 'Pengembalian'))
@section('page_title', 'QR Code Fasilitas')

@section('content')
<div class="max-w-xl mx-auto py-4">
    <div class="mb-4 flex items-center justify-between">
        <a href="{{ route('mahasiswa.peminjaman-saya') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 flex items-center gap-1">
            &larr; Kembali ke Peminjaman Saya
        </a>
        <span class="text-xs font-mono text-slate-400">{{ $peminjaman->kode_peminjaman }}</span>
    </div>

    <div class="bg-white rounded-3xl p-8 sm:p-10 shadow-lg border border-slate-200 text-center">
        <!-- Header Badge -->
        @if($isExpired)
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-3 bg-rose-50 text-rose-700 border border-rose-200">
                <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                QR Code Kedaluwarsa
            </div>
        @else
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-3
                {{ $jenisQr === 'PEMINJAMAN' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-indigo-50 text-indigo-700 border border-indigo-200' }}">
                <span class="w-2 h-2 rounded-full {{ $jenisQr === 'PEMINJAMAN' ? 'bg-blue-600' : 'bg-indigo-600' }}"></span>
                QR Code {{ $jenisQr === 'PEMINJAMAN' ? 'Penyerahan Fasilitas' : 'Pengembalian Fasilitas' }}
            </div>
        @endif

        <h3 class="text-xl font-extrabold text-slate-900 mb-1">{{ $peminjaman->nama_fasilitas }}</h3>
        <p class="text-xs text-slate-500 mb-6" id="qr-subtitle">
            @if($isExpired)
                Masa tenggang 15 menit telah habis. Kode ini tidak berlaku lagi dan Anda harus mengajukan peminjaman ulang.
            @else
                {{ $jenisQr === 'PEMINJAMAN' ? 'Tunjukkan kode ini kepada Dosen untuk konfirmasi penyerahan barang.' : 'Tunjukkan kode ini kepada Dosen untuk konfirmasi pengembalian barang.' }}
            @endif
        </p>

        <!-- QR Code Display Box -->
        <div class="p-6 bg-slate-50 rounded-3xl border border-slate-200/80 inline-block mb-6 shadow-inner w-full max-w-[280px]">
            @if($isExpired)
                <div class="w-52 h-52 mx-auto rounded-2xl bg-rose-50 border-2 border-dashed border-rose-300 flex flex-col items-center justify-center p-6 text-rose-600">
                    <div class="w-14 h-14 rounded-full bg-rose-100 flex items-center justify-center mb-3">
                        <svg class="w-7 h-7 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="font-extrabold text-sm text-rose-800">KEDALUWARSA</div>
                    <div class="text-[11px] text-rose-600/80 mt-1">Masa berlaku 15 menit habis</div>
                </div>
            @else
                <div id="full-qrcode-canvas" class="flex justify-center"></div>
                <div id="expired-placeholder" class="hidden w-52 h-52 mx-auto rounded-2xl bg-rose-50 border-2 border-dashed border-rose-300 flex flex-col items-center justify-center p-6 text-rose-600">
                    <div class="w-14 h-14 rounded-full bg-rose-100 flex items-center justify-center mb-3">
                        <svg class="w-7 h-7 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="font-extrabold text-sm text-rose-800">KEDALUWARSA</div>
                    <div class="text-[11px] text-rose-600/80 mt-1">Masa berlaku 15 menit habis</div>
                </div>
            @endif
        </div>

        <!-- Real-time Countdown Timer (15 Menit) -->
        <div class="mb-6">
            @if($isExpired)
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-bold shadow-xs">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span>
                    <span>Status: KEDALUWARSA</span>
                </div>
                <p class="text-xs text-rose-600 font-medium mt-2">Masa tenggang telah lewat. Sesuai aturan sistem, buat pengajuan peminjaman ulang.</p>
            @else
                <div id="qr-timer-box" class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-sm font-bold shadow-xs">
                    <svg class="w-4 h-4 text-amber-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Masa berlaku: <span id="full-countdown-timer" class="font-mono text-base">--:--</span></span>
                </div>
                <p class="text-[11px] text-slate-400 mt-2" id="qr-timer-desc">QR Code hanya berlaku selama 15 menit. Jika lewat masa tenggang, wajib mengajukan ulang.</p>
            @endif
        </div>

        @if(!$isExpired && $qr)
        <!-- Token Text for manual verification -->
        <div id="token-box" class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 text-left mb-6">
            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Token Digital Transaksi (Backup Manual)</div>
            <div class="flex items-center justify-between">
                <code id="token-text" class="font-mono text-xs font-bold text-slate-800 break-all select-all">{{ $qr->token }}</code>
                <button type="button" id="btn-copy-token" onclick="copyToken()" class="ml-2 text-xs text-blue-600 font-bold hover:underline flex-shrink-0">
                    Salin
                </button>
            </div>
        </div>
        @endif

        <!-- Transaction Details Summary -->
        <div class="text-left text-xs bg-slate-50/50 p-4 rounded-2xl border border-slate-100 space-y-2 mb-6">
            <div class="flex justify-between">
                <span class="text-slate-400">Pemohon:</span>
                <span class="font-semibold text-slate-800">{{ $peminjaman->user->nama }} ({{ $peminjaman->user->nomor_induk }})</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-400">Jumlah / Peserta:</span>
                <span class="font-semibold text-slate-800">{{ $peminjaman->jumlah }} unit</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-400">Jadwal:</span>
                <span class="font-semibold text-slate-800">{{ $peminjaman->tanggal_peminjaman->format('d M Y') }}, {{ substr($peminjaman->jam_mulai, 0, 5) }} - {{ substr($peminjaman->jam_selesai, 0, 5) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-400">Status Transaksi:</span>
                <span class="font-bold {{ $isExpired ? 'text-rose-600' : 'text-blue-600' }}" id="status-text">
                    {{ $isExpired ? 'KEDALUWARSA' : $peminjaman->status }}
                </span>
            </div>
        </div>

        <!-- Action Section: Jika Kedaluwarsa Wajib Pengajuan Ulang (Bukan Buat QR Baru) -->
        <div id="qr-action-container">
            @if($isExpired)
                <div class="space-y-3">
                    <a href="{{ route('mahasiswa.form-peminjaman', [$peminjaman->jenis_fasilitas, $peminjaman->id_barang ?? $peminjaman->id_ruangan]) }}"
                       class="w-full py-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-lg shadow-blue-600/30 transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        Ajukan Peminjaman Ulang
                    </a>
                    <a href="{{ route('mahasiswa.peminjaman-saya') }}"
                       class="block w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition-colors">
                        Kembali ke Daftar Transaksi
                    </a>
                </div>
            @else
                <div class="text-xs text-slate-400 italic">
                    Perlihatkan QR Code ini ke Dosen sebelum waktu 15 menit berakhir.
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
@if(!$isExpired && $qr)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new QRCode(document.getElementById("full-qrcode-canvas"), {
            text: "{{ $qr->token }}",
            width: 208,
            height: 208,
            colorDark : "#0c1a40",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });

        let remainingSeconds = {{ $qr->remaining_seconds }};
        const timerEl = document.getElementById('full-countdown-timer');
        const boxEl = document.getElementById('qr-timer-box');
        const canvasEl = document.getElementById('full-qrcode-canvas');
        const expiredPlaceholder = document.getElementById('expired-placeholder');
        const actionContainer = document.getElementById('qr-action-container');
        const statusText = document.getElementById('status-text');
        const subtitle = document.getElementById('qr-subtitle');
        const timerDesc = document.getElementById('qr-timer-desc');
        const tokenBox = document.getElementById('token-box');

        const interval = setInterval(function() {
            if (remainingSeconds <= 0) {
                clearInterval(interval);
                // Ubah tampilan menjadi kedaluwarsa seketika
                if (canvasEl) canvasEl.classList.add('hidden');
                if (expiredPlaceholder) expiredPlaceholder.classList.remove('hidden');

                if (boxEl) {
                    boxEl.className = 'inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-bold shadow-xs';
                    boxEl.innerHTML = '<span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span> <span>Status: KEDALUWARSA</span>';
                }

                if (timerDesc) {
                    timerDesc.className = 'text-xs text-rose-600 font-medium mt-2';
                    timerDesc.innerText = 'Masa tenggang telah lewat. Sesuai aturan sistem, buat pengajuan peminjaman ulang.';
                }

                if (subtitle) {
                    subtitle.innerText = 'Masa tenggang 15 menit telah habis. Kode ini tidak berlaku lagi dan Anda harus mengajukan peminjaman ulang.';
                }

                if (statusText) {
                    statusText.className = 'font-bold text-rose-600';
                    statusText.innerText = 'KEDALUWARSA';
                }

                if (tokenBox) {
                    tokenBox.classList.add('opacity-40');
                    const copyBtn = document.getElementById('btn-copy-token');
                    if (copyBtn) copyBtn.remove();
                }

                // Tampilkan tombol Ajukan Peminjaman Ulang (bukan buat QR baru)
                if (actionContainer) {
                    actionContainer.innerHTML = `
                        <div class="space-y-3">
                            <a href="{{ route('mahasiswa.form-peminjaman', [$peminjaman->jenis_fasilitas, $peminjaman->id_barang ?? $peminjaman->id_ruangan]) }}"
                               class="w-full py-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-lg shadow-blue-600/30 transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                Ajukan Peminjaman Ulang
                            </a>
                            <a href="{{ route('mahasiswa.peminjaman-saya') }}"
                               class="block w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition-colors">
                                Kembali ke Daftar Transaksi
                            </a>
                        </div>
                    `;
                }
            } else {
                const m = Math.floor(remainingSeconds / 60);
                const s = remainingSeconds % 60;
                if (timerEl) {
                    timerEl.innerText = `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
                }
                remainingSeconds--;
            }
        }, 1000);
    });

    function copyToken() {
        const text = document.getElementById('token-text').innerText;
        navigator.clipboard.writeText(text).then(() => {
            alert('Token berhasil disalin ke clipboard!');
        });
    }
</script>
@endif
@endpush
@endsection
