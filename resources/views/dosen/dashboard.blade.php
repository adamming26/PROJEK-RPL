@extends('layouts.app')

@section('title', 'Dashboard Dosen')
@section('page_title', 'Dashboard Dosen')

@section('content')
<!-- Hero Banner Dosen (Matches Bab III Gambar 3.9) -->
<div class="rounded-3xl gradient-banner text-white p-6 sm:p-8 mb-8 shadow-xl">
    <div class="max-w-2xl">
        <span class="inline-block px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-semibold uppercase tracking-wider mb-2">
            Dashboard Dosen
        </span>
        <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight mb-2">Selamat Datang, {{ $user->nama }}</h2>
        <p class="text-sm text-slate-200">Periksa pengajuan, beri keputusan, dan pindai QR Code saat penyerahan dan pengembalian.</p>
    </div>
</div>

<!-- 4 Summary Stats -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
    <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200">
        <div class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-1">Menunggu Keputusan</div>
        <div class="text-2xl font-extrabold text-slate-900">{{ $menungguKeputusan }}</div>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200">
        <div class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-1">Menunggu Penyerahan</div>
        <div class="text-2xl font-extrabold text-slate-900">{{ $menungguPenyerahan }}</div>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200">
        <div class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-1">Menunggu Pengembalian</div>
        <div class="text-2xl font-extrabold text-slate-900">{{ $menungguPengembalian }}</div>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200">
        <div class="text-xs font-bold text-emerald-600 uppercase tracking-wider mb-1">Total Disetujui Anda</div>
        <div class="text-2xl font-extrabold text-slate-900">{{ $disetujui }}</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

    <!-- Kolom Kiri: Menunggu Keputusan & Detail Pengajuan (Matches Bab III Wireframe 3.9) -->
    <div class="lg:col-span-7 space-y-6">

        <!-- Card Menunggu Keputusan -->
        <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Menunggu keputusan</h3>
                <a href="{{ route('dosen.pengajuan-masuk') }}" class="text-xs font-bold text-blue-600 hover:underline">Lihat Semua &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 uppercase font-bold tracking-wider">
                            <th class="pb-3 pl-1">Pemohon</th>
                            <th class="pb-3">Fasilitas</th>
                            <th class="pb-3">Waktu</th>
                            <th class="pb-3 text-right pr-1">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($pengajuanMenunggu as $item)
                            <tr class="hover:bg-slate-50/70 transition-colors cursor-pointer" onclick="window.location='{{ route('dosen.detail-pengajuan', $item->id_peminjaman) }}'">
                                <td class="py-3 pl-1">
                                    <div class="font-bold text-slate-900">{{ $item->user->nama }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $item->user->nomor_induk }}</div>
                                </td>
                                <td class="py-3 font-semibold text-slate-800">{{ $item->nama_fasilitas }}</td>
                                <td class="py-3 text-slate-500 whitespace-nowrap">
                                    {{ $item->tanggal_peminjaman->format('d/m') }}, {{ substr($item->jam_mulai, 0, 5) }}
                                </td>
                                <td class="py-3 text-right pr-1">
                                    <span class="px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold">Menunggu</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-slate-400">
                                    Tidak ada pengajuan yang menunggu keputusan saat ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Card Detail Pengajuan Terkini / Cepat -->
        @if($pengajuanMenunggu->isNotEmpty())
            @php $firstItem = $pengajuanMenunggu->first(); @endphp
            <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200">
                <h3 class="text-base font-bold text-slate-900 pb-3 mb-4 border-b border-slate-100 flex items-center justify-between">
                    <span>Detail pengajuan terkini</span>
                    <span class="text-xs font-mono text-slate-400">{{ $firstItem->kode_peminjaman }}</span>
                </h3>

                <!-- Status Stok / Jadwal Badge -->
                <div class="p-3.5 rounded-2xl bg-blue-50/70 border border-blue-200/60 mb-4">
                    <span class="text-xs font-bold text-blue-900 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        {{ $firstItem->jenis_fasilitas === 'BARANG' ? 'Stok mencukupi untuk dipinjam' : 'Pemeriksaan bentrok jadwal otomatis lolos' }}
                    </span>
                </div>

                <dl class="space-y-2 text-xs mb-4">
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Pemohon:</dt>
                        <dd class="font-bold text-slate-900">{{ $firstItem->user->nama }} ({{ $firstItem->user->nomor_induk }})</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Fasilitas:</dt>
                        <dd class="font-bold text-slate-900">{{ $firstItem->nama_fasilitas }} ({{ $firstItem->jumlah }} unit)</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Waktu:</dt>
                        <dd class="font-semibold text-slate-800">{{ $firstItem->tanggal_peminjaman->format('d/m/Y') }}, {{ substr($firstItem->jam_mulai, 0, 5) }} - {{ substr($firstItem->jam_selesai, 0, 5) }} ({{ $firstItem->durasi }})</dd>
                    </div>
                    <div class="pt-2 border-t border-slate-100">
                        <dt class="text-slate-400 mb-1">Tujuan:</dt>
                        <dd class="p-2.5 rounded-xl bg-slate-50 text-slate-700 leading-relaxed">{{ $firstItem->tujuan }}</dd>
                    </div>
                </dl>

                <form method="POST" action="{{ route('dosen.putuskan-pengajuan', $firstItem->id_peminjaman) }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Catatan (opsional / wajib jika tolak)</label>
                        <input type="text" name="catatan" placeholder="Tulis alasan keputusan..."
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="submit" name="keputusan" value="DISETUJUI"
                                class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition-all">
                            Setujui
                        </button>
                        <button type="submit" name="keputusan" value="DITOLAK"
                                class="px-5 py-2.5 rounded-xl border border-rose-300 text-rose-600 hover:bg-rose-50 font-bold text-xs transition-all">
                            Tolak
                        </button>
                    </div>
                </form>
            </div>
        @endif

    </div>

    <!-- Kolom Kanan: Akses Pemindai QR Code Cepat (Matches Bab III Wireframe 3.9) -->
    <div class="lg:col-span-5 space-y-6">

        <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Pindai QR Code</h3>
                <a href="{{ route('dosen.pindai-qr') }}" class="text-xs font-bold text-blue-600 hover:underline">Buka Scanner Lengkap &rarr;</a>
            </div>

            <!-- Tab Penyerahan / Pengembalian -->
            <div class="flex items-center gap-2 mb-4">
                <a href="{{ route('dosen.pindai-qr', ['tab' => 'penyerahan']) }}" class="flex-1 py-2 text-center rounded-xl bg-blue-600 text-white font-bold text-xs shadow-xs">
                    Penyerahan
                </a>
                <a href="{{ route('dosen.pindai-qr', ['tab' => 'pengembalian']) }}" class="flex-1 py-2 text-center rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
                    Pengembalian
                </a>
            </div>

            <!-- Quick Action Box -->
            <div class="p-8 rounded-2xl border-2 border-dashed border-blue-200 bg-blue-50/30 text-center mb-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center mx-auto mb-3 shadow-md">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                </div>
                <h4 class="text-xs font-bold text-slate-800 mb-1">Pemindai Kamera Siap</h4>
                <p class="text-[11px] text-slate-500 mb-4">Arahkan kamera ke QR Code yang ditunjukkan oleh Mahasiswa.</p>

                <a href="{{ route('dosen.pindai-qr') }}" class="inline-block px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition-all">
                    Buka Kamera Scanner
                </a>
            </div>

            <!-- Riwayat Keputusan Terakhir -->
            <div class="pt-4 border-t border-slate-100">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-3">Aktivitas Terakhir Anda</span>
                @forelse($riwayatTerakhir as $riw)
                    <div class="flex items-center justify-between text-xs py-1.5">
                        <span class="text-slate-800 truncate max-w-[180px] font-medium">{{ $riw->peminjaman->nama_fasilitas }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $riw->keputusan === 'DISETUJUI' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                            {{ $riw->keputusan }}
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400">Belum ada keputusan.</p>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
