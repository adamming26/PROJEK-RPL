@extends('layouts.app')

@section('title', 'Detail Peminjaman')
@section('page_title', 'Detail Peminjaman')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <a href="{{ route('mahasiswa.peminjaman-saya') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 flex items-center gap-1 mb-1">
            &larr; Kembali ke Peminjaman Saya
        </a>
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Detail Peminjaman #{{ $peminjaman->kode_peminjaman }}</h2>
    </div>
    <div>
        @if($peminjaman->status === 'MENUNGGU PERSETUJUAN')
            <span class="px-3 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold">Menunggu Dosen</span>
        @elseif(in_array($peminjaman->status, ['DISETUJUI', 'MENUNGGU PENYERAHAN']))
            <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold">Disetujui Dosen</span>
        @elseif($peminjaman->status === 'DIPINJAM')
            <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200 text-xs font-bold">Sedang Dipinjam</span>
        @elseif($peminjaman->status === 'MENUNGGU PENGEMBALIAN')
            <span class="px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 text-xs font-bold">Menunggu Pengembalian</span>
        @elseif($peminjaman->status === 'DITOLAK')
            <span class="px-3 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200 text-xs font-bold">Ditolak</span>
        @else
            <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold">Selesai</span>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

    <!-- Kolom Kiri: Rincian Peminjaman -->
    <div class="lg:col-span-8 space-y-6">
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xs border border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 mb-4 pb-3 border-b border-slate-100">Informasi Peminjaman</h3>

            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs mb-6">
                <div>
                    <dt class="text-slate-400 uppercase font-bold mb-1">Kode Peminjaman</dt>
                    <dd class="font-mono font-bold text-slate-800 text-sm">{{ $peminjaman->kode_peminjaman }}</dd>
                </div>
                <div>
                    <dt class="text-slate-400 uppercase font-bold mb-1">Tanggal Pengajuan</dt>
                    <dd class="font-semibold text-slate-800">{{ $peminjaman->tanggal_pengajuan->format('d F Y') }}</dd>
                </div>
                <div>
                    <dt class="text-slate-400 uppercase font-bold mb-1">Fasilitas</dt>
                    <dd class="font-bold text-slate-900 text-sm">{{ $peminjaman->nama_fasilitas }}</dd>
                </div>
                <div>
                    <dt class="text-slate-400 uppercase font-bold mb-1">Jenis Fasilitas</dt>
                    <dd><span class="px-2 py-0.5 rounded bg-slate-100 font-semibold text-slate-700">{{ $peminjaman->jenis_fasilitas }}</span></dd>
                </div>
                <div>
                    <dt class="text-slate-400 uppercase font-bold mb-1">Jumlah / Peserta</dt>
                    <dd class="font-semibold text-slate-800">{{ $peminjaman->jumlah }} unit / orang</dd>
                </div>
                <div>
                    <dt class="text-slate-400 uppercase font-bold mb-1">Jadwal Penggunaan</dt>
                    <dd class="font-semibold text-slate-800">{{ $peminjaman->tanggal_peminjaman->format('d/m/Y') }} ({{ substr($peminjaman->jam_mulai, 0, 5) }} - {{ substr($peminjaman->jam_selesai, 0, 5) }} WITA)</dd>
                </div>
                <div>
                    <dt class="text-slate-400 uppercase font-bold mb-1">Durasi</dt>
                    <dd class="font-semibold text-slate-800">{{ $peminjaman->durasi }}</dd>
                </div>
                <div>
                    <dt class="text-slate-400 uppercase font-bold mb-1">Rencana Pengembalian</dt>
                    <dd class="font-semibold text-slate-800">{{ $peminjaman->waktu_pengembalian_rencana ? $peminjaman->waktu_pengembalian_rencana->format('d/m/Y H:i') : '-' }}</dd>
                </div>
            </dl>

            <div class="pt-4 border-t border-slate-100">
                <dt class="text-slate-400 uppercase font-bold text-xs mb-1.5">Tujuan Peminjaman</dt>
                <dd class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-slate-700 leading-relaxed">
                    {{ $peminjaman->tujuan }}
                </dd>
            </div>
        </div>

        <!-- Riwayat Keputusan Dosen -->
        @if($peminjaman->persetujuan->isNotEmpty())
            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xs border border-slate-200">
                <h3 class="text-lg font-bold text-slate-900 mb-4 pb-3 border-b border-slate-100">Keputusan Dosen</h3>
                @foreach($peminjaman->persetujuan as $persetujuan)
                    <div class="p-4 rounded-2xl {{ $persetujuan->keputusan === 'DISETUJUI' ? 'bg-emerald-50/70 border border-emerald-200/60' : 'bg-rose-50/70 border border-rose-200/60' }} mb-3">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-xs {{ $persetujuan->keputusan === 'DISETUJUI' ? 'text-emerald-900' : 'text-rose-900' }}">
                                Keputusan: {{ $persetujuan->keputusan }}
                            </span>
                            <span class="text-[11px] text-slate-500">{{ $persetujuan->tanggal->format('d/m/Y H:i') }}</span>
                        </div>
                        <p class="text-xs text-slate-700 mb-1">
                            <strong>Dosen Penguji / Verifikator:</strong> {{ $persetujuan->dosen->nama ?? '-' }}
                        </p>
                        @if($persetujuan->catatan)
                            <p class="text-xs text-slate-600 italic">"{{ $persetujuan->catatan }}"</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Riwayat Pengembalian -->
        @if($peminjaman->pengembalian)
            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xs border border-slate-200">
                <h3 class="text-lg font-bold text-slate-900 mb-4 pb-3 border-b border-slate-100">Catatan Pengembalian</h3>
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs space-y-2">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Waktu Kembali:</span>
                        <span class="font-semibold text-slate-800">{{ $peminjaman->pengembalian->waktu_kembali->format('d F Y H:i') }} WITA</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Dosen Penerima:</span>
                        <span class="font-semibold text-slate-800">{{ $peminjaman->pengembalian->dosen->nama ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Kondisi Barang:</span>
                        <span class="font-bold text-emerald-600">{{ $peminjaman->pengembalian->kondisi_barang }}</span>
                    </div>
                    @if($peminjaman->pengembalian->catatan)
                        <div class="pt-2 border-t border-slate-200 text-slate-600">
                            <strong>Catatan Dosen:</strong> {{ $peminjaman->pengembalian->catatan }}
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Kolom Kanan: Aksi & Shortcut QR Code -->
    <div class="lg:col-span-4 space-y-6">
        @if(in_array($peminjaman->status, ['DISETUJUI', 'MENUNGGU PENYERAHAN', 'DIPINJAM', 'MENUNGGU PENGEMBALIAN']))
            <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200 text-center">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 text-left">
                    Tindakan Transaksi
                </h3>

                @if(in_array($peminjaman->status, ['DISETUJUI', 'MENUNGGU PENYERAHAN']))
                    <p class="text-xs text-slate-500 mb-4">Pengajuan Anda telah disetujui. Tampilkan QR Code kepada Dosen saat penyerahan.</p>
                    <a href="{{ route('mahasiswa.qr-code', $peminjaman->id_peminjaman) }}"
                       class="block w-full py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition-all">
                        Tampilkan QR Code Peminjaman
                    </a>
                @elseif($peminjaman->status === 'DIPINJAM')
                    <p class="text-xs text-slate-500 mb-4">Barang sedang berada pada Anda. Ajukan pengembalian untuk menerbitkan QR Code pengembalian.</p>
                    <form method="POST" action="{{ route('mahasiswa.ajukan-pengembalian', $peminjaman->id_peminjaman) }}">
                        @csrf
                        <button type="submit" class="w-full py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition-all">
                            Ajukan Pengembalian Sekarang
                        </button>
                    </form>
                @elseif($peminjaman->status === 'MENUNGGU PENGEMBALIAN')
                    <p class="text-xs text-slate-500 mb-4">QR Code pengembalian aktif. Tunjukkan kode kepada Dosen pemeriksa.</p>
                    <a href="{{ route('mahasiswa.qr-code', $peminjaman->id_peminjaman) }}"
                       class="block w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition-all">
                        Tampilkan QR Code Pengembalian
                    </a>
                @endif
            </div>
        @endif
    </div>

</div>
@endsection
