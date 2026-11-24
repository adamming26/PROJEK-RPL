<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIPAFKA') - Sistem Informasi Peminjaman Fasilitas Kampus</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (Vite + Standalone fallback) -->
    @if(file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            navy: {
                                950: '#070f26',
                                900: '#0c1a40',
                                800: '#13285c',
                                700: '#1b3b87',
                            },
                            primary: {
                                50: '#eff6ff',
                                100: '#dbeafe',
                                500: '#3b82f6',
                                600: '#2563eb',
                                700: '#1d4ed8',
                            }
                        },
                        fontFamily: {
                            sans: ['Inter', 'sans-serif'],
                        }
                    }
                }
            }
        </script>
    @endif

    <!-- QR Code Script & HTML5 QR Scanner -->
    <script src="{{ asset('js/qrcode.min.js') }}"></script>
    <script src="{{ asset('js/html5-qrcode.min.js') }}"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-banner {
            background: linear-gradient(135deg, rgba(12, 26, 64, 0.90) 0%, rgba(27, 59, 135, 0.85) 100%),
                        url('https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat;
        }

        /* Global Table Spacing & Padding Fix */
        table th, table td {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
            vertical-align: middle;
        }
        table th:first-child, table td:first-child {
            padding-left: 1.25rem !important;
        }
        table th:last-child, table td:last-child {
            padding-right: 1.25rem !important;
        }
        table th {
            white-space: nowrap;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-[#f8fafc] text-slate-800 antialiased min-h-screen flex flex-col">

    <div class="flex flex-1 min-h-screen">
        <!-- SIDEBAR (Navy Blue Modern) -->
        <aside id="sidebar" class="w-64 bg-[#0c1a40] text-white flex-shrink-0 flex flex-col fixed inset-y-0 left-0 z-40 transform -translate-x-full md:translate-x-0 transition-transform duration-200 ease-in-out shadow-2xl">
            <!-- Brand & University Header -->
            <div class="p-5 border-b border-white/10 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center font-bold text-white shadow-md text-lg tracking-wider">
                        S
                    </div>
                    <div>
                        <div class="text-lg font-bold tracking-tight text-white flex items-center gap-1.5">
                            SIPAFKA
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-blue-500/30 text-blue-300 font-semibold uppercase">UHO</span>
                        </div>
                        <div class="text-[11px] text-slate-300 font-medium leading-tight">Prodi Ilmu Komputer</div>
                    </div>
                </div>
                <!-- Close Button on Mobile -->
                <button type="button" onclick="toggleSidebar()" class="md:hidden text-slate-400 hover:text-white p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Role Badge -->
            <div class="px-5 py-3 bg-white/5 border-b border-white/10 flex items-center justify-between">
                <span class="text-xs uppercase tracking-wider font-semibold text-slate-400">Hak Akses</span>
                @auth
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wide
                        @if(Auth::user()->role === 'mahasiswa') bg-emerald-500/20 text-emerald-300 border border-emerald-500/30
                        @elseif(Auth::user()->role === 'dosen') bg-blue-500/20 text-blue-300 border border-blue-500/30
                        @else bg-amber-500/20 text-amber-300 border border-amber-500/30 @endif">
                        {{ Auth::user()->role }}
                    </span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wide bg-slate-500/20 text-slate-300 border border-slate-500/30">
                        Tamu / Publik
                    </span>
                @endauth
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto">
                @auth
                    @if(Auth::user()->role === 'mahasiswa')
                        <!-- Mahasiswa Nav -->
                        <a href="{{ route('mahasiswa.dashboard') }}"
                           class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('mahasiswa.dashboard') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            Beranda
                        </a>
                        <a href="{{ route('mahasiswa.fasilitas') }}"
                           class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('mahasiswa.fasilitas') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            Daftar Fasilitas
                        </a>
                        <a href="{{ route('mahasiswa.form-peminjaman', 'BARANG') }}"
                           class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('mahasiswa.form-peminjaman') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Ajukan Peminjaman
                        </a>
                        <a href="{{ route('mahasiswa.peminjaman-saya') }}"
                           class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('mahasiswa.peminjaman-saya', 'mahasiswa.detail-peminjaman', 'mahasiswa.qr-code') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                            Peminjaman Saya
                        </a>
                        <a href="{{ route('mahasiswa.riwayat') }}"
                           class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('mahasiswa.riwayat') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Riwayat
                        </a>

                    @elseif(Auth::user()->role === 'dosen')
                        <!-- Dosen Nav -->
                        <a href="{{ route('dosen.dashboard') }}"
                           class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('dosen.dashboard') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            Beranda Dosen
                        </a>
                        <a href="{{ route('dosen.pengajuan-masuk') }}"
                           class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('dosen.pengajuan-masuk', 'dosen.detail-pengajuan') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Pengajuan Masuk
                        </a>
                        <a href="{{ route('dosen.pindai-qr') }}"
                           class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('dosen.pindai-qr') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            Pindai QR Code
                        </a>
                        <a href="{{ route('dosen.riwayat-keputusan') }}"
                           class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('dosen.riwayat-keputusan') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                            Riwayat Keputusan
                        </a>

                    @else
                        <!-- Admin Nav -->
                        <a href="{{ route('admin.dashboard') }}"
                           class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            Beranda Admin
                        </a>
                        <a href="{{ route('admin.kelola-fasilitas') }}"
                           class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.kelola-fasilitas') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            Kelola Fasilitas
                        </a>
                        <a href="{{ route('admin.transaksi-berjalan') }}"
                           class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.transaksi-berjalan') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            Transaksi Berjalan
                        </a>
                        <a href="{{ route('admin.riwayat-transaksi') }}"
                           class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.riwayat-transaksi') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Riwayat Transaksi
                        </a>
                        <a href="{{ route('admin.kelola-pengguna') }}"
                           class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.kelola-pengguna') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Data Pengguna
                        </a>
                    @endif

                    <div class="pt-4 mt-4 border-t border-white/10">
                        <a href="{{ route('profile.index') }}"
                           class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('profile.index') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Profil Saya
                        </a>
                    </div>
                @else
                    <a href="{{ route('home') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-white/10">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Beranda
                    </a>
                    <a href="{{ route('fasilitas.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-white/10">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Katalog Fasilitas
                    </a>
                    <a href="{{ route('login') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-white/10">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        Masuk
                    </a>
                @endauth
            </nav>

            <!-- User Info & Logout Footer -->
            <div class="p-4 border-t border-white/10 bg-[#070f26]/60 flex items-center justify-between">
                @auth
                    <div class="flex items-center space-x-3 overflow-hidden">
                        <div class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                            {{ strtoupper(substr(Auth::user()->nama, 0, 1)) }}
                        </div>
                        <div class="truncate">
                            <div class="text-sm font-semibold text-white truncate">{{ Auth::user()->nama }}</div>
                            <div class="text-xs text-slate-400 capitalize">{{ Auth::user()->role }}</div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" title="Keluar dari sistem" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-white/5 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </button>
                    </form>
                @else
                    <div class="text-xs text-slate-400">
                        Silakan <a href="{{ route('login') }}" class="text-blue-400 font-bold hover:underline">Masuk</a> ke akun Anda.
                    </div>
                @endauth
            </div>
        </aside>

        <!-- MAIN CONTENT AREA -->
        <div class="flex-1 md:pl-64 flex flex-col min-h-screen">
            <!-- Top Navbar Header -->
            <header class="bg-white border-b border-slate-200 sticky top-0 z-30 px-4 sm:px-6 py-3.5 flex items-center justify-between shadow-xs">
                <div class="flex items-center space-x-3">
                    <button type="button" onclick="toggleSidebar()" class="md:hidden p-2 text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <div>
                        <h1 class="text-lg font-bold text-slate-900 leading-tight">@yield('page_title', 'SIPAFKA')</h1>
                        <p class="text-xs text-slate-500">Program Studi Ilmu Komputer Universitas Halu Oleo</p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <!-- Quick Time Indicator (WITA) -->
                    <div class="hidden sm:flex items-center gap-1.5 text-xs text-slate-500 bg-slate-100 px-3 py-1.5 rounded-full font-medium">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span id="current-clock">{{ now()->format('H:i') }} WITA</span>
                    </div>

                    <!-- User Pill Profile or Login Button -->
                    @auth
                        <a href="{{ route('profile.index') }}" class="flex items-center gap-2 pl-2 pr-3 py-1 bg-slate-50 hover:bg-slate-100 rounded-full border border-slate-200 transition-colors">
                            <div class="w-7 h-7 rounded-full bg-blue-600 text-white font-semibold text-xs flex items-center justify-center">
                                {{ strtoupper(substr(Auth::user()->nama, 0, 1)) }}
                            </div>
                            <span class="text-xs font-semibold text-slate-700 hidden sm:inline max-w-[120px] truncate">
                                {{ Auth::user()->nama }}
                            </span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-1.5 rounded-full bg-blue-600 text-white font-bold text-xs hover:bg-blue-700 transition-colors">
                            Masuk
                        </a>
                    @endauth
                </div>
            </header>

            <!-- Alerts Banner (Success & Error Notifications) -->
            <div class="px-4 sm:px-6 pt-4 max-w-7xl w-full mx-auto">
                @if(session('success'))
                    <div class="mb-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start shadow-xs">
                        <svg class="w-5 h-5 text-emerald-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="text-sm font-medium flex-1">{{ session('success') }}</div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start shadow-xs">
                        <svg class="w-5 h-5 text-rose-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="text-sm font-medium flex-1">{{ session('error') }}</div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 shadow-xs">
                        <div class="flex items-center font-semibold text-sm mb-1 text-amber-900">
                            <svg class="w-5 h-5 text-amber-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Terdapat beberapa kesalahan input:
                        </div>
                        <ul class="list-disc list-inside text-xs space-y-0.5 text-amber-800 pl-6">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <!-- Page Body Content -->
            <main class="flex-1 px-4 sm:px-6 pb-12 max-w-7xl w-full mx-auto">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="mt-auto border-t border-slate-200 bg-white py-4 px-6 text-center text-xs text-slate-500">
                &copy; {{ date('Y') }} <strong>SIPAFKA</strong> - Sistem Informasi Peminjaman Fasilitas Kampus. Program Studi Ilmu Komputer, Universitas Halu Oleo.
            </footer>
        </div>
    </div>

    <!-- Mobile Sidebar Backdrop -->
    <div id="sidebar-backdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/50 z-30 hidden md:hidden transition-opacity"></div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
        }

        // Live Clock
        setInterval(function() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const clockEl = document.getElementById('current-clock');
            if (clockEl) clockEl.innerText = `${h}:${m} WITA`;
        }, 30000);
    </script>

    @stack('scripts')
</body>
</html>
