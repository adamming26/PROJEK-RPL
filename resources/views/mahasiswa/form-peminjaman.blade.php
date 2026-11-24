@extends('layouts.app')

@section('title', 'Ajukan Peminjaman')
@section('page_title', 'Ajukan Peminjaman')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">
        {{ $jenis === 'BARANG' ? 'Ajukan Peminjaman Fasilitas' : 'Ajukan Peminjaman Lab' }}
    </h2>
    <p class="text-sm text-slate-500">
        {{ $jenis === 'BARANG' ? 'Isi data peminjaman. Stok barang diperiksa sistem.' : 'Lab komputer disediakan untuk praktikum. Penggunaan lab memerlukan izin Dosen.' }}
    </p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

    <!-- Kolom Kiri: Formulir Pengajuan (Matches Bab III Wireframe) -->
    <div class="lg:col-span-8">
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xs border border-slate-200">
            <!-- Header & Switcher Tabs -->
            <div class="flex items-center justify-between pb-6 mb-6 border-b border-slate-100">
                <h3 class="text-lg font-bold text-slate-900">
                    {{ $jenis === 'BARANG' ? 'Data peminjaman barang' : 'Data penggunaan lab' }}
                </h3>
                <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-xl text-xs font-bold">
                    <a href="{{ route('mahasiswa.form-peminjaman', 'BARANG') }}"
                       class="px-4 py-1.5 rounded-lg transition-all {{ $jenis === 'BARANG' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Barang
                    </a>
                    <a href="{{ route('mahasiswa.form-peminjaman', 'RUANGAN') }}"
                       class="px-4 py-1.5 rounded-lg transition-all {{ $jenis === 'RUANGAN' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Lab
                    </a>
                </div>
            </div>

            <form method="POST" action="{{ route('mahasiswa.store-peminjaman') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="jenis_fasilitas" value="{{ $jenis }}">

                @if($jenis === 'BARANG')
                    <!-- FORM PEMINJAMAN BARANG -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                        <div class="sm:col-span-8">
                            <label for="id_barang" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Fasilitas</label>
                            <select name="id_barang" id="id_barang" required
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none bg-white">
                                <option value="">[Pilih barang]</option>
                                @foreach($barangList as $b)
                                    <option value="{{ $b->id_barang }}" {{ (old('id_barang', optional($selectedBarang)->id_barang) == $b->id_barang) ? 'selected' : '' }}>
                                        {{ $b->nama_barang }} (Sisa: {{ $b->stok_tersedia }} unit)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-4">
                            <label for="jumlah" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jumlah</label>
                            <input type="number" name="jumlah" id="jumlah" value="{{ old('jumlah', 1) }}" min="1" max="10" required
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                        </div>
                    </div>
                @else
                    <!-- FORM PEMINJAMAN LAB -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                        <div class="sm:col-span-8">
                            <label for="id_ruangan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Lab</label>
                            <select name="id_ruangan" id="id_ruangan" required
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none bg-white">
                                <option value="">[Pilih lab]</option>
                                @foreach($ruanganList as $r)
                                    <option value="{{ $r->id_ruangan }}" {{ (old('id_ruangan', optional($selectedRuangan)->id_ruangan) == $r->id_ruangan) ? 'selected' : '' }}>
                                        {{ $r->nama_ruangan }} (Kapasitas: {{ $r->kapasitas }} orang)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-4">
                            <label for="jumlah" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jumlah peserta</label>
                            <input type="number" name="jumlah" id="jumlah" value="{{ old('jumlah', 25) }}" min="1" max="100" required
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="tanggal_peminjaman" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal</label>
                        <input type="date" name="tanggal_peminjaman" id="tanggal_peminjaman" min="{{ date('Y-m-d') }}" value="{{ old('tanggal_peminjaman', date('Y-m-d')) }}" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    </div>
                    <div>
                        <label for="jam_mulai" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jam mulai</label>
                        <input type="time" name="jam_mulai" id="jam_mulai" value="{{ old('jam_mulai', '08:00') }}" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label for="durasi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Durasi</label>
                    <select name="durasi" id="durasi" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none bg-white">
                        <option value="1 Jam" {{ old('durasi') == '1 Jam' ? 'selected' : '' }}>1 Jam</option>
                        <option value="2 Jam" {{ old('durasi', '2 Jam') == '2 Jam' ? 'selected' : '' }}>2 Jam</option>
                        <option value="3 Jam" {{ old('durasi') == '3 Jam' ? 'selected' : '' }}>3 Jam</option>
                        <option value="4 Jam" {{ old('durasi') == '4 Jam' ? 'selected' : '' }}>4 Jam</option>
                        <option value="1 Hari Penuh" {{ old('durasi') == '1 Hari Penuh' ? 'selected' : '' }}>1 Hari Penuh</option>
                    </select>
                </div>

                <div>
                    <label for="tujuan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        {{ $jenis === 'BARANG' ? 'Tujuan peminjaman' : 'Tujuan penggunaan lab' }}
                    </label>
                    <textarea name="tujuan" id="tujuan" rows="4" required
                              placeholder="{{ $jenis === 'BARANG' ? 'Jelaskan keperluan peminjaman barang ini...' : 'Jelaskan kegiatan atau praktikum yang akan dilaksanakan...' }}"
                              class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none placeholder:text-slate-400">{{ old('tujuan') }}</textarea>
                </div>

                @if($jenis === 'RUANGAN')
                    <p class="text-xs text-slate-400 italic">Untuk lab, kolom Jumlah diganti Jumlah peserta.</p>
                @endif

                <div class="flex items-center gap-3 pt-3">
                    <button type="submit" class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md transition-all">
                        {{ $jenis === 'BARANG' ? 'Ajukan peminjaman' : 'Ajukan izin penggunaan' }}
                    </button>
                    <a href="{{ route('mahasiswa.dashboard') }}" class="px-5 py-3 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-sm transition-all">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Kolom Kanan: Panduan, Pemeriksaan Stok & Alur (Matches Bab III Wireframe) -->
    <div class="lg:col-span-4 space-y-6">

        <!-- Card Pemeriksaan Stok / Izin Lab -->
        <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">
                {{ $jenis === 'BARANG' ? 'Pemeriksaan stok dan izin' : 'Ketentuan izin lab' }}
            </h3>

            @if($jenis === 'BARANG')
                <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-200/60 mb-3">
                    <div class="flex items-center gap-2 text-xs font-bold text-blue-900 mb-1">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        Stok mencukupi
                    </div>
                    <p class="text-xs text-blue-800 leading-relaxed">
                        Sistem memeriksa ketersediaan stok aktual secara otomatis sebelum pengajuan dikirimkan.
                    </p>
                </div>
            @else
                <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-200/60 mb-3">
                    <div class="flex items-center gap-2 text-xs font-bold text-blue-900 mb-1">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        Izin Dosen diperlukan
                    </div>
                    <p class="text-xs text-blue-800 leading-relaxed">
                        Penggunaan lab harus mendapat izin Dosen terlebih dahulu. Sistem secara otomatis mendeteksi bentrok jadwal pemakaian.
                    </p>
                </div>
            @endif
        </div>

        <!-- Card Alur Setelah Diajukan (Matches Bab III Wireframe 3.8) -->
        <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">
                Alur setelah diajukan
            </h3>

            <ol class="space-y-4 text-xs">
                <li class="flex items-start gap-3">
                    <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center flex-shrink-0 text-xs">1</span>
                    <div class="pt-0.5">
                        <span class="font-bold text-slate-900 block">Pemeriksaan Otomatis</span>
                        <span class="text-slate-500">Sistem memeriksa stok (barang) atau bentrok jadwal (lab).</span>
                    </div>
                </li>
                <li class="flex items-start gap-3">
                    <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center flex-shrink-0 text-xs">2</span>
                    <div class="pt-0.5">
                        <span class="font-bold text-slate-900 block">Persetujuan Dosen</span>
                        <span class="text-slate-500">Dosen memeriksa dan menyetujui atau menolak permohonan.</span>
                    </div>
                </li>
                <li class="flex items-start gap-3">
                    <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center flex-shrink-0 text-xs">3</span>
                    <div class="pt-0.5">
                        <span class="font-bold text-slate-900 block">Penerbitan QR Code</span>
                        <span class="text-slate-500">QR Code terbit otomatis dan berlaku selama 15 menit.</span>
                    </div>
                </li>
                <li class="flex items-start gap-3">
                    <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center flex-shrink-0 text-xs">4</span>
                    <div class="pt-0.5">
                        <span class="font-bold text-slate-900 block">Pemindaian & Penyerahan</span>
                        <span class="text-slate-500">Dosen memindai QR saat penyerahan barang / izin masuk lab.</span>
                    </div>
                </li>
            </ol>
        </div>

    </div>

</div>
@endsection
