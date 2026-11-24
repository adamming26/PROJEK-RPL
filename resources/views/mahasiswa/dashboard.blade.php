@extends('layouts.app')

@section('title', 'Beranda Mahasiswa')
@section('page_title', 'Beranda Mahasiswa')

@section('content')
<!-- Hero Welcome Banner (Matches Report Bab III) -->
<div class="rounded-3xl gradient-banner text-white p-6 sm:p-8 mb-8 shadow-xl">
    <div class="max-w-2xl">
        <span class="inline-block px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-semibold uppercase tracking-wider mb-2">
            Beranda Mahasiswa
        </span>
        <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight mb-2">Selamat Datang, {{ $user->nama }}</h2>
        <p class="text-sm text-slate-200">Cek ketersediaan fasilitas, ajukan peminjaman, dan pantau statusnya secara real-time.</p>
    </div>
</div>

<!-- 4 Statistik Ringkasan -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
    <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Peminjaman</span>
            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            </div>
        </div>
        <div class="text-2xl font-extrabold text-slate-900">{{ $totalPeminjaman }}</div>
    </div>

    <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-bold text-amber-600 uppercase tracking-wider">Menunggu Dosen</span>
            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
        <div class="text-2xl font-extrabold text-slate-900">{{ $menungguPersetujuan }}</div>
    </div>

    <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Peminjaman Aktif</span>
            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
        </div>
        <div class="text-2xl font-extrabold text-slate-900">{{ $peminjamanAktif }}</div>
    </div>

    <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Peminjaman Selesai</span>
            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
        <div class="text-2xl font-extrabold text-slate-900">{{ $peminjamanSelesai }}</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

    <!-- Kolom Kiri: Tabel Ketersediaan Fasilitas (Matches Wireframe 3.8) -->
    <div class="lg:col-span-8 space-y-6">
        <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Ketersediaan fasilitas</h3>
                    <p class="text-xs text-slate-500">Periksa ketersediaan sebelum mengajukan peminjaman.</p>
                </div>

                <!-- Filter button pills -->
                <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-xl text-xs font-semibold">
                    <button type="button" onclick="filterFasilitas('semua')" id="tab-semua" class="tab-btn px-3 py-1.5 rounded-lg bg-blue-600 text-white shadow-xs">Semua</button>
                    <button type="button" onclick="filterFasilitas('barang')" id="tab-barang" class="tab-btn px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900">Barang</button>
                    <button type="button" onclick="filterFasilitas('lab')" id="tab-lab" class="tab-btn px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900">Lab</button>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto rounded-2xl border border-slate-100">
                <table class="w-full text-left text-xs min-w-[560px] border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 uppercase font-bold tracking-wider text-[11px]">
                            <th class="py-3 px-4 text-left">Fasilitas</th>
                            <th class="py-3 px-4 text-left">Jenis</th>
                            <th class="py-3 px-4 text-left">Ketersediaan</th>
                            <th class="py-3 px-4 text-left">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 bg-white">
                        @foreach($barangTersedia as $b)
                            <tr class="item-row item-barang hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-4 font-bold text-slate-900">{{ $b->nama_barang }}</td>
                                <td class="py-3.5 px-4 whitespace-nowrap"><span class="inline-flex px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-700 font-semibold text-xs">Barang</span></td>
                                <td class="py-3.5 px-4 font-medium whitespace-nowrap">{{ $b->stok_tersedia }} / {{ $b->stok_total }} unit</td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($b->stok_tersedia > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-xs">Tersedia</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200 font-bold text-xs">Habis</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    @if($b->stok_tersedia > 0)
                                        <a href="{{ route('mahasiswa.form-peminjaman', ['jenis' => 'BARANG', 'id' => $b->id_barang]) }}"
                                           class="px-3 py-1.5 rounded-xl border border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white font-bold transition-all text-xs">
                                            Ajukan Pinjam
                                        </a>
                                    @else
                                        <span class="text-slate-400 font-medium text-xs">Tidak tersedia</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                        @foreach($ruanganTersedia as $r)
                            <tr class="item-row item-lab hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-4 font-bold text-slate-900">{{ $r->nama_ruangan }}</td>
                                <td class="py-3.5 px-4 whitespace-nowrap"><span class="inline-flex px-2.5 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-100 font-semibold text-xs">Lab</span></td>
                                <td class="py-3.5 px-4 font-medium whitespace-nowrap">Kapasitas {{ $r->kapasitas }} orang</td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200 font-bold text-xs">{{ $r->status }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <a href="{{ route('mahasiswa.form-peminjaman', ['jenis' => 'RUANGAN', 'id' => $r->id_ruangan]) }}"
                                       class="px-3 py-1.5 rounded-xl border border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white font-bold transition-all text-xs">
                                        Ajukan Izin
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 text-right">
                <a href="{{ route('mahasiswa.fasilitas') }}" class="text-xs font-bold text-blue-600 hover:underline">Lihat Semua Fasilitas &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Status Pengajuan & QR Code Peminjaman (Matches Wireframe 3.8) -->
    <div class="lg:col-span-4 space-y-6">

        <!-- Card Status Pengajuan Terbaru -->
        <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">Status pengajuan</h3>

            @if($pengajuanTerbaru->isEmpty())
                <p class="text-xs text-slate-400 py-4 text-center">Belum ada pengajuan peminjaman.</p>
            @else
                <div class="space-y-4">
                    @foreach($pengajuanTerbaru as $p)
                        <div class="flex items-start justify-between gap-2 pb-3 border-b border-slate-100 last:border-0 last:pb-0">
                            <div>
                                <h4 class="text-xs font-bold text-slate-900">{{ $p->nama_fasilitas }}</h4>
                                <span class="text-[11px] text-slate-400">Diajukan {{ $p->tanggal_pengajuan->format('d/m/Y') }}</span>
                            </div>
                            <div>
                                @if($p->status === 'MENUNGGU PERSETUJUAN')
                                    <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold">Menunggu Dosen</span>
                                @elseif(in_array($p->status, ['DISETUJUI', 'MENUNGGU PENYERAHAN']))
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold">Disetujui</span>
                                @elseif($p->status === 'DIPINJAM')
                                    <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-bold">Dipinjam</span>
                                @elseif($p->status === 'DITOLAK')
                                    <span class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-bold">Ditolak</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-bold">Selesai</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Card QR Code Peminjaman / Pengembalian Aktif -->
        <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200 text-center">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 text-left">
                QR Code Aktif
            </h3>

            @if($qrAktif && $qrAktif->isValid())
                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 inline-block mb-3">
                    <div id="qrcode-canvas" class="flex justify-center"></div>
                </div>

                <div class="mb-3">
                    <span id="countdown-badge" class="inline-block px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold">
                        Berlaku <span id="countdown-timer">--:--</span> menit lagi
                    </span>
                    <p class="text-[11px] text-slate-500 mt-2">
                        Tunjukkan kode ini kepada Dosen saat
                        <strong>{{ $qrAktif->jenis_qr === 'PEMINJAMAN' ? 'penyerahan' : 'pengembalian' }}</strong>.
                    </p>
                    <p class="font-mono text-[10px] text-slate-400 mt-1">{{ $qrAktif->token }}</p>
                </div>

                @if($qrAktif->peminjaman->status === 'DIPINJAM')
                    <form method="POST" action="{{ route('mahasiswa.ajukan-pengembalian', $qrAktif->peminjaman->id_peminjaman) }}">
                        @csrf
                        <button type="submit" class="w-full py-2 rounded-xl border border-blue-600 text-blue-600 hover:bg-blue-50 font-bold text-xs transition-colors">
                            Ajukan pengembalian
                        </button>
                    </form>
                @else
                    <a href="{{ route('mahasiswa.qr-code', $qrAktif->peminjaman->id_peminjaman) }}"
                       class="inline-block w-full py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-colors">
                        Lihat Layar Penuh QR
                    </a>
                @endif

            @else
                <div class="py-6 text-center text-slate-400">
                    <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center mx-auto mb-2 text-slate-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    </div>
                    <p class="text-xs text-slate-500 font-medium">Tidak ada QR Code aktif</p>
                    <p class="text-[11px] text-slate-400 mt-1">QR Code otomatis terbit setelah pengajuan disetujui Dosen.</p>
                </div>
            @endif
        </div>

    </div>

</div>

@push('scripts')
<script>
    function filterFasilitas(tab) {
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('bg-blue-600', 'text-white', 'shadow-xs');
            btn.classList.add('text-slate-600');
        });
        const activeBtn = document.getElementById('tab-' + tab);
        activeBtn.classList.add('bg-blue-600', 'text-white', 'shadow-xs');
        activeBtn.classList.remove('text-slate-600');

        document.querySelectorAll('.item-row').forEach(row => {
            if (tab === 'semua') {
                row.style.display = '';
            } else if (tab === 'barang') {
                row.style.display = row.classList.contains('item-barang') ? '' : 'none';
            } else if (tab === 'lab') {
                row.style.display = row.classList.contains('item-lab') ? '' : 'none';
            }
        });
    }

    @if($qrAktif && $qrAktif->isValid())
    // Render QR Code
    document.addEventListener('DOMContentLoaded', function() {
        new QRCode(document.getElementById("qrcode-canvas"), {
            text: "{{ $qrAktif->token }}",
            width: 140,
            height: 140,
            colorDark : "#0c1a40",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });

        // Countdown timer 15 Menit
        let remainingSeconds = {{ $qrAktif->remaining_seconds }};
        const timerEl = document.getElementById('countdown-timer');
        const badgeEl = document.getElementById('countdown-badge');

        const interval = setInterval(function() {
            if (remainingSeconds <= 0) {
                clearInterval(interval);
                timerEl.innerText = '00:00';
                badgeEl.className = 'inline-block px-3 py-1 rounded-full bg-rose-100 text-rose-800 text-xs font-bold';
                badgeEl.innerText = 'KEDALUWARSA (Masa berlaku habis)';
                setTimeout(() => location.reload(), 2000);
            } else {
                const m = Math.floor(remainingSeconds / 60);
                const s = remainingSeconds % 60;
                timerEl.innerText = `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
                remainingSeconds--;
            }
        }, 1000);
    });
    @endif
</script>
@endpush
@endsection
