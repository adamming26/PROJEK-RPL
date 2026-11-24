<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - SIPAFKA UHO</title>
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
<body class="font-sans antialiased text-slate-900 min-h-screen relative flex flex-col justify-between overflow-x-hidden">

    <!-- Background Image with Overlay Matching Report Bab III -->
    <div class="fixed inset-0 z-0 bg-cover bg-center" style="background-image: linear-gradient(135deg, rgba(12, 26, 64, 0.92) 0%, rgba(20, 48, 115, 0.88) 100%), url('https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=1920&q=80');">
    </div>

    <!-- Header Navigation -->
    <header class="relative z-10 px-6 sm:px-12 py-6 flex items-center justify-between max-w-7xl w-full mx-auto">
        <a href="{{ route('home') }}" class="flex items-center space-x-3 text-white">
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
        </a>

        <nav class="flex items-center space-x-4 text-sm text-white">
            <a href="{{ route('home') }}" class="text-blue-200 hover:text-white transition-colors">Beranda</a>
            <a href="{{ route('fasilitas.index') }}" class="text-blue-200 hover:text-white transition-colors">Fasilitas</a>
        </nav>
    </header>

    <!-- Main Section (2 Columns: Hero Left, Card Right) -->
    <main class="relative z-10 px-4 sm:px-8 py-8 max-w-7xl w-full mx-auto flex-1 flex items-center">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center w-full">

            <!-- Left Description -->
            <div class="lg:col-span-7 text-white pr-0 lg:pr-8">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-semibold uppercase tracking-wider mb-4">
                    Layanan Terpadu
                </div>
                <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight leading-tight mb-4 text-white">
                    Kelola peminjaman fasilitas <br class="hidden sm:inline">
                    Prodi Ilmu Komputer dalam satu tempat.
                </h1>
                <p class="text-base sm:text-lg text-slate-200/90 leading-relaxed max-w-xl mb-6">
                    Mahasiswa mengajukan dan memantau, dosen memeriksa dan menyetujui, admin mengelola fasilitas dan memantau transaksi.
                </p>
            </div>

            <!-- Right Form Card (Exact Style of Bab III Fig 3.7) -->
            <div class="lg:col-span-5 flex justify-center lg:justify-end">
                <div class="w-full max-w-md bg-white rounded-3xl p-8 sm:p-10 shadow-2xl border border-slate-100">
                    <div class="mb-6">
                        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Masuk ke akun</h2>
                        <p class="text-sm text-slate-500 mt-1">Gunakan akun sesuai peran Anda.</p>
                    </div>

                    @if(session('error'))
                        <div class="mb-4 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium flex items-center gap-2">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="mb-4 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-medium flex items-center gap-2">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label for="login" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">NIM / NIP / Email</label>
                            <input type="text" id="login" name="login" value="{{ old('login') }}" required autofocus
                                   placeholder="Masukkan identitas akun"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all placeholder:text-slate-400">
                            @error('login')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kata sandi</label>
                            <input type="password" id="password" name="password" required
                                   placeholder="Masukkan kata sandi"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all placeholder:text-slate-400">
                            @error('password')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between text-xs text-slate-500 pt-1">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="remember" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                <span>Ingat saya</span>
                            </label>
                            <a href="{{ route('password.request') }}" class="text-blue-600 font-semibold hover:underline">
                                Lupa kata sandi?
                            </a>
                        </div>

                        <button type="submit" class="w-full py-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-lg shadow-blue-600/30 transition-all transform hover:-translate-y-0.5">
                            Masuk
                        </button>
                    </form>

                    <div class="mt-6 text-center text-xs text-slate-500">
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="text-blue-600 font-bold hover:underline ml-1">Daftar</a>
                    </div>

                    <div class="mt-5 p-3 rounded-xl bg-slate-50 border border-slate-100 text-[11px] text-slate-500 text-center leading-relaxed">
                        Setelah masuk, kamu diarahkan ke halaman sesuai hak akses.
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="relative z-10 px-6 sm:px-12 py-4 text-center text-xs text-slate-400 border-t border-white/10">
        &copy; {{ date('Y') }} SIPAFKA UHO. Program Studi Ilmu Komputer Universitas Halu Oleo.
    </footer>
</body>
</html>
