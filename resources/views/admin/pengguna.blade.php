@extends('layouts.app')

@section('title', 'Data Pengguna')
@section('page_title', 'Kelola Pengguna')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Data Pengguna SIPAFKA</h2>
        <p class="text-sm text-slate-500">Daftar akun civitas akademika (Mahasiswa, Dosen, dan Admin).</p>
    </div>
</div>

<div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.kelola-pengguna', ['role' => 'semua', 'search' => request('search')]) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $role === 'semua' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua
            </a>
            <a href="{{ route('admin.kelola-pengguna', ['role' => 'mahasiswa', 'search' => request('search')]) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $role === 'mahasiswa' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Mahasiswa
            </a>
            <a href="{{ route('admin.kelola-pengguna', ['role' => 'dosen', 'search' => request('search')]) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $role === 'dosen' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Dosen
            </a>
            <a href="{{ route('admin.kelola-pengguna', ['role' => 'admin', 'search' => request('search')]) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $role === 'admin' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Admin
            </a>
        </div>

        <form method="GET" action="{{ route('admin.kelola-pengguna') }}" class="w-full sm:w-72">
            <input type="hidden" name="role" value="{{ $role }}">
            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, email, NIM/NIP..."
                       class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-slate-100">
        <table class="w-full text-left text-xs min-w-[760px] border-collapse">
            <thead>
                <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 uppercase font-bold tracking-wider text-[11px]">
                    <th class="py-3.5 px-4 text-left">Nama Pengguna</th>
                    <th class="py-3.5 px-4 text-left">NIM / NIP / ID</th>
                    <th class="py-3.5 px-4 text-left">Email &amp; Kontak</th>
                    <th class="py-3.5 px-4 text-left">Peran</th>
                    <th class="py-3.5 px-4 text-left">Program Studi</th>
                    <th class="py-3.5 px-4 text-right">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700 bg-white">
                @forelse($users as $u)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-4 px-4 font-bold text-slate-900 flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0 shadow-xs">
                                {{ strtoupper(substr($u->nama, 0, 1)) }}
                            </div>
                            <span class="text-sm">{{ $u->nama }}</span>
                        </td>
                        <td class="py-4 px-4 font-mono font-bold text-slate-800 whitespace-nowrap">{{ $u->nomor_induk ?: '-' }}</td>
                        <td class="py-4 px-4 text-slate-600">
                            <div class="font-medium text-slate-800">{{ $u->email }}</div>
                            <div class="text-[10px] text-slate-400 font-mono">{{ $u->nomor_hp ?: '-' }}</div>
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider leading-none
                                @if($u->role === 'mahasiswa') bg-emerald-50 text-emerald-700 border border-emerald-200
                                @elseif($u->role === 'dosen') bg-blue-50 text-blue-700 border border-blue-200
                                @else bg-amber-50 text-amber-700 border border-amber-200 @endif">
                                {{ $u->role }}
                            </span>
                        </td>
                        <td class="py-4 px-4 text-slate-600 whitespace-nowrap">{{ $u->prodi }}</td>
                        <td class="py-4 px-4 text-right whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 font-bold text-xs border border-emerald-100">
                                {{ ucfirst($u->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400">
                            Tidak ada data pengguna yang cocok.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 pt-3 border-t border-slate-100">
        {{ $users->links() }}
    </div>
</div>
@endsection
