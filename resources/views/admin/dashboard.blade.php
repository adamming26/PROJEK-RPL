@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page_title', 'Dashboard Admin')

@section('content')
<!-- Hero Banner Admin (Matches Bab III Gambar 3.10) -->
<div class="rounded-3xl gradient-banner text-white p-6 sm:p-8 mb-8 shadow-xl">
    <div class="max-w-2xl">
        <span class="inline-block px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-semibold uppercase tracking-wider mb-2">
            Dashboard Admin
        </span>
        <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight mb-2">Selamat Datang, {{ Auth::user()->nama }}</h2>
        <p class="text-sm text-slate-200">Kelola data barang dan lab, serta pantau transaksi peminjaman fasilitas secara terpusat.</p>
    </div>
</div>

<!-- 6 Ringkasan Statistik Utama -->
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
    <div class="bg-white rounded-2xl p-4 shadow-xs border border-slate-200">
        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Total Barang</span>
        <div class="text-xl font-extrabold text-slate-900">{{ $totalBarang }} jenis</div>
    </div>
    <div class="bg-white rounded-2xl p-4 shadow-xs border border-slate-200">
        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Total Ruangan</span>
        <div class="text-xl font-extrabold text-slate-900">{{ $totalRuangan }} lab</div>
    </div>
    <div class="bg-white rounded-2xl p-4 shadow-xs border border-slate-200">
        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Total Stok</span>
        <div class="text-xl font-extrabold text-slate-900">{{ $totalStokFasilitas }} unit</div>
    </div>
    <div class="bg-white rounded-2xl p-4 shadow-xs border border-slate-200">
        <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block mb-1">Stok Tersedia</span>
        <div class="text-xl font-extrabold text-emerald-600">{{ $totalFasilitasTersedia }} unit</div>
    </div>
    <div class="bg-white rounded-2xl p-4 shadow-xs border border-slate-200">
        <span class="text-[10px] font-bold text-blue-600 uppercase tracking-wider block mb-1">Transaksi Aktif</span>
        <div class="text-xl font-extrabold text-blue-600">{{ $totalPeminjamanBerjalan }}</div>
    </div>
    <div class="bg-white rounded-2xl p-4 shadow-xs border border-slate-200">
        <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider block mb-1">Total Pengajuan</span>
        <div class="text-xl font-extrabold text-indigo-600">{{ $totalPengajuan }}</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

    <!-- Kolom Kiri: Kelola Fasilitas Cepat (Matches Bab III Wireframe 3.10) -->
    <div class="lg:col-span-7 space-y-6">
        <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Kelola fasilitas</h3>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.kelola-fasilitas', ['tab' => 'barang']) }}" class="px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs">
                        + Tambah barang
                    </a>
                    <a href="{{ route('admin.kelola-fasilitas', ['tab' => 'lab']) }}" class="px-3 py-1.5 rounded-xl border border-blue-600 text-blue-600 hover:bg-blue-50 font-bold text-xs">
                        + Tambah lab
                    </a>
                </div>
            </div>

            <!-- Tabel Fasilitas -->
            <div class="overflow-x-auto rounded-2xl border border-slate-100">
                <table class="w-full text-left text-xs min-w-[500px] border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 uppercase font-bold tracking-wider text-[11px]">
                            <th class="py-3 px-4 text-left">Nama Fasilitas</th>
                            <th class="py-3 px-4 text-left">Stok / Kapasitas</th>
                            <th class="py-3 px-4 text-left">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 bg-white">
                        @foreach(\App\Models\Barang::take(4)->get() as $b)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-4 font-bold text-slate-900">{{ $b->nama_barang }}</td>
                                <td class="py-3.5 px-4 font-medium whitespace-nowrap">{{ $b->stok_tersedia }} / {{ $b->stok_total }} unit</td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold leading-none">Aktif</span>
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.kelola-fasilitas', ['tab' => 'barang', 'search' => $b->nama_barang]) }}"
                                       class="px-2.5 py-1 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-600 font-bold text-xs transition-colors">
                                        Ubah
                                    </a>
                                </td>
                            </tr>
                        @endforeach

                        @foreach(\App\Models\Ruangan::take(3)->get() as $r)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-4 font-bold text-slate-900">{{ $r->nama_ruangan }}</td>
                                <td class="py-3.5 px-4 font-medium whitespace-nowrap">{{ $r->kapasitas }} orang</td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200 text-xs font-bold leading-none">{{ $r->status }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.kelola-fasilitas', ['tab' => 'lab', 'search' => $r->nama_ruangan]) }}"
                                       class="px-2.5 py-1 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-600 font-bold text-xs transition-colors">
                                        Ubah
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 text-right">
                <a href="{{ route('admin.kelola-fasilitas') }}" class="text-xs font-bold text-blue-600 hover:underline">Kelola Seluruh Fasilitas &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Transaksi Berjalan (Matches Bab III Wireframe 3.10) -->
    <div class="lg:col-span-5 space-y-6">
        <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Transaksi berjalan</h3>
                <a href="{{ route('admin.transaksi-berjalan') }}" class="text-xs font-bold text-blue-600 hover:underline">Pantau &rarr;</a>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-100">
                <table class="w-full text-left text-xs min-w-[340px] border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 uppercase font-bold tracking-wider text-[11px]">
                            <th class="py-3 px-4 text-left">Peminjam</th>
                            <th class="py-3 px-4 text-left">Fasilitas</th>
                            <th class="py-3 px-4 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 bg-white">
                        @forelse($transaksiTerbaru as $t)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900">{{ $t->user->nama ?? '-' }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $t->kode_peminjaman }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-slate-800">{{ $t->nama_fasilitas }}</td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    @if(in_array($t->status, ['DISETUJUI', 'MENUNGGU PENYERAHAN']))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold leading-none">Menunggu Serah</span>
                                    @elseif($t->status === 'DIPINJAM')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200 text-xs font-bold leading-none">Dipinjam</span>
                                    @elseif($t->status === 'MENUNGGU PENGEMBALIAN')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 text-xs font-bold leading-none">Menunggu Kembali</span>
                                    @elseif($t->status === 'SELESAI')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold leading-none">Selesai</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-xs font-bold leading-none">{{ $t->status }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-6 text-center text-slate-400">
                                    Tidak ada transaksi aktif.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
