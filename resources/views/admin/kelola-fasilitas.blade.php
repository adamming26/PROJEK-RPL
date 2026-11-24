@extends('layouts.app')

@section('title', 'Kelola Fasilitas')
@section('page_title', 'Kelola Fasilitas')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Kelola Fasilitas</h2>
        <p class="text-sm text-slate-500">Tambah dan perbarui data barang serta lab beserta kode fasilitasnya.</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

    <!-- Kolom Kiri: Tabel Daftar Fasilitas (Matches Bab III Wireframe 3.10) -->
    <div class="lg:col-span-8 space-y-6">
        <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200">
            <!-- Header Filter Tabs & Search -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.kelola-fasilitas', ['tab' => 'semua', 'search' => request('search')]) }}"
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $tab === 'semua' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Semua
                    </a>
                    <a href="{{ route('admin.kelola-fasilitas', ['tab' => 'barang', 'search' => request('search')]) }}"
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $tab === 'barang' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Barang
                    </a>
                    <a href="{{ route('admin.kelola-fasilitas', ['tab' => 'lab', 'search' => request('search')]) }}"
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $tab === 'lab' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Lab
                    </a>
                </div>

                <form method="GET" action="{{ route('admin.kelola-fasilitas') }}" class="w-full sm:w-64">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <div class="relative">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari data..."
                               class="w-full pl-9 pr-3 py-1.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                        <svg class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </form>
            </div>

            <!-- Table (Matches Bab III Wireframe 3.10) -->
            <div class="overflow-x-auto rounded-2xl border border-slate-100">
                <table class="w-full text-left text-xs min-w-[760px] border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 uppercase font-bold tracking-wider text-[11px]">
                            <th class="py-3.5 px-4 text-left">Kode</th>
                            <th class="py-3.5 px-4 text-left">Nama</th>
                            <th class="py-3.5 px-4 text-left">Jenis</th>
                            <th class="py-3.5 px-4 text-left">Stok / Kapasitas</th>
                            <th class="py-3.5 px-4 text-left">Kondisi</th>
                            <th class="py-3.5 px-4 text-left">Status</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 bg-white">
                        @if($tab !== 'lab')
                            @foreach($barangList as $b)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-4 px-4 font-mono font-bold text-slate-800 whitespace-nowrap">{{ $b->kode_barang }}</td>
                                    <td class="py-4 px-4 font-bold text-slate-900">{{ $b->nama_barang }}</td>
                                    <td class="py-4 px-4 whitespace-nowrap"><span class="inline-flex px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-700 font-semibold text-xs">Barang</span></td>
                                    <td class="py-4 px-4 font-medium whitespace-nowrap">{{ $b->stok_tersedia }} / {{ $b->stok_total }} unit</td>
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold {{ $b->kondisi === 'BAIK' ? 'text-emerald-700 bg-emerald-50' : 'text-amber-700 bg-amber-50' }}">
                                            {{ $b->kondisi }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full {{ $b->status === 'Aktif' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' }} text-xs font-bold leading-none">
                                            {{ $b->status }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-right space-x-1 whitespace-nowrap">
                                        <button type="button" onclick="editBarang({{ json_encode($b) }})"
                                                class="px-2.5 py-1 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs transition-colors">
                                            Ubah
                                        </button>
                                        <form method="POST" action="{{ route('admin.destroy-barang', $b->id_barang) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus barang ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2 py-1 text-rose-600 hover:underline font-bold text-xs">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @endif

                        @if($tab !== 'barang')
                            @foreach($ruanganList as $r)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-4 px-4 font-mono font-bold text-blue-700 whitespace-nowrap">{{ $r->kode_ruangan }}</td>
                                    <td class="py-4 px-4 font-bold text-slate-900">{{ $r->nama_ruangan }}</td>
                                    <td class="py-4 px-4 whitespace-nowrap"><span class="inline-flex px-2.5 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-100 font-semibold text-xs">Lab</span></td>
                                    <td class="py-4 px-4 font-medium whitespace-nowrap">{{ $r->kapasitas }} orang</td>
                                    <td class="py-4 px-4 whitespace-nowrap"><span class="text-slate-400">-</span></td>
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200 text-xs font-bold leading-none">
                                            {{ $r->status }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-right space-x-1 whitespace-nowrap">
                                        <button type="button" onclick="editRuangan({{ json_encode($r) }})"
                                                class="px-2.5 py-1 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs transition-colors">
                                            Ubah
                                        </button>
                                        <form method="POST" action="{{ route('admin.destroy-ruangan', $r->id_ruangan) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ruangan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2 py-1 text-rose-600 hover:underline font-bold text-xs">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Form Tambah / Ubah Fasilitas (Matches Bab III Wireframe 3.10) -->
    <div class="lg:col-span-4 space-y-6">
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xs border border-slate-200">
            <h3 class="text-base font-bold text-slate-900 pb-3 mb-4 border-b border-slate-100 flex items-center justify-between">
                <span id="form-heading">Tambah / ubah fasilitas</span>
                <span id="form-badge" class="text-xs px-2 py-0.5 rounded-md bg-blue-50 text-blue-600 font-bold uppercase">Baru</span>
            </h3>

            <!-- Switcher Tabs Form -->
            <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-xl text-xs font-bold mb-5" id="form-switcher">
                <button type="button" onclick="switchFormType('barang')" id="btn-switch-barang" class="flex-1 py-1.5 rounded-lg bg-blue-600 text-white shadow-xs transition-all">
                    Barang
                </button>
                <button type="button" onclick="switchFormType('lab')" id="btn-switch-lab" class="flex-1 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 transition-all">
                    Lab
                </button>
            </div>

            <!-- FORM BARANG -->
            <form id="form-barang" method="POST" action="{{ route('admin.store-barang') }}" class="space-y-4">
                @csrf
                <div id="barang-method-field"></div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kode fasilitas</label>
                    <input type="text" name="kode_barang" id="b_kode" placeholder="[KODE-00]" required
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-mono uppercase focus:ring-2 focus:ring-blue-600 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama</label>
                    <input type="text" name="nama_barang" id="b_nama" placeholder="[Nama fasilitas]" required
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Stok total</label>
                        <input type="number" name="stok_total" id="b_stok" min="0" value="5" required
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    </div>
                    <div id="b_tersedia_container" class="hidden">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Stok tersedia</label>
                        <input type="number" name="stok_tersedia" id="b_tersedia" min="0" value="5"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kondisi</label>
                        <select name="kondisi" id="b_kondisi" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none bg-white">
                            <option value="BAIK">Baik</option>
                            <option value="RUSAK">Rusak</option>
                            <option value="TIDAK LENGKAP">Tidak lengkap</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kategori</label>
                        <input type="text" name="kategori" id="b_kategori" value="Perangkat Pendukung Perkuliahan" required
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status</label>
                        <select name="status" id="b_status" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none bg-white">
                            <option value="Aktif">Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Deskripsi</label>
                    <textarea name="deskripsi" id="b_deskripsi" rows="2" placeholder="Keterangan spesifikasi..."
                              class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none"></textarea>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition-all">
                        Simpan
                    </button>
                    <button type="button" onclick="resetForm()" class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 font-semibold text-xs">
                        Batal
                    </button>
                </div>
            </form>

            <!-- FORM LAB -->
            <form id="form-lab" method="POST" action="{{ route('admin.store-ruangan') }}" class="space-y-4 hidden">
                @csrf
                <div id="lab-method-field"></div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kode fasilitas</label>
                    <input type="text" name="kode_ruangan" id="r_kode" placeholder="[KODE-00]" required
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-mono uppercase focus:ring-2 focus:ring-blue-600 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama</label>
                    <input type="text" name="nama_ruangan" id="r_nama" placeholder="[Nama lab / ruangan]" required
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kapasitas</label>
                        <input type="number" name="kapasitas" id="r_kapasitas" min="1" value="30" required
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status</label>
                        <select name="status" id="r_status" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none bg-white">
                            <option value="Perlu Izin">Perlu Izin</option>
                            <option value="Tersedia">Tersedia</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Lokasi</label>
                    <input type="text" name="lokasi" id="r_lokasi" placeholder="Gedung FMIPA Lantai..." required
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Deskripsi fasilitas</label>
                    <textarea name="deskripsi" id="r_deskripsi" rows="2" placeholder="Fasilitas yang tersedia di ruangan..."
                              class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none"></textarea>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition-all">
                        Simpan
                    </button>
                    <button type="button" onclick="resetForm()" class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 font-semibold text-xs">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function switchFormType(type) {
        const btnBarang = document.getElementById('btn-switch-barang');
        const btnLab = document.getElementById('btn-switch-lab');
        const formBarang = document.getElementById('form-barang');
        const formLab = document.getElementById('form-lab');

        if (type === 'barang') {
            btnBarang.className = 'flex-1 py-1.5 rounded-lg bg-blue-600 text-white shadow-xs transition-all';
            btnLab.className = 'flex-1 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 transition-all';
            formBarang.classList.remove('hidden');
            formLab.classList.add('hidden');
        } else {
            btnLab.className = 'flex-1 py-1.5 rounded-lg bg-blue-600 text-white shadow-xs transition-all';
            btnBarang.className = 'flex-1 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 transition-all';
            formLab.classList.remove('hidden');
            formBarang.classList.add('hidden');
        }
    }

    function editBarang(barang) {
        switchFormType('barang');
        document.getElementById('form-heading').innerText = 'Ubah Fasilitas Barang';
        document.getElementById('form-badge').innerText = 'Edit';
        document.getElementById('form-badge').className = 'text-xs px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 font-bold uppercase';

        const form = document.getElementById('form-barang');
        form.action = '/admin/fasilitas/barang/' + barang.id_barang;
        document.getElementById('barang-method-field').innerHTML = '@method("PUT")';

        document.getElementById('b_kode').value = barang.kode_barang;
        document.getElementById('b_nama').value = barang.nama_barang;
        document.getElementById('b_stok').value = barang.stok_total;
        document.getElementById('b_tersedia').value = barang.stok_tersedia;
        document.getElementById('b_tersedia_container').classList.remove('hidden');
        document.getElementById('b_kondisi').value = barang.kondisi;
        document.getElementById('b_kategori').value = barang.kategori;
        document.getElementById('b_status').value = barang.status;
        document.getElementById('b_deskripsi').value = barang.deskripsi || '';

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function editRuangan(ruangan) {
        switchFormType('lab');
        document.getElementById('form-heading').innerText = 'Ubah Ruangan / Lab';
        document.getElementById('form-badge').innerText = 'Edit';
        document.getElementById('form-badge').className = 'text-xs px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 font-bold uppercase';

        const form = document.getElementById('form-lab');
        form.action = '/admin/fasilitas/ruangan/' + ruangan.id_ruangan;
        document.getElementById('lab-method-field').innerHTML = '@method("PUT")';

        document.getElementById('r_kode').value = ruangan.kode_ruangan;
        document.getElementById('r_nama').value = ruangan.nama_ruangan;
        document.getElementById('r_kapasitas').value = ruangan.kapasitas;
        document.getElementById('r_status').value = ruangan.status;
        document.getElementById('r_lokasi').value = ruangan.lokasi;
        document.getElementById('r_deskripsi').value = ruangan.deskripsi || '';

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function resetForm() {
        document.getElementById('form-heading').innerText = 'Tambah / ubah fasilitas';
        document.getElementById('form-badge').innerText = 'Baru';
        document.getElementById('form-badge').className = 'text-xs px-2 py-0.5 rounded-md bg-blue-50 text-blue-600 font-bold uppercase';

        const formB = document.getElementById('form-barang');
        formB.action = "{{ route('admin.store-barang') }}";
        formB.reset();
        document.getElementById('barang-method-field').innerHTML = '';
        document.getElementById('b_tersedia_container').classList.add('hidden');

        const formL = document.getElementById('form-lab');
        formL.action = "{{ route('admin.store-ruangan') }}";
        formL.reset();
        document.getElementById('lab-method-field').innerHTML = '';
    }
</script>
@endpush
@endsection
