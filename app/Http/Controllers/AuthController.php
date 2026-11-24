<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required' => 'Identitas akun (NIM / NIP / Email) wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $loginInput = $credentials['login'];
        $password = $credentials['password'];

        // Cari user berdasarkan email ATAU nomor induk (NIM/NIP/ID Admin)
        $user = User::where('email', $loginInput)
            ->orWhere('nomor_induk', $loginInput)
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return back()->withInput()->with('error', 'Kombinasi identitas akun dan kata sandi tidak cocok.');
        }

        if ($user->status !== 'aktif') {
            return back()->withInput()->with('error', 'Akun Anda saat ini dinonaktifkan. Silakan hubungi Administrator.');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return $this->redirectBasedOnRole($user)
            ->with('success', "Selamat datang kembali, {$user->nama}!");
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nomor_induk' => ['required', 'string', 'max:50', 'unique:users,nomor_induk'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'nomor_hp' => ['nullable', 'string', 'max:20'],
            'prodi' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nomor_induk.required' => 'NIM wajib diisi.',
            'nomor_induk.unique' => 'NIM tersebut sudah terdaftar di sistem SIPAFKA.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email tersebut sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal terdiri dari 6 karakter.',
        ]);

        $user = User::create([
            'nama' => $validated['nama'],
            'nomor_induk' => $validated['nomor_induk'],
            'email' => $validated['email'],
            'nomor_hp' => $validated['nomor_hp'] ?? null,
            'prodi' => $validated['prodi'],
            'password' => Hash::make($validated['password']),
            'role' => 'mahasiswa', // Registrasi publik otomatis menjadi Mahasiswa
            'status' => 'aktif',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('mahasiswa.dashboard')
            ->with('success', "Pendaftaran berhasil! Selamat datang di SIPAFKA, {$user->nama}.");
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }

    public function showForgotPassword()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.forgot-password');
    }

    public function processForgotPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'nomor_induk' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'nomor_induk.required' => 'Nomor Induk (NIM / NIP) wajib diisi untuk verifikasi identitas.',
        ]);

        $user = User::where('email', $request->email)
            ->where('nomor_induk', $request->nomor_induk)
            ->first();

        if (!$user) {
            return back()->withInput()->with('error', 'Kombinasi Email dan Nomor Induk (NIM / NIP) tidak ditemukan di sistem SIPAFKA.');
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        return redirect()->route('password.reset', ['token' => $token, 'email' => $user->email])
            ->with('success', 'Identitas terverifikasi! Silakan buat kata sandi baru Anda.');
    }

    public function showResetPassword(Request $request, $token)
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        $email = $request->query('email');

        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', $token)
            ->first();

        if (!$record) {
            return redirect()->route('password.request')
                ->with('error', 'Tautan pengaturan ulang sandi tidak valid atau telah kedaluwarsa.');
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'token.required' => 'Token reset sandi tidak valid.',
            'email.required' => 'Email wajib diisi.',
            'email.exists' => 'Email tidak terdaftar.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$record) {
            return redirect()->route('password.request')
                ->with('error', 'Sesi reset kata sandi tidak valid atau telah kedaluwarsa. Silakan ajukan ulang.');
        }

        // Update password pengguna
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        // Hapus token yang sudah terpakai
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')
            ->with('success', 'Kata sandi berhasil diperbarui! Silakan masuk dengan kata sandi baru Anda.');
    }

    public function showProfile()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id_user . ',id_user'],
            'nomor_hp' => ['nullable', 'string', 'max:20'],
        ], [
            'nama.required' => 'Nama lengkap tidak boleh kosong.',
            'email.required' => 'Email tidak boleh kosong.',
            'email.unique' => 'Email sudah digunakan oleh akun lain.',
        ]);

        $user->update([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'nomor_hp' => $validated['nomor_hp'],
        ]);

        return back()->with('success', 'Profil Anda berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:6'],
            'new_password_confirmation' => ['required', 'same:new_password'],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'new_password.required' => 'Kata sandi baru wajib diisi.',
            'new_password.min' => 'Kata sandi baru minimal 6 karakter.',
            'new_password_confirmation.same' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->with('error', 'Kata sandi saat ini tidak tepat.');
        }

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return back()->with('success', 'Kata sandi Anda berhasil diubah.');
    }

    private function redirectBasedOnRole(User $user)
    {
        return match ($user->role) {
            'mahasiswa' => redirect()->route('mahasiswa.dashboard'),
            'dosen' => redirect()->route('dosen.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect()->route('home'),
        };
    }
}
