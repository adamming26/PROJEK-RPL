@extends('layouts.app')

@section('title', 'Transaksi Berjalan')
@section('page_title', 'Transaksi Berjalan')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Transaksi</h2>
    <p class="text-sm text-slate-500">Pantau peminjaman yang sedang berjalan dan tahapannya secara real-time.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

    <!-- Kolom Kiri: Tabel Transaksi Berjalan (Matches Bab III Wireframe 3.10) -->
    <div class="lg:col-span-8 space-y-6">
        <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200">
            <!-- Tabs & Search -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
                    <a href="{{ route('admin.transaksi-berjalan', ['status' => 'semua', 'search' => request('search')]) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $status === 'semua' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Semua
                    </a>
                    <a href="{{ route('admin.transaksi-berjalan', ['status' => 'menunggu_serah', 'search' => request('search')]) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $status === 'menunggu_serah' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Menunggu serah
                    </a>
                    <a href="{{ route('admin.transaksi-berjalan', ['status' => 'dipinjam', 'search' => request('search')]) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $status === 'dipinjam' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Dipinjam
                    </a>
                    <a href="{{ route('admin.transaksi-berjalan', ['status' => 'menunggu_pengembalian', 'search' => request('search')]) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $status === 'menunggu_pengembalian' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Menunggu pengembalian
                    </a>
                </div>

                <form method="GET" action="{{ route('admin.transaksi-berjalan') }}" class="w-full sm:w-64">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <div class="relative">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari data..."
                               class="w-full pl-9 pr-3 py-1.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                        <svg class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </form>
            </div>

            <!-- Table (Matches Bab III Wireframe 3.10) -->
            <div class="overflow-x-auto rounded-2xl border border-slate-100">
                <table class="w-full text-left text-xs min-w-[720px] border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 uppercase font-bold tracking-wider text-[11px]">
                            <th class="py-3.5 px-4 text-left">ID / Kode</th>
                            <th class="py-3.5 px-4 text-left">Peminjam</th>
                            <th class="py-3.5 px-4 text-left">Fasilitas</th>
                            <th class="py-3.5 px-4 text-left">Jenis</th>
                            <th class="py-3.5 px-4 text-left">Waktu</th>
                            <th class="py-3.5 px-4 text-left">Status</th>
                            <th class="py-3.5 px-4 text-right">Dikonfirmasi Oleh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 bg-white">
                        @forelse($transaksiList as $t)
                            <tr class="hover:bg-blue-50/50 transition-colors cursor-pointer {{ (optional($selectedTransaksi)->id_peminjaman === $t->id_peminjaman) ? 'bg-blue-50/80 font-semibold' : '' }}"
                                onclick="window.location='{{ route('admin.transaksi-berjalan', ['selected_id' => $t->id_peminjaman, 'status' => $status, 'search' => $search]) }}'">
                                <td class="py-4 px-4 font-mono font-bold text-slate-800 whitespace-nowrap">{{ $t->kode_peminjaman }}</td>
                                <td class="py-4 px-4 font-bold text-slate-900">{{ $t->user->nama ?? '-' }}</td>
                                <td class="py-4 px-4 text-slate-800 font-semibold">{{ $t->nama_fasilitas }}</td>
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $t->jenis_fasilitas === 'BARANG' ? 'bg-slate-100 text-slate-700' : 'bg-blue-50 text-blue-700 border border-blue-100' }}">
                                        {{ $t->jenis_fasilitas === 'BARANG' ? 'Barang' : 'Lab' }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-slate-600 whitespace-nowrap">
                                    {{ $t->tanggal_peminjaman->format('d/m/Y') }}, {{ substr($t->jam_mulai, 0, 5) }}
                                </td>
                                <td class="py-4 px-4 whitespace-nowrap">
                                    @if(in_array($t->status, ['DISETUJUI', 'MENUNGGU PENYERAHAN']))
                                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold leading-none">Menunggu Serah</span>
                                    @elseif($t->status === 'DIPINJAM')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200 text-xs font-bold leading-none">Dipinjam</span>
                                    @elseif($t->status === 'MENUNGGU PENGEMBALIAN')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 text-xs font-bold leading-none">Menunggu Kembali</span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold leading-none">{{ $t->status }}</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-right text-slate-600 font-medium whitespace-nowrap">
                                    {{ optional($t->diserahkanOleh)->nama ?: (optional($t->persetujuanTerakhir)->dosen->nama ?: '-') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    Tidak ada transaksi aktif yang berjalan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Rincian 6 Tahapan Transaksi (Matches Bab III Wireframe 3.10) -->
    <div class="lg:col-span-4 space-y-6">
        @if($selectedTransaksi)
            <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200">
                <h3 class="text-base font-bold text-slate-900 pb-3 mb-4 border-b border-slate-100 flex items-center justify-between">
                    <span>Rincian [{{ $selectedTransaksi->kode_peminjaman }}]</span>
                    <span class="text-xs font-normal text-slate-400">{{ $selectedTransaksi->nama_fasilitas }}</span>
                </h3>

                <!-- 6 Sequential Lifecycle Steps (Matches Report Exactly) -->
                @php
                    $isSubmitted = true;
                    $persetujuan = $selectedTransaksi->persetujuanTerakhir;
                    $isApproved = in_array($selectedTransaksi->status, ['DISETUJUI', 'MENUNGGU PENYERAHAN', 'DIPINJAM', 'MENUNGGU PENGEMBALIAN', 'SELESAI']);
                    $qrPeminjaman = $selectedTransaksi->qrCodes->where('jenis_qr', 'PEMINJAMAN')->first();
                    $hasQrPeminjaman = (bool) $qrPeminjaman;
                    $isDiserahkan = in_array($selectedTransaksi->status, ['DIPINJAM', 'MENUNGGU PENGEMBALIAN', 'SELESAI']);
                    $qrPengembalian = $selectedTransaksi->qrCodes->where('jenis_qr', 'PENGEMBALIAN')->first();
                    $isKembaliDiajukan = in_array($selectedTransaksi->status, ['MENUNGGU PENGEMBALIAN', 'SELESAI']) || (bool) $qrPengembalian;
                    $isSelesai = $selectedTransaksi->status === 'SELESAI';
                @endphp

                <ol class="space-y-4 text-xs">
                    <!-- Step 1 -->
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-emerald-500 text-white font-bold flex items-center justify-center flex-shrink-0 text-xs">1</span>
                        <div>
                            <span class="font-bold text-slate-900 block">Diajukan Mahasiswa</span>
                            <span class="text-slate-400 text-[11px]">{{ $selectedTransaksi->tanggal_pengajuan->format('d/m/Y') }}, {{ $selectedTransaksi->created_at->format('H:i') }}</span>
                            <span class="text-[11px] text-slate-500 block">Oleh: {{ $selectedTransaksi->user->nama }}</span>
                        </div>
                    </li>

                    <!-- Step 2 -->
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full {{ $isApproved ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-500' }} font-bold flex items-center justify-center flex-shrink-0 text-xs">2</span>
                        <div>
                            <span class="font-bold {{ $isApproved ? 'text-slate-900' : 'text-slate-400' }} block">Disetujui Dosen</span>
                            @if($persetujuan)
                                <span class="text-slate-400 text-[11px]">{{ $persetujuan->tanggal->format('d/m/Y, H:i') }}</span>
                                <span class="text-[11px] text-slate-500 block">Oleh: {{ $persetujuan->dosen->nama ?? '-' }}</span>
                            @else
                                <span class="text-[11px] text-slate-400 italic">Menunggu keputusan...</span>
                            @endif
                        </div>
                    </li>

                    <!-- Step 3 -->
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full {{ $hasQrPeminjaman ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-500' }} font-bold flex items-center justify-center flex-shrink-0 text-xs">3</span>
                        <div>
                            <span class="font-bold {{ $hasQrPeminjaman ? 'text-slate-900' : 'text-slate-400' }} block">QR peminjaman terbit</span>
                            @if($qrPeminjaman)
                                <span class="text-slate-400 text-[11px]">{{ $qrPeminjaman->created_at->format('d/m/Y, H:i') }}</span>
                                <span class="text-[11px] text-slate-500 block font-mono">Token: {{ $qrPeminjaman->token }}</span>
                            @else
                                <span class="text-[11px] text-slate-400 italic">Belum diterbitkan</span>
                            @endif
                        </div>
                    </li>

                    <!-- Step 4 -->
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full {{ $isDiserahkan ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-500' }} font-bold flex items-center justify-center flex-shrink-0 text-xs">4</span>
                        <div>
                            <span class="font-bold {{ $isDiserahkan ? 'text-slate-900' : 'text-slate-400' }} block">Diserahkan, QR dipindai Dosen</span>
                            @if($selectedTransaksi->diserahkan_pada)
                                <span class="text-slate-400 text-[11px]">{{ $selectedTransaksi->diserahkan_pada->format('d/m/Y, H:i') }}</span>
                                <span class="text-[11px] text-slate-500 block">Oleh: {{ optional($selectedTransaksi->diserahkanOleh)->nama ?: '-' }}</span>
                            @else
                                <span class="text-[11px] text-slate-400 italic">Menunggu penyerahan...</span>
                            @endif
                        </div>
                    </li>

                    <!-- Step 5 -->
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full {{ $isKembaliDiajukan ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-500' }} font-bold flex items-center justify-center flex-shrink-0 text-xs">5</span>
                        <div>
                            <span class="font-bold {{ $isKembaliDiajukan ? 'text-slate-900' : 'text-slate-400' }} block">Pengembalian diajukan</span>
                            @if($qrPengembalian)
                                <span class="text-slate-400 text-[11px]">{{ $qrPengembalian->created_at->format('d/m/Y, H:i') }}</span>
                                <span class="text-[11px] text-slate-500 block font-mono">QR Pengembalian aktif</span>
                            @else
                                <span class="text-[11px] text-slate-400 italic">Belum diajukan mahasiswa</span>
                            @endif
                        </div>
                    </li>

                    <!-- Step 6 -->
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full {{ $isSelesai ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-500' }} font-bold flex items-center justify-center flex-shrink-0 text-xs">6</span>
                        <div>
                            <span class="font-bold {{ $isSelesai ? 'text-slate-900' : 'text-slate-400' }} block">Dikembalikan, QR dipindai Dosen</span>
                            @if($selectedTransaksi->pengembalian)
                                <span class="text-slate-400 text-[11px]">{{ $selectedTransaksi->pengembalian->waktu_kembali->format('d/m/Y, H:i') }}</span>
                                <span class="text-[11px] text-emerald-700 block font-bold">Kondisi: {{ $selectedTransaksi->pengembalian->kondisi_barang }}</span>
                            @else
                                <span class="text-[11px] text-slate-400 italic">Belum dikembalikan</span>
                            @endif
                        </div>
                    </li>
                </ol>
            </div>
        @else
            <div class="bg-white rounded-3xl p-8 text-center text-slate-400 border border-slate-200">
                <p class="text-xs">Pilih salah satu transaksi di tabel untuk melihat tahapan rinciannya.</p>
            </div>
        @endif
    </div>

</div>
@endsection
