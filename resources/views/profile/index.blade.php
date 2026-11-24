@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page_title', 'Profil Saya')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Profil Saya</h2>
    <p class="text-sm text-slate-500">Kelola data akun dan keamanan Anda.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    <!-- Kolom Kiri: Form Data Diri & Ubah Kata Sandi -->
    <div class="lg:col-span-8 space-y-6">

        <!-- Card Data Diri -->
        <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-xs border border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 mb-5 pb-3 border-b border-slate-100 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Data Diri
            </h3>

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama lengkap</label>
                        <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            @if($user->role === 'mahasiswa') NIM @elseif($user->role === 'dosen') NIP @else ID Admin @endif
                        </label>
                        <input type="text" value="{{ $user->nomor_induk }}" readonly
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-500 text-sm cursor-not-allowed">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nomor HP</label>
                        <input type="text" name="nomor_hp" value="{{ old('nomor_hp', $user->nomor_hp) }}" placeholder="08xxxxxxxxxx"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Program studi</label>
                        <input type="text" value="{{ $user->prodi }}" readonly
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-500 text-sm cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Peran</label>
                        <input type="text" value="{{ ucfirst($user->role) }}" readonly
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-500 text-sm capitalize cursor-not-allowed">
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-3">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md transition-all">
                        Simpan perubahan
                    </button>
                    <a href="{{ url()->previous() }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-sm transition-all">
                        Batal
                    </a>
                </div>
            </form>
        </div>

        <!-- Card Ubah Kata Sandi -->
        <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-xs border border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 mb-5 pb-3 border-b border-slate-100 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                Ubah Kata Sandi
            </h3>

            <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kata sandi saat ini</label>
                    <input type="password" name="current_password" required placeholder="Masukkan kata sandi saat ini"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kata sandi baru</label>
                        <input type="password" name="new_password" required placeholder="Buat kata sandi baru"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Konfirmasi kata sandi baru</label>
                        <input type="password" name="new_password_confirmation" required placeholder="Ulangi kata sandi baru"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    </div>
                </div>

                <div class="pt-3">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md transition-all">
                        Perbarui kata sandi
                    </button>
                </div>
            </form>
        </div>

    </div>

    <!-- Kolom Kanan: Card Akun & Informasi Akun -->
    <div class="lg:col-span-4 space-y-6">

        <!-- Card Akun -->
        <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200 text-center">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 text-left">Akun</h3>

            <div class="w-24 h-24 rounded-full bg-blue-600 text-white font-extrabold text-3xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-blue-600/20">
                {{ strtoupper(substr($user->nama, 0, 1)) }}
            </div>

            <h4 class="text-lg font-bold text-slate-900 leading-tight">{{ $user->nama }}</h4>
            <div class="inline-block mt-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide
                @if($user->role === 'mahasiswa') bg-emerald-100 text-emerald-800
                @elseif($user->role === 'dosen') bg-blue-100 text-blue-800
                @else bg-amber-100 text-amber-800 @endif">
                {{ $user->role }}
            </div>
            <p class="text-xs text-slate-500 mt-2">Prodi {{ $user->prodi }}</p>

            <button type="button" class="mt-4 px-4 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-xs font-medium text-slate-600 transition-colors">
                Ubah foto
            </button>
        </div>

        <!-- Card Informasi Akun -->
        <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Informasi akun</h3>

            <dl class="space-y-3 text-sm">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <dt class="text-slate-500 text-xs">Peran</dt>
                    <dd class="font-semibold text-slate-800 capitalize">{{ $user->role }}</dd>
                </div>
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <dt class="text-slate-500 text-xs">Program studi</dt>
                    <dd class="font-semibold text-slate-800">{{ $user->prodi }}</dd>
                </div>
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <dt class="text-slate-500 text-xs">Status akun</dt>
                    <dd><span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-700 text-xs font-semibold">Aktif</span></dd>
                </div>
            </dl>

            <form method="POST" action="{{ route('logout') }}" class="mt-6">
                @csrf
                <button type="submit" class="w-full py-2.5 rounded-xl border border-rose-200 text-rose-600 hover:bg-rose-50 font-bold text-xs transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Keluar
                </button>
            </form>
        </div>

    </div>

</div>
@endsection
