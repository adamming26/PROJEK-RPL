@extends('layouts.app')

@section('title', 'Riwayat Keputusan')
@section('page_title', 'Riwayat Keputusan')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Riwayat Keputusan</h2>
    <p class="text-sm text-slate-500">Pengajuan yang sudah Anda setujui atau tolak, beserta status pemindaian QR Code.</p>
</div>

<div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <!-- Filter Tabs -->
        <div class="flex items-center gap-2">
            <a href="{{ route('dosen.riwayat-keputusan', ['keputusan' => 'semua', 'search' => request('search')]) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $keputusan === 'semua' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua
            </a>
            <a href="{{ route('dosen.riwayat-keputusan', ['keputusan' => 'DISETUJUI', 'search' => request('search')]) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $keputusan === 'DISETUJUI' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Disetujui
            </a>
            <a href="{{ route('dosen.riwayat-keputusan', ['keputusan' => 'DITOLAK', 'search' => request('search')]) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $keputusan === 'DITOLAK' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Ditolak
            </a>
        </div>

        <!-- Search -->
        <form method="GET" action="{{ route('dosen.riwayat-keputusan') }}" class="w-full sm:w-72">
            <input type="hidden" name="keputusan" value="{{ $keputusan }}">
            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari data..."
                       class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </form>
    </div>

    <!-- Table (Matches Bab III Wireframe 3.10) -->
    <div class="overflow-x-auto rounded-2xl border border-slate-100">
        <table class="w-full text-left text-xs min-w-[780px] border-collapse">
            <thead>
                <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 uppercase font-bold tracking-wider text-[11px]">
                    <th class="py-3.5 px-4 text-left">Tanggal</th>
                    <th class="py-3.5 px-4 text-left">Pemohon</th>
                    <th class="py-3.5 px-4 text-left">Fasilitas</th>
                    <th class="py-3.5 px-4 text-left">Jenis</th>
                    <th class="py-3.5 px-4 text-left">Keputusan</th>
                    <th class="py-3.5 px-4 text-left">Catatan</th>
                    <th class="py-3.5 px-4 text-right">Pemindaian</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700 bg-white">
                @forelse($riwayatList as $item)
                    @php $p = $item->peminjaman; @endphp
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-4 px-4 text-slate-600 font-medium whitespace-nowrap">
                            {{ $item->tanggal->format('d/m/Y') }}
                        </td>
                        <td class="py-4 px-4">
                            <div class="font-bold text-slate-900 text-sm leading-tight">{{ $p->user->nama ?? '-' }}</div>
                            <span class="inline-block text-[10px] text-slate-400 font-mono mt-0.5">{{ $p->kode_peminjaman }}</span>
                        </td>
                        <td class="py-4 px-4 font-semibold text-slate-800">{{ $p->nama_fasilitas }}</td>
                        <td class="py-4 px-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $p->jenis_fasilitas === 'BARANG' ? 'bg-slate-100 text-slate-700' : 'bg-blue-50 text-blue-700 border border-blue-100' }}">
                                {{ $p->jenis_fasilitas === 'BARANG' ? 'Barang' : 'Lab' }}
                            </span>
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap">
                            @if($item->keputusan === 'DISETUJUI')
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold leading-none">Disetujui</span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200 text-xs font-bold leading-none">Ditolak</span>
                            @endif
                        </td>
                        <td class="py-4 px-4 text-slate-600 max-w-xs truncate">
                            {{ $item->catatan ?: '-' }}
                        </td>
                        <td class="py-4 px-4 text-right whitespace-nowrap">
                            @if($p->status === 'SELESAI')
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold leading-none">
                                    Pengembalian Dipindai
                                </span>
                            @elseif(in_array($p->status, ['DIPINJAM', 'MENUNGGU PENGEMBALIAN']))
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200 text-xs font-bold leading-none">
                                    Penyerahan Dipindai
                                </span>
                            @elseif(in_array($p->status, ['DISETUJUI', 'MENUNGGU PENYERAHAN']))
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold leading-none">
                                    Belum Dipindai
                                </span>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            Belum ada riwayat keputusan.
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
