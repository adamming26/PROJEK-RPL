<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPAFKA - Sistem Informasi Peminjaman Fasilitas Kampus Prodi Ilmu Komputer UHO</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: { 950: '#070f26', 900: '#0c1a40', 800: '#13285c', 700: '#1b3b87' },
                        primary: { 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8' }
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
</head>
<body class="font-sans antialiased text-white min-h-screen relative flex flex-col justify-between overflow-x-hidden">

    <!-- Background Image with Overlay Matching Report Bab III -->
    <div class="fixed inset-0 z-0 bg-cover bg-center" style="background-image: linear-gradient(135deg, rgba(12, 26, 64, 0.92) 0%, rgba(20, 48, 115, 0.85) 100%), url('https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=1920&q=80');">
    </div>

    <!-- Header Navigation -->
    <header class="relative z-10 px-6 sm:px-12 py-6 flex items-center justify-between max-w-7xl w-full mx-auto">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center font-extrabold text-blue-400 text-lg shadow-lg">
                S
            </div>
            <div>
                <div class="font-bold text-lg tracking-tight text-white flex items-center gap-2">
                    SIPAFKA
                    <span class="text-[10px] bg-blue-500/30 text-blue-300 font-semibold px-2 py-0.5 rounded-full border border-blue-400/30">UHO</span>
                </div>
                <div class="text-xs text-blue-200/80">Prodi Ilmu Komputer</div>
            </div>
        </div>

        <nav class="flex items-center space-x-3 sm:space-x-6 text-sm">
            <a href="{{ route('home') }}" class="text-white font-medium hover:text-blue-300 transition-colors hidden sm:inline">Beranda</a>
            <a href="{{ route('fasilitas.index') }}" class="text-blue-200 hover:text-white transition-colors">Fasilitas</a>
            @auth
                <a href="{{ route(Auth::user()->role . '.dashboard') }}" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 font-semibold text-white shadow-lg shadow-blue-600/30 transition-all">
                    Dashboard ({{ Auth::user()->nama }})
                </a>
            @else
                <a href="{{ route('login') }}" class="px-5 py-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 font-semibold text-white backdrop-blur-sm transition-all">
                    Masuk
                </a>
                <a href="{{ route('register') }}" class="px-5 py-2 rounded-xl bg-white text-slate-900 hover:bg-slate-100 font-bold shadow-lg shadow-black/10 transition-all hidden sm:inline">
                    Daftar
                </a>
            @endauth
        </nav>
    </header>

    <!-- Hero Content -->
    <main class="relative z-10 px-6 sm:px-12 py-12 max-w-7xl w-full mx-auto flex-1 flex flex-col justify-center">
        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-semibold uppercase tracking-wider mb-6 backdrop-blur-sm">
                <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                Layanan Prodi Ilmu Komputer
            </div>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white tracking-tight leading-tight sm:leading-none mb-6">
                Pinjam Fasilitas Prodi <br class="hidden sm:inline">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-300 via-blue-200 to-indigo-200">Ilmu Komputer</span> dengan Mudah
            </h1>

            <p class="text-base sm:text-lg text-slate-200/90 font-normal leading-relaxed mb-8 max-w-2xl">
                Ajukan peminjaman laboratorium komputer dan fasilitas barang seperti proyektor dan kabel secara online, tanpa antre dan tanpa berkas manual.
            </p>

            <div class="flex flex-wrap items-center gap-4 mb-14">
                <a href="{{ route('mahasiswa.form-peminjaman', 'BARANG') }}" class="px-7 py-3.5 rounded-xl bg-white text-[#0c1a40] hover:bg-slate-100 font-bold shadow-xl shadow-black/20 transition-all transform hover:-translate-y-0.5">
                    Ajukan Peminjaman
                </a>
                <a href="{{ route('fasilitas.index') }}" class="px-7 py-3.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/25 text-white font-semibold backdrop-blur-md transition-all">
                    Lihat Fasilitas
                </a>
            </div>
        </div>

        <!-- Two Category Cards (Matches Bab III Wireframe) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl">
            <!-- Card Lab Komputer -->
            <div class="p-6 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 hover:bg-white/15 transition-all">
                <div class="w-12 h-12 rounded-xl bg-blue-500/20 border border-blue-400/30 flex items-center justify-center text-blue-300 mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Laboratorium Komputer</h3>
                <p class="text-sm text-slate-300 leading-relaxed mb-4">
                    Untuk praktikum dan kegiatan akademik di luar jadwal reguler dengan pemeriksaan otomatis bentrok jadwal.
                </p>
                <div class="flex flex-wrap items-center gap-2 text-xs text-blue-200">
                    <span class="px-2.5 py-1 rounded-lg bg-white/10">Lab Komputer Basis Data (50 Kursi)</span>
                    <span class="px-2.5 py-1 rounded-lg bg-white/10">Lab Komputer Unit Teknologi Informasi (50 Kursi)</span>
                </div>
            </div>

            <!-- Card Fasilitas Barang -->
            <div class="p-6 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 hover:bg-white/15 transition-all">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center text-emerald-300 mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Fasilitas Barang</h3>
                <p class="text-sm text-slate-300 leading-relaxed mb-4">
                    Perlengkapan penunjang perkuliahan yang bisa dipinjam dengan validasi token QR Code dan update stok instan.
                </p>
                <div class="flex flex-wrap items-center gap-2 text-xs text-emerald-200">
                    <span class="px-2.5 py-1 rounded-lg bg-white/10">Proyektor (2 Unit)</span>
                    <span class="px-2.5 py-1 rounded-lg bg-white/10">Kabel HDMI (2 Unit)</span>
                    <span class="px-2.5 py-1 rounded-lg bg-white/10">Kabel Terminal Cabang (5 Unit)</span>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="relative z-10 px-6 sm:px-12 py-6 text-center text-xs text-slate-400 border-t border-white/10">
        &copy; {{ date('Y') }} SIPAFKA - Sistem Informasi Peminjaman Fasilitas Kampus. Program Studi Ilmu Komputer, FMIPA Universitas Halu Oleo.
    </footer>
</body>
</html>
