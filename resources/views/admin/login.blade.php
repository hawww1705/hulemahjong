<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login — Hule Mahjong CMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#c3b091] min-h-screen flex items-center justify-center p-4">

    <div class="bg-white rounded-[28px] max-w-[430px] w-full p-7 sm:p-9 shadow-2xl border border-white/60">
        <div class="text-center mb-7">
            <a href="{{ route('home') }}" class="inline-flex items-baseline text-[24px] tracking-[-0.03em] text-[#141413]">
                <span class="font-normal">Hule</span><span class="font-bold">Mahjong</span>
            </a>
            <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-[#8c734b] mt-2">Management CMS Portal</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-xs text-red-700 font-medium">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-4">
            @csrf
            <div>
                <label class="cms-label">Admin Email</label>
                <input type="email" id="login-email" name="email" value="{{ old('email', 'admin@hulemahjong.id') }}" required class="cms-input">
            </div>

            <div>
                <label class="cms-label">Password</label>
                <input type="password" id="login-password" name="password" value="password123" required class="cms-input">
            </div>

            <div class="flex items-center justify-between text-xs text-stone-600 pt-1">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" checked class="rounded border-stone-300">
                    <span>Keep me signed in</span>
                </label>
                <button type="button" onclick="document.getElementById('login-email').value='admin@hulemahjong.id';document.getElementById('login-password').value='password123';"
                        class="text-[#8c734b] font-semibold hover:underline">
                    Fill Demo Login
                </button>
            </div>

            <button type="submit" class="w-full h-[46px] rounded-full bg-[#111111] hover:bg-[#c3b091] text-white hover:text-[#141310] font-semibold text-[13.5px] transition mt-2">
                Sign In to CMS →
            </button>
        </form>

        <div class="mt-6 pt-5 border-t border-stone-100 text-center">
            <a href="{{ route('home') }}" class="text-xs font-medium text-stone-500 hover:text-stone-900">← Back to Booking Website</a>
        </div>
    </div>

</body>
</html>
