<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LinkLens AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @fluxAppearance
</head>

<body class="bg-black text-white h-screen overflow-hidden snap-y snap-mandatory">
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

        @keyframes float3 {

            0%,
            100% {
                transform: translate(0, 0) rotate(0deg);
            }

            50% {
                transform: translate(60px, 80px) rotate(180deg);
            }
        }

        .blur1 {
            animation: float1 20s ease-in-out infinite;
        }

        .blur2 {
            animation: float2 15s ease-in-out infinite;
        }

        .blur3 {
            animation: float3 25s ease-in-out infinite;
        }
    </style>

    <!-- Animated Background Blurs -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="blur1 absolute w-96 h-96 bg-indigo-500/30 rounded-full blur-3xl top-10 left-10"></div>
        <div class="blur1 absolute w-64 h-64 bg-cyan-500/20 rounded-full blur-3xl top-1/2 left-10"></div>
        <div class="blur2 absolute w-96 h-96 bg-pink-500/15 rounded-full blur-3xl bottom-1/2 right-40"></div>
        <div
            class="blur3 absolute w-96 h-96 bg-sky-600/15 rounded-full blur-3xl top-1/3 left-1/3 transform -translate-x-1/2 -translate-y-1/2">
        </div>
        <div class="blur2 absolute w-80 h-80 bg-purple-500/20 rounded-full blur-3xl top-1/3 right-20"></div>
        <div class="blur3 absolute w-72 h-72 bg-blue-500/25 rounded-full blur-3xl bottom-20 left-1/3"></div>
        <div class="blur1 absolute w-64 h-64 bg-cyan-500/20 rounded-full blur-3xl bottom-10 right-10"
            style="animation-delay: -5s;"></div>
        <div class="blur2 absolute w-88 h-88 bg-pink-500/15 rounded-full blur-3xl top-1/2 left-1/2"
            style="animation-delay: -10s;"></div>
    </div>

    <!-- Hero Section -->
    <section id="hero" class="relative h-screen flex items-center justify-center snap-start">
        <div class="container mx-auto px-4 py-16 relative z-10">
            <div class="text-center">
                <h1
                    class="text-7xl md:text-8xl font-bold mb-6 bg-gradient-to-r from-white via-purple-300 to-indigo-900 bg-clip-text text-transparent">
                    LinkLens<span class="text-pink-400">AI</span>
                </h1>
                <p class="text-2xl text-gray-300 mb-12 max-w-3xl mx-auto leading-relaxed">
                    AI-powered LinkedIn automation that manages your professional presence with intelligent posting and
                    connection filtering.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @auth
                        <a href="{{ route('dashboard') }}"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-10 py-4 rounded-xl font-semibold text-lg transition-all transform hover:scale-105">
                            Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-10 py-4 rounded-xl font-semibold text-lg transition-all transform hover:scale-105">
                            Get Started
                        </a>
                        <a href="{{ route('register') }}"
                            class="border-2 border-indigo-400 hover:bg-indigo-400 hover:text-black text-white px-10 py-4 rounded-xl font-semibold text-lg transition-all transform hover:scale-105">
                            Sign Up
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features"
        class="relative h-screen w-full flex flex-col items-center justify-center overflow-hidden snap-start pt-12">
        <h1 class="text-4xl sm:text-5xl lg:text-6xl mt-10 font-extrabold tracking-tight text-white text-center mb-8">
            <span class="text-white">Exciting</span>
            <span class="text-sky-400">Features</span>
        </h1>

        <div class=" container mx-auto px-4 sm:px-6 py-12 md:py-16 lg:py-20">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center backdrop-blur-sm bg-white/5 rounded-2xl p-8 border border-white/10">
                    <div class="flex justify-center mb-4">
                        <x-gmdi-group-add class="w-16" />
                    </div>
                    <h3 class="text-2xl font-semibold mb-4 text-indigo-300">Smart Connections</h3>
                    <p class="text-gray-300">Auto-accept connections based on your custom filters</p>
                </div>
                <div class="text-center backdrop-blur-sm bg-white/5 rounded-2xl p-8 border border-white/10">
                    <div class="flex justify-center mb-4">
                        <x-gmdi-post-add class="w-16" />
                    </div>
                    <h3 class="text-2xl font-semibold mb-4 text-purple-300">AI Posts</h3>
                    <p class="text-gray-300">Generate engaging LinkedIn content automatically</p>
                </div>
                <div class="text-center backdrop-blur-sm bg-white/5 rounded-2xl p-8 border border-white/10">
                    <div class="flex justify-center mb-4">
                        <x-carbon-analytics class="w-16" />
                    </div>
                    <h3 class="text-2xl font-semibold mb-4 text-cyan-300">Analytics</h3>
                    <p class="text-gray-300">Track your LinkedIn engagement and growth</p>
                </div>
            </div>
        </div>
    </section>
    @fluxScripts
</body>

</html>
