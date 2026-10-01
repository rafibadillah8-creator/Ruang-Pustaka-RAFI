<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar — Ruang Pustaka</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-[#fdfbf7] text-gray-900">

    <div class="flex min-h-screen">
        <!-- Left Pane (Image/Brand Side) -->
        <div class="hidden lg:flex lg:w-1/2 bg-[#3a261f] text-[#fdfbf7] flex-col justify-center relative overflow-hidden px-16 xl:px-24">
            <!-- Decorative Background -->
            <div class="absolute inset-0 bg-gradient-to-br from-[#3a261f] via-[#2c1d18] to-[#1e1411]"></div>
            <div class="absolute bottom-0 left-0 w-full h-1/2 bg-gradient-to-t from-[#8b5e3c]/10 to-transparent"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full border-[1px] border-[#c19b6c]/10"></div>
            <div class="absolute top-1/2 right-10 w-[500px] h-[500px] rounded-full border-[1px] border-[#c19b6c]/10 transform -translate-y-1/2 blur-md"></div>
            
            <div class="relative z-10 w-full max-w-lg">
                <div class="flex items-center gap-3 mb-16">
                    <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="h-8 w-8 object-contain">
                    <span class="text-2xl font-bold tracking-tight text-[#c19b6c]">Ruang Pustaka</span>
                </div>
                
                <h1 class="text-4xl xl:text-5xl font-bold mb-6 leading-[1.2]">Belajar lebih mudah, membaca lebih praktis!</h1>
                <p class="text-[#e8dcc4] text-lg">Temukan e-book pilihan untuk mendukung perkuliahan, pembelajaran, dan pengembangan pengetahuan.</p>
            </div>
            
            <div class="absolute bottom-8 left-16 xl:left-24 z-10 text-xs text-[#e8dcc4]/70">
                &copy; {{ date('Y') }} Ruang Pustaka. All rights reserved.
            </div>
        </div>

        <!-- Right Pane (Form Side) -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center items-center bg-[#fdfbf7] p-8 sm:p-12">
            <div class="w-full max-w-[400px]">
                
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-[#3a261f] mb-2">Create an account</h2>
                    <p class="text-sm text-gray-500">Sign up to get started</p>
                </div>

                @if ($errors->any())
                    <div class="mb-5 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 space-y-1">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium text-[#3a261f] mb-1">Full Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                               placeholder="Your name"
                               class="w-full bg-white border border-gray-300 focus:border-[#8b5e3c] rounded-lg px-4 py-2.5 text-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-[#8b5e3c]/20 transition">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-[#3a261f] mb-1">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                               placeholder="your.email@address"
                               class="w-full bg-white border border-gray-300 focus:border-[#8b5e3c] rounded-lg px-4 py-2.5 text-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-[#8b5e3c]/20 transition">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-[#3a261f] mb-1">Password</label>
                        <input id="password" type="password" name="password" required
                               placeholder="Create a password"
                               class="w-full bg-white border border-gray-300 focus:border-[#8b5e3c] rounded-lg px-4 py-2.5 text-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-[#8b5e3c]/20 transition">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-[#3a261f] mb-1">Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                               placeholder="Repeat password"
                               class="w-full bg-white border border-gray-300 focus:border-[#8b5e3c] rounded-lg px-4 py-2.5 text-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-[#8b5e3c]/20 transition">
                    </div>

                    <button type="submit"
                            class="w-full bg-[#3a261f] hover:bg-[#2c1d18] text-white font-medium py-2.5 rounded-lg transition shadow-sm mt-6">
                        Sign Up
                    </button>
                </form>

                <p class="text-center text-sm text-gray-500 mt-8">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="font-medium text-[#8b5e3c] hover:underline">Sign in</a>
                </p>
            </div>
        </div>
    </div>

</body>
</html>