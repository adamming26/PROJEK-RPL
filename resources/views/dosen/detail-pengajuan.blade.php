@extends('layouts.app')

@section('title', 'Detail Pengajuan #' . $peminjaman->kode_peminjaman)
@section('page_title', 'Periksa Pengajuan')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <a href="{{ route('dosen.pengajuan-masuk') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 flex items-center gap-1 mb-1">
            &larr; Kembali ke Pengajuan Masuk
        </a>
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Pemeriksaan Pengajuan #{{ $peminjaman->kode_peminjaman }}</h2>
    </div>
    <div>
        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
            @if($peminjaman->status === 'MENUNGGU PERSETUJUAN') bg-amber-50 text-amber-700 border border-amber-200
            @elseif(in_array($peminjaman->status, ['DISETUJUI', 'MENUNGGU PENYERAHAN'])) bg-emerald-50 text-emerald-700 border border-emerald-200
            @elseif($peminjaman->status === 'DIPINJAM') bg-blue-50 text-blue-700 border border-blue-200
            @elseif($peminjaman->status === 'DITOLAK') bg-rose-50 text-rose-700 border border-rose-200
            @else bg-slate-100 text-slate-700 @endif">
            {{ $peminjaman->status }}
        </span>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

    <!-- Kolom Kiri: Detail Informasi & Validasi Sistem -->
    <div class="lg:col-span-8 space-y-6">

        <!-- Status Validasi Sistem Otomatis -->
        @if($peminjaman->jenis_fasilitas === 'BARANG')
            @php $barang = $peminjaman->barang; @endphp
            @if($barang && $barang->stok_tersedia >= $peminjaman->jumlah)
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs flex items-start gap-3">
                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <strong class="font-bold block text-sm">Stok Tersedia &amp; Mencukupi</strong>
                        <span>Stok barang saat ini tersedia <strong>{{ $barang->stok_tersedia }} unit</strong> (dari total {{ $barang->stok_total }} unit). Mahasiswa mengajukan <strong>{{ $peminjaman->jumlah }} unit</strong>.</span>
                    </div>
                </div>
            @else
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs flex items-start gap-3">
                    <svg class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <strong class="font-bold block text-sm">Peringatan: Stok Tidak Mencukupi!</strong>
                        <span>Stok tersedia saat ini hanya <strong>{{ optional($barang)->stok_tersedia ?? 0 }} unit</strong>, sedangkan pengajuan membutuhkan <strong>{{ $peminjaman->jumlah }} unit</strong>. Disarankan untuk menolak pengajuan ini.</span>
                    </div>
                </div>
            @endif
        @else
            @if($bentrokJadwal)
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs flex items-start gap-3">
                    <svg class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <strong class="font-bold block text-sm">Jadwal Bentrok Terdeteksi!</strong>
                        <span>Ruangan ini telah disetujui / digunakan oleh <strong>{{ $bentrokJadwal->user->nama }}</strong> pada rentang jam <strong>{{ $bentrokJadwal->jam_mulai }} - {{ $bentrokJadwal->jam_selesai }}</strong>.</span>
                    </div>
                </div>
            @else
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs flex items-start gap-3">
                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <strong class="font-bold block text-sm">Jadwal Bebas &amp; Siap Digunakan</strong>
                        <span>Tidak ditemukan bentrok pemakaian lain untuk laboratorium ini pada jam tersebut.</span>
                    </div>
                </div>
            @endif
        @endif

        <!-- Card Data Pemohon & Fasilitas -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xs border border-slate-200">
            <h3 class="text-base font-bold text-slate-900 pb-3 mb-5 border-b border-slate-100">Data Pemohon &amp; Keperluan</h3>

            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs mb-6">
                <div>
                    <dt class="text-slate-400 uppercase font-bold mb-1">Nama Mahasiswa</dt>
                    <dd class="font-bold text-slate-900 text-sm">{{ $peminjaman->user->nama }}</dd>
                </div>
                <div>
                    <dt class="text-slate-400 uppercase font-bold mb-1">NIM / Nomor Induk</dt>
                    <dd class="font-mono font-bold text-slate-800">{{ $peminjaman->user->nomor_induk }}</dd>
                </div>
                <div>
                    <dt class="text-slate-400 uppercase font-bold mb-1">Program Studi</dt>
                    <dd class="font-semibold text-slate-800">{{ $peminjaman->user->prodi }}</dd>
                </div>
                <div>
                    <dt class="text-slate-400 uppercase font-bold mb-1">Kontak Mahasiswa</dt>
                    <dd class="font-semibold text-slate-800">{{ $peminjaman->user->nomor_hp ?: '-' }} ({{ $peminjaman->user->email }})</dd>
                </div>
                <div>
                    <dt class="text-slate-400 uppercase font-bold mb-1">Fasilitas Diminta</dt>
                    <dd class="font-bold text-blue-700 text-sm">{{ $peminjaman->nama_fasilitas }}</dd>
                </div>
                <div>
                    <dt class="text-slate-400 uppercase font-bold mb-1">Jumlah / Kapasitas</dt>
                    <dd class="font-semibold text-slate-800">{{ $peminjaman->jumlah }} unit / peserta</dd>
                </div>
                <div>
                    <dt class="text-slate-400 uppercase font-bold mb-1">Tanggal Penggunaan</dt>
                    <dd class="font-semibold text-slate-800">{{ $peminjaman->tanggal_peminjaman->format('d F Y') }}</dd>
                </div>
                <div>
                    <dt class="text-slate-400 uppercase font-bold mb-1">Waktu &amp; Durasi</dt>
                    <dd class="font-semibold text-slate-800">{{ substr($peminjaman->jam_mulai, 0, 5) }} - {{ substr($peminjaman->jam_selesai, 0, 5) }} WITA ({{ $peminjaman->durasi }})</dd>
                </div>
            </dl>

            <div class="pt-4 border-t border-slate-100">
                <dt class="text-slate-400 uppercase font-bold text-xs mb-1.5">Tujuan Peminjaman</dt>
                <dd class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-slate-700 leading-relaxed font-medium">
                    {{ $peminjaman->tujuan }}
                </dd>
            </div>
        </div>

    </div>

    <!-- Kolom Kanan: Form Keputusan Dosen -->
    <div class="lg:col-span-4 space-y-6">

        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xs border border-slate-200">
            <h3 class="text-base font-bold text-slate-900 pb-3 mb-4 border-b border-slate-100">Keputusan Dosen</h3>

            @if($peminjaman->status === 'MENUNGGU PERSETUJUAN')
                <p class="text-xs text-slate-500 mb-4">
                    Pilih keputusan untuk permohonan ini. Jika disetujui, sistem akan secara otomatis menerbitkan <strong>QR Code Peminjaman (15 Menit)</strong> untuk mahasiswa.
                </p>

                <form method="POST" action="{{ route('dosen.putuskan-pengajuan', $peminjaman->id_peminjaman) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="catatan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Catatan keputusan</label>
                        <textarea name="catatan" id="catatan" rows="3" placeholder="Tuliskan catatan arahan (atau alasan jika menolak)..."
                                  class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none"></textarea>
                    </div>

                    <div class="space-y-2 pt-2">
                        <button type="submit" name="keputusan" value="DISETUJUI"
                                class="w-full py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition-all flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Setujui Pengajuan &amp; Terbitkan QR
                        </button>

                        <button type="submit" name="keputusan" value="DITOLAK"
                                class="w-full py-3 rounded-xl border border-rose-300 text-rose-600 hover:bg-rose-50 font-bold text-xs transition-all flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            Tolak Pengajuan
                        </button>
                    </div>
                </form>
            @else
                <div class="p-4 rounded-2xl bg-slate-50 text-center text-xs text-slate-600">
                    <p class="font-bold text-slate-800 mb-1">Pengajuan Ini Telah Diputuskan</p>
                    <p>Status: <strong class="text-blue-600">{{ $peminjaman->status }}</strong></p>
                    @if($peminjaman->catatan_penolakan)
                        <p class="mt-2 text-rose-600 italic">"{{ $peminjaman->catatan_penolakan }}"</p>
                    @endif
                </div>

                @if(in_array($peminjaman->status, ['DISETUJUI', 'MENUNGGU PENYERAHAN', 'DIPINJAM', 'MENUNGGU PENGEMBALIAN']))
                    <div class="mt-4">
                        <a href="{{ route('dosen.pindai-qr') }}" class="block w-full py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs text-center shadow-xs">
                            Buka Pemindai QR Code
                        </a>
                    </div>
                @endif
            @endif
        </div>

    </div>

</div>
@endsection
