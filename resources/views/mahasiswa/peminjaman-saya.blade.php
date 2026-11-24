@extends('layouts.app')

@section('title', 'Peminjaman Saya')
@section('page_title', 'Peminjaman Saya')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Peminjaman Saya</h2>
    <p class="text-sm text-slate-500">Pantau pengajuan, tampilkan QR Code, dan ajukan pengembalian fasilitas.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

    <!-- Kolom Kiri: Tabel Daftar Peminjaman (Matches Bab III Wireframe 3.8) -->
    <div class="lg:col-span-8 space-y-6">
        <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200">
            <!-- Header Filter Tabs & Search -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <!-- Filter Tabs -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
                    <a href="{{ route('mahasiswa.peminjaman-saya', ['status' => 'semua', 'search' => request('search')]) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $status === 'semua' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Semua
                    </a>
                    <a href="{{ route('mahasiswa.peminjaman-saya', ['status' => 'menunggu', 'search' => request('search')]) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $status === 'menunggu' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Menunggu
                    </a>
                    <a href="{{ route('mahasiswa.peminjaman-saya', ['status' => 'disetujui', 'search' => request('search')]) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $status === 'disetujui' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Disetujui
                    </a>
                    <a href="{{ route('mahasiswa.peminjaman-saya', ['status' => 'dipinjam', 'search' => request('search')]) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $status === 'dipinjam' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Dipinjam
                    </a>
                    <a href="{{ route('mahasiswa.peminjaman-saya', ['status' => 'ditolak', 'search' => request('search')]) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $status === 'ditolak' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Ditolak
                    </a>
                </div>

                <!-- Search -->
                <form method="GET" action="{{ route('mahasiswa.peminjaman-saya') }}" class="w-full sm:w-60">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <div class="relative">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari data..."
                               class="w-full pl-9 pr-3 py-1.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                        <svg class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto rounded-2xl border border-slate-100">
                <table class="w-full text-left text-xs min-w-[680px] border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 uppercase font-bold tracking-wider text-[11px]">
                            <th class="py-3.5 px-4 text-left">Fasilitas</th>
                            <th class="py-3.5 px-4 text-left">Jenis</th>
                            <th class="py-3.5 px-4 text-left">Jadwal Pinjam</th>
                            <th class="py-3.5 px-4 text-left">Status</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 bg-white">
                        @forelse($peminjamanList as $p)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-4 px-4">
                                    <div class="font-bold text-slate-900 text-sm leading-tight">{{ $p->nama_fasilitas }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $p->kode_peminjaman }} • {{ $p->jumlah }} unit</div>
                                </td>
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $p->jenis_fasilitas === 'BARANG' ? 'bg-slate-100 text-slate-700' : 'bg-blue-50 text-blue-700 border border-blue-100' }}">
                                        {{ $p->jenis_fasilitas === 'BARANG' ? 'Barang' : 'Lab' }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <div class="text-slate-800 font-medium">{{ $p->tanggal_peminjaman->format('d/m/Y') }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">{{ substr($p->jam_mulai, 0, 5) }} - {{ substr($p->jam_selesai, 0, 5) }}</div>
                                </td>
                                <td class="py-4 px-4 whitespace-nowrap">
                                    @if($p->status === 'MENUNGGU PERSETUJUAN')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold leading-none">Menunggu Dosen</span>
                                    @elseif(in_array($p->status, ['DISETUJUI', 'MENUNGGU PENYERAHAN']))
                                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold leading-none">Disetujui</span>
                                    @elseif($p->status === 'DIPINJAM')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200 text-xs font-bold leading-none">Dipinjam</span>
                                    @elseif($p->status === 'MENUNGGU PENGEMBALIAN')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 text-xs font-bold leading-none">Menunggu Kembali</span>
                                    @elseif($p->status === 'KEDALUWARSA')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200 text-xs font-bold leading-none">Kedaluwarsa</span>
                                    @elseif($p->status === 'DITOLAK')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200 text-xs font-bold leading-none">Ditolak</span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold leading-none">Selesai</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-right whitespace-nowrap space-x-1">
                                    @if($p->status === 'MENUNGGU PERSETUJUAN')
                                        <span class="text-xs text-slate-400 font-medium">Menunggu keputusan</span>
                                    @elseif(in_array($p->status, ['DISETUJUI', 'MENUNGGU PENYERAHAN', 'MENUNGGU PENGEMBALIAN']))
                                        <a href="{{ route('mahasiswa.qr-code', $p->id_peminjaman) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                            Tampilkan QR
                                        </a>
                                    @elseif($p->status === 'DIPINJAM')
                                        <form method="POST" action="{{ route('mahasiswa.ajukan-pengembalian', $p->id_peminjaman) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 rounded-xl border border-blue-600 text-blue-600 hover:bg-blue-50 font-bold text-xs transition-all">
                                                Ajukan Pengembalian
                                            </button>
                                        </form>
                                    @elseif($p->status === 'KEDALUWARSA')
                                        <a href="{{ route('mahasiswa.form-peminjaman', [$p->jenis_fasilitas, $p->id_barang ?? $p->id_ruangan]) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-all">
                                            Ajukan Ulang
                                        </a>
                                    @elseif($p->status === 'DITOLAK')
                                        <a href="{{ route('mahasiswa.detail-peminjaman', $p->id_peminjaman) }}"
                                           class="px-2.5 py-1 text-rose-600 hover:underline font-bold text-xs">
                                            Lihat Catatan
                                        </a>
                                    @else
                                        <a href="{{ route('mahasiswa.detail-peminjaman', $p->id_peminjaman) }}" class="text-slate-500 hover:text-slate-800 font-medium">Detail</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">
                                    Tidak ada data peminjaman yang cocok.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $peminjamanList->links() }}
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Card QR Code Peminjaman & Arti Status (Matches Bab III Wireframe 3.8) -->
    <div class="lg:col-span-4 space-y-6">

        <!-- Card QR Code Peminjaman -->
        <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200 text-center">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 text-left">
                QR Code peminjaman
            </h3>

            @if($qrAktif && $qrAktif->isValid())
                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 inline-block mb-3">
                    <div id="qrcode-saya-canvas" class="flex justify-center"></div>
                </div>

                <div class="mb-3">
                    <span id="countdown-saya-badge" class="inline-block px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold">
                        Berlaku <span id="countdown-saya-timer">--:--</span> menit lagi
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
                        <button type="submit" class="w-full py-2.5 rounded-xl border border-blue-600 text-blue-600 hover:bg-blue-50 font-bold text-xs transition-colors">
                            Ajukan pengembalian
                        </button>
                    </form>
                @else
                    <a href="{{ route('mahasiswa.qr-code', $qrAktif->peminjaman->id_peminjaman) }}"
                       class="inline-block w-full py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-colors">
                        Lihat Layar Penuh QR
                    </a>
                @endif
            @else
                <div class="py-6 text-center text-slate-400">
                    <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center mx-auto mb-2 text-slate-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    </div>
                    <p class="text-xs text-slate-500 font-medium">Tidak ada QR aktif</p>
                    <p class="text-[11px] text-slate-400 mt-1">QR Code akan muncul di sini setelah pengajuan disetujui Dosen.</p>
                </div>
            @endif
        </div>

        <!-- Card Arti Status (Matches Bab III Wireframe 3.8) -->
        <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">
                Arti status
            </h3>

            <dl class="space-y-3 text-xs">
                <div class="flex items-center justify-between">
                    <dt><span class="px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200 font-bold">Menunggu</span></dt>
                    <dd class="text-slate-500 text-right">Sedang diperiksa Dosen</dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt><span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold">Disetujui</span></dt>
                    <dd class="text-slate-500 text-right">QR Code diterbitkan</dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt><span class="px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200 font-bold">Dipinjam</span></dt>
                    <dd class="text-slate-500 text-right">Sudah diserahkan</dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt><span class="px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200 font-bold">Ditolak</span></dt>
                    <dd class="text-slate-500 text-right">Lihat catatan Dosen</dd>
                </div>
            </dl>
        </div>

    </div>

</div>

@push('scripts')
@if($qrAktif && $qrAktif->isValid())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new QRCode(document.getElementById("qrcode-saya-canvas"), {
            text: "{{ $qrAktif->token }}",
            width: 140,
            height: 140,
            colorDark : "#0c1a40",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });

        let remainingSeconds = {{ $qrAktif->remaining_seconds }};
        const timerEl = document.getElementById('countdown-saya-timer');
        const badgeEl = document.getElementById('countdown-saya-badge');

        const interval = setInterval(function() {
            if (remainingSeconds <= 0) {
                clearInterval(interval);
                timerEl.innerText = '00:00';
                badgeEl.className = 'inline-block px-3 py-1 rounded-full bg-rose-100 text-rose-800 text-xs font-bold';
                badgeEl.innerText = 'KEDALUWARSA';
                setTimeout(() => location.reload(), 2000);
            } else {
                const m = Math.floor(remainingSeconds / 60);
                const s = remainingSeconds % 60;
                timerEl.innerText = `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
                remainingSeconds--;
            }
        }, 1000);
    });
</script>
@endif
@endpush
@endsection
