<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-black text-white min-h-screen">
    <style>
        @keyframes float1 {

            0%,
            100% {
                transform: translate(0, 0) rotate(0deg);
            }

            25% {
                transform: translate(100px, -50px) rotate(90deg);
            }

            50% {
                transform: translate(-50px, -100px) rotate(180deg);
            }

            75% {
                transform: translate(-100px, 50px) rotate(270deg);
            }
        }

        @keyframes float2 {

            0%,
            100% {
                transform: translate(0, 0) rotate(0deg);
            }

            33% {
                transform: translate(-80px, 60px) rotate(120deg);
            }

            66% {
                transform: translate(120px, -40px) rotate(240deg);
            }
        }

        .blur1 {
            animation: float1 20s ease-in-out infinite;
        }

        .blur2 {
            animation: float2 15s ease-in-out infinite;
        }
    </style>

    <!-- Animated Background Blurs -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="blur1 absolute w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl top-10 left-10"></div>
        <div class="blur2 absolute w-80 h-80 bg-purple-500/15 rounded-full blur-3xl top-1/3 right-20"></div>
        <div class="blur1 absolute w-72 h-72 bg-blue-500/20 rounded-full blur-3xl bottom-20 left-1/3"
            style="animation-delay: -5s;"></div>
        <div class="blur2 absolute w-64 h-64 bg-pink-500/15 rounded-full blur-3xl bottom-10 right-10"
            style="animation-delay: -10s;"></div>
    </div>

    <div class="relative min-h-screen flex items-center justify-center p-6">
        <div class="w-full max-w-md relative z-10">
            <div class="text-center mb-8">
                <a href="{{ route('home') }}" class="inline-block" wire:navigate>
                    <h1 class="text-4xl font-bold mb-6 text-purple-200" style="text-shadow: 0 0 20px rgba(0,0,0,0.8);">
                        LinkLens<span class="text-pink-400">AI</span>
                    </h1>
                </a>
            </div>
            <div class="backdrop-blur-sm bg-white/70 dark:bg-white/5 rounded-2xl p-8 border border-white/10">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>

</html>
