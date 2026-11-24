@extends('layouts.app')

@section('title', 'Riwayat Peminjaman')
@section('page_title', 'Riwayat Peminjaman')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Riwayat Peminjaman</h2>
    <p class="text-sm text-slate-500">Catatan seluruh peminjaman dan pengembalian Anda.</p>
</div>

<div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200">
    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">Semua riwayat</h3>

    <!-- Filter Form (Matches Bab III Wireframe) -->
    <form method="GET" action="{{ route('mahasiswa.riwayat') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 mb-6 text-xs">
        <div class="sm:col-span-4">
            <label class="block text-slate-500 mb-1 font-semibold">Cari</label>
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari data..."
                   class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
        </div>
        <div class="sm:col-span-3">
            <label class="block text-slate-500 mb-1 font-semibold">Jenis</label>
            <select name="jenis" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none bg-white">
                <option value="">Semua</option>
                <option value="BARANG" {{ $jenis === 'BARANG' ? 'selected' : '' }}>Barang</option>
                <option value="RUANGAN" {{ $jenis === 'RUANGAN' ? 'selected' : '' }}>Lab</option>
            </select>
        </div>
        <div class="sm:col-span-3">
            <label class="block text-slate-500 mb-1 font-semibold">Status</label>
            <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none bg-white">
                <option value="">Semua</option>
                <option value="SELESAI" {{ $status === 'SELESAI' ? 'selected' : '' }}>Selesai / Dikembalikan</option>
                <option value="DITOLAK" {{ $status === 'DITOLAK' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>
        <div class="sm:col-span-2">
            <label class="block text-slate-500 mb-1 font-semibold">Dari tanggal</label>
            <input type="date" name="dari_tanggal" value="{{ $dariTanggal }}"
                   class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
        </div>
        <div class="sm:col-span-12 flex justify-end gap-2 pt-2">
            <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs transition-colors">
                Terapkan Filter
            </button>
            <a href="{{ route('mahasiswa.riwayat') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold">
                Reset
            </a>
        </div>
    </form>

    <!-- Table (Matches Bab III Wireframe 3.8) -->
    <div class="overflow-x-auto rounded-2xl border border-slate-100">
        <table class="w-full text-left text-xs min-w-[760px] border-collapse">
            <thead>
                <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 uppercase font-bold tracking-wider text-[11px]">
                    <th class="py-3.5 px-4 text-left">Tanggal</th>
                    <th class="py-3.5 px-4 text-left">Fasilitas</th>
                    <th class="py-3.5 px-4 text-left">Jenis</th>
                    <th class="py-3.5 px-4 text-left">Diputuskan Oleh</th>
                    <th class="py-3.5 px-4 text-left">Waktu Kembali</th>
                    <th class="py-3.5 px-4 text-right">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700 bg-white">
                @forelse($riwayatList as $r)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-4 px-4 text-slate-600 font-medium whitespace-nowrap">
                            {{ $r->tanggal_peminjaman->format('d/m/Y') }}
                        </td>
                        <td class="py-4 px-4">
                            <div class="font-bold text-slate-900 text-sm leading-tight">{{ $r->nama_fasilitas }}</div>
                            <span class="inline-block text-[10px] text-slate-400 font-mono mt-0.5">{{ $r->kode_peminjaman }} • {{ $r->jumlah }} unit</span>
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $r->jenis_fasilitas === 'BARANG' ? 'bg-slate-100 text-slate-700' : 'bg-blue-50 text-blue-700 border border-blue-100' }}">
                                {{ $r->jenis_fasilitas === 'BARANG' ? 'Barang' : 'Lab' }}
                            </span>
                        </td>
                        <td class="py-4 px-4 text-slate-700 font-medium">
                            {{ optional($r->persetujuanTerakhir)->dosen->nama ?? '-' }}
                        </td>
                        <td class="py-4 px-4 text-slate-600 whitespace-nowrap">
                            @if($r->pengembalian)
                                <div class="font-semibold text-slate-800">{{ $r->pengembalian->waktu_kembali->format('d/m/Y') }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $r->pengembalian->waktu_kembali->format('H:i') }} WITA</div>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="py-4 px-4 text-right whitespace-nowrap">
                            @if($r->status === 'SELESAI')
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold leading-none">
                                    {{ $r->jenis_fasilitas === 'BARANG' ? 'Dikembalikan' : 'Selesai' }}
                                </span>
                            @elseif($r->status === 'DITOLAK')
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200 text-xs font-bold leading-none">
                                    Ditolak
                                </span>
                            @elseif($r->status === 'KEDALUWARSA')
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200 text-xs font-bold leading-none">
                                    Kedaluwarsa
                                </span>
                            @elseif($r->status === 'MENUNGGU PERSETUJUAN')
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold leading-none">
                                    Menunggu Persetujuan
                                </span>
                            @elseif(in_array($r->status, ['DISETUJUI', 'MENUNGGU PENYERAHAN']))
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200 text-xs font-bold leading-none">
                                    Disetujui
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold leading-none">
                                    {{ $r->status }}
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400">
                            Belum ada riwayat transaksi peminjaman.
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
