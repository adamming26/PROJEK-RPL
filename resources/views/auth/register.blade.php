<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Mahasiswa - SIPAFKA UHO</title>
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
            <a href="{{ route('login') }}" class="text-white hover:text-blue-300 font-medium">Masuk</a>
        </nav>
    </header>

    <!-- Main Section (2 Columns: Card Left, Hero Right) -->
    <main class="relative z-10 px-4 sm:px-8 py-8 max-w-7xl w-full mx-auto flex-1 flex items-center">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center w-full">

            <!-- Left Form Card (Exact Style of Bab III Fig 3.7) -->
            <div class="lg:col-span-6 flex justify-center lg:justify-start order-2 lg:order-1">
                <div class="w-full max-w-lg bg-white rounded-3xl p-8 sm:p-10 shadow-2xl border border-slate-100">
                    <div class="mb-6">
                        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Daftar akun</h2>
                        <p class="text-sm text-slate-500 mt-1">Lengkapi data berikut.</p>
                    </div>

                    @if($errors->any())
                        <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label for="nama" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama lengkap</label>
                            <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required autofocus
                                   placeholder="Nama lengkap"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all placeholder:text-slate-400">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="nomor_induk" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">NIM</label>
                                <input type="text" id="nomor_induk" name="nomor_induk" value="{{ old('nomor_induk') }}" required
                                       placeholder="Nomor induk mahasiswa"
                                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all placeholder:text-slate-400">
                            </div>
                            <div>
                                <label for="nomor_hp" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nomor HP</label>
                                <input type="text" id="nomor_hp" name="nomor_hp" value="{{ old('nomor_hp') }}"
                                       placeholder="08xxxxxxxxxx"
                                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all placeholder:text-slate-400">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                       placeholder="name@email.com"
                                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all placeholder:text-slate-400">
                            </div>
                            <div>
                                <label for="prodi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Program Studi</label>
                                <input type="text" id="prodi" name="prodi" value="{{ old('prodi', 'Ilmu Komputer') }}" required readonly
                                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-600 text-sm focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kata sandi</label>
                            <input type="password" id="password" name="password" required
                                   placeholder="Buat kata sandi minimal 6 karakter"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all placeholder:text-slate-400">
                        </div>

                        <button type="submit" class="w-full py-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-lg shadow-blue-600/30 transition-all transform hover:-translate-y-0.5 mt-2">
                            Daftar
                        </button>
                    </form>

                    <div class="mt-6 text-center text-xs text-slate-500">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="text-blue-600 font-bold hover:underline ml-1">Masuk</a>
                    </div>
                </div>
            </div>

            <!-- Right Hero Description -->
            <div class="lg:col-span-6 text-white pl-0 lg:pl-8 order-1 lg:order-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-semibold uppercase tracking-wider mb-4">
                    Pendaftaran Mahasiswa
                </div>
                <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight leading-tight mb-4 text-white">
                    Buat akun untuk meminjam fasilitas Prodi Ilmu Komputer.
                </h1>
                <p class="text-base sm:text-lg text-slate-200/90 leading-relaxed max-w-xl">
                    Cukup beberapa data, lalu kamu bisa mengajukan peminjaman kapan saja. Data otomatis terhubung dengan sistem persetujuan Dosen.
                </p>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="relative z-10 px-6 sm:px-12 py-4 text-center text-xs text-slate-400 border-t border-white/10">
        &copy; {{ date('Y') }} SIPAFKA UHO. Program Studi Ilmu Komputer Universitas Halu Oleo.
    </footer>
</body>
</html>
