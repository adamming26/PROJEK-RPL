@extends('layouts.app')

@section('title', 'Riwayat Transaksi')
@section('page_title', 'Riwayat Transaksi')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Riwayat Transaksi</h2>
    <p class="text-sm text-slate-500">Arsip transaksi untuk mengetahui penggunaan fasilitas barang dan lab.</p>
</div>

<div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200">
    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">Semua riwayat</h3>

    <!-- Filter Bar (Matches Bab III Wireframe 3.10) -->
    <form method="GET" action="{{ route('admin.riwayat-transaksi') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 mb-6 text-xs">
        <div class="sm:col-span-2">
            <label class="block text-slate-500 mb-1 font-semibold">Dari tanggal</label>
            <input type="date" name="dari_tanggal" value="{{ $dariTanggal }}"
                   class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
        </div>
        <div class="sm:col-span-2">
            <label class="block text-slate-500 mb-1 font-semibold">Sampai tanggal</label>
            <input type="date" name="sampai_tanggal" value="{{ $sampaiTanggal }}"
                   class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
        </div>
        <div class="sm:col-span-2">
            <label class="block text-slate-500 mb-1 font-semibold">Jenis</label>
            <select name="jenis" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none bg-white">
                <option value="">Semua</option>
                <option value="BARANG" {{ $jenis === 'BARANG' ? 'selected' : '' }}>Barang</option>
                <option value="RUANGAN" {{ $jenis === 'RUANGAN' ? 'selected' : '' }}>Lab</option>
            </select>
        </div>
        <div class="sm:col-span-2">
            <label class="block text-slate-500 mb-1 font-semibold">Status</label>
            <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none bg-white">
                <option value="">Semua</option>
                <option value="SELESAI" {{ $status === 'SELESAI' ? 'selected' : '' }}>Selesai</option>
                <option value="DIPINJAM" {{ $status === 'DIPINJAM' ? 'selected' : '' }}>Dipinjam</option>
                <option value="DITOLAK" {{ $status === 'DITOLAK' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>
        <div class="sm:col-span-3">
            <label class="block text-slate-500 mb-1 font-semibold">Cari</label>
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari kode, peminjam..."
                   class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
        </div>
        <div class="sm:col-span-1 flex items-end">
            <button type="submit" class="w-full py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs transition-colors">
                Terapkan
            </button>
        </div>
    </form>

    <!-- Table (Matches Bab III Wireframe 3.10) -->
    <div class="overflow-x-auto rounded-2xl border border-slate-100">
        <table class="w-full text-left text-xs min-w-[880px] border-collapse">
            <thead>
                <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 uppercase font-bold tracking-wider text-[11px]">
                    <th class="py-3.5 px-4 text-left">Tanggal</th>
                    <th class="py-3.5 px-4 text-left">Kode</th>
                    <th class="py-3.5 px-4 text-left">Fasilitas</th>
                    <th class="py-3.5 px-4 text-left">Peminjam</th>
                    <th class="py-3.5 px-4 text-left">Jenis</th>
                    <th class="py-3.5 px-4 text-left">Disetujui Oleh</th>
                    <th class="py-3.5 px-4 text-left">Dikonfirmasi Oleh</th>
                    <th class="py-3.5 px-4 text-left">Kondisi Kembali</th>
                    <th class="py-3.5 px-4 text-right">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700 bg-white">
                @forelse($riwayatList as $item)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-4 px-4 text-slate-600 font-medium whitespace-nowrap">{{ $item->tanggal_peminjaman->format('d/m/Y') }}</td>
                        <td class="py-4 px-4 font-mono font-bold text-slate-800 whitespace-nowrap">{{ $item->kode_peminjaman }}</td>
                        <td class="py-4 px-4 font-bold text-slate-900">{{ $item->nama_fasilitas }}</td>
                        <td class="py-4 px-4 font-medium text-slate-800 whitespace-nowrap">{{ $item->user->nama ?? '-' }}</td>
                        <td class="py-4 px-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $item->jenis_fasilitas === 'BARANG' ? 'bg-slate-100 text-slate-700' : 'bg-blue-50 text-blue-700 border border-blue-100' }}">
                                {{ $item->jenis_fasilitas === 'BARANG' ? 'Barang' : 'Lab' }}
                            </span>
                        </td>
                        <td class="py-4 px-4 text-slate-600 whitespace-nowrap">{{ optional($item->persetujuanTerakhir)->dosen->nama ?? '-' }}</td>
                        <td class="py-4 px-4 text-slate-600 whitespace-nowrap">{{ optional($item->diserahkanOleh)->nama ?: (optional(optional($item->pengembalian)->dosen)->nama ?? '-') }}</td>
                        <td class="py-4 px-4 whitespace-nowrap">
                            @if($item->pengembalian)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold {{ $item->pengembalian->kondisi_barang === 'BAIK' ? 'text-emerald-700 bg-emerald-50' : 'text-amber-700 bg-amber-50' }}">
                                    {{ $item->pengembalian->kondisi_barang }}
                                </span>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="py-4 px-4 text-right whitespace-nowrap">
                            @if($item->status === 'SELESAI')
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold leading-none">Selesai</span>
                            @elseif($item->status === 'DITOLAK')
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200 text-xs font-bold leading-none">Ditolak</span>
                            @elseif($item->status === 'KEDALUWARSA')
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200 text-xs font-bold leading-none">Kedaluwarsa</span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200 text-xs font-bold leading-none">{{ $item->status }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center text-slate-400">
                            Tidak ada arsip riwayat transaksi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 pt-3 border-t border-slate-100">
        {{ $riwayatList->links() }}
    </div>
</div>
@endsection
