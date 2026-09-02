<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in - Ruang Pustaka</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#F8FAFC] text-[#0F172A] min-h-screen flex flex-col justify-center items-center p-4">

    <!-- Logo & Header -->
    <div class="mb-6 text-center">
        <a href="{{ url('/') }}" class="text-3xl font-bold text-[#2563EB]">Ruang Pustaka</a>
        <p class="text-sm text-slate-500 mt-1">Masuk ke akun kamu untuk melanjutkan</p>
    </div>

    <!-- Card Login -->
    <div class="w-full max-w-md bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm">
        
        <!-- Session Status (Misal setelah reset pass/logout) -->
        @if (session('status'))
            <div class="mb-4 text-sm font-medium text-green-600 bg-green-50 p-3 rounded-lg border border-green-200">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#2563EB] focus:border-[#2563EB] outline-none transition text-sm">
                @error('email')
                    <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <div class="flex justify-between items-center mb-1">
                    <label for="password" class="block text-sm font-semibold text-slate-700">Password</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs text-[#2563EB] hover:underline font-medium">Lupa password?</a>
                    @endif
                </div>
                <input id="password" type="password" name="password" required
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#2563EB] focus:border-[#2563EB] outline-none transition text-sm">
                @error('password')
                    <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 text-[#2563EB] border-slate-300 rounded focus:ring-[#2563EB]">
                <label for="remember_me" class="ml-2 text-sm text-slate-600">Ingat Saya</label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full bg-[#2563EB] hover:bg-blue-700 text-white font-semibold py-2.5 rounded-xl transition shadow-sm text-sm">
                Log in
            </button>
        </form>

        <!-- Footer Link -->
        <div class="mt-6 text-center text-xs text-slate-500">
            Belum punya akun? 
            <a href="{{ route('register') }}" class="text-[#2563EB] font-semibold hover:underline">Daftar sekarang</a>
        </div>
    </div>

</body>
</html>