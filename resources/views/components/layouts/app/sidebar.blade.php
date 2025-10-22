<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" >
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white/5 dark:bg-black/50 ">
        <style>
            @keyframes float1 {
                0%, 100% { transform: translate(0, 0) rotate(0deg); }
                25% { transform: translate(100px, -50px) rotate(90deg); }
                50% { transform: translate(-50px, -100px) rotate(180deg); }
                75% { transform: translate(-100px, 50px) rotate(270deg); }
            }
            @keyframes float2 {
                0%, 100% { transform: translate(0, 0) rotate(0deg); }
                33% { transform: translate(-80px, 60px) rotate(120deg); }
                66% { transform: translate(120px, -40px) rotate(240deg); }
            }
            .blur1 { animation: float1 20s ease-in-out infinite; }
            .blur2 { animation: float2 15s ease-in-out infinite; }
        </style>

        <!-- Animated Background Blurs -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div class="blur1 absolute w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl top-10 left-10"></div>
            <div class="blur2 absolute w-80 h-80 bg-purple-500/20 rounded-full blur-3xl top-1/3 right-20"></div>
            <div class="blur1 absolute w-72 h-72 bg-blue-500/40 rounded-full blur-3xl bottom-20 left-1/3" style="animation-delay: -5s;"></div>
            <div class="blur2 absolute w-64 h-64 bg-pink-500/10 rounded-full blur-3xl bottom-10 right-10" style="animation-delay: -10s;"></div>
        </div>
        <!-- Header for All Pages -->
        <div class="fixed top-0 left-0 z-50 w-full border-b border-white/10 bg-zinc-300/50 dark:bg-black/50 backdrop-blur-sm">
            <div class="flex items-center justify-between p-4">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2" wire:navigate>
                    <h1 class="text-2xl font-bold text-white" style="text-shadow: 0 0 20px rgba(0, 0, 0, 0.54);">
                        LinkLens<span class="text-pink-400">AI</span>
                    </h1>
                </a>

                @auth
                <div class="relative">
                    <button onclick="toggleMenu()" class="flex items-center space-x-2 px-3 py-2 rounded-lg bg-white/10 hover:bg-white/20 transition-colors">
                        <div class="w-8 h-8 bg-neutral-200 dark:bg-neutral-700 rounded-lg flex items-center justify-center">
                            <span class="text-sm font-medium text-black dark:text-white">{{ auth()->user()->initials() }}</span>
                        </div>
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div id="userMenu" class="hidden absolute z-50 right-0 mt-2 w-64 bg-white/90 dark:bg-zinc-800/90 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 backdrop-blur-sm">
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-neutral-200 dark:bg-neutral-700 rounded-lg flex items-center justify-center">
                                    <span class="text-sm font-medium text-black dark:text-white">{{ auth()->user()->initials() }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ auth()->user()->email }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="py-2">
                            <a href="{{ route('settings.profile') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Settings
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endauth
            </div>
        </div>

        <div class="pt-20">
            {{ $slot }}
        </div>


        <script>
            function toggleMenu() {
                const menu = document.getElementById('userMenu');
                menu.classList.toggle('hidden');
            }

            document.addEventListener('click', function(event) {
                const menu = document.getElementById('userMenu');
                const button = event.target.closest('button[onclick="toggleMenu()"]');
                if (!button && menu && !menu.contains(event.target)) {
                    menu.classList.add('hidden');
                }
            });
        </script>

        <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
        <script>
            window.Pusher = Pusher;
            import Echo from 'laravel-echo';
            window.Echo = new Echo({
                broadcaster: 'reverb',
                key: '{{ config('broadcasting.connections.reverb.key') }}',
                wsHost: '{{ config('broadcasting.connections.reverb.host') }}',
                wsPort: {{ config('broadcasting.connections.reverb.port') }},
                wssPort: {{ config('broadcasting.connections.reverb.port') }},
                forceTLS: false,
                enabledTransports: ['ws', 'wss'],
            });
        </script>

        @livewireScripts
    </body>
</html>
