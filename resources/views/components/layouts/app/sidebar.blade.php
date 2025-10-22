<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" >
    <head>
        @include('partials.head')
        @fluxAppearance
        @fluxScripts
    </head>
    <body class="min-h-screen bg-white dark:bg-black ">
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
            <div class="blur1 absolute w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl top-10 left-10"></div>
            <div class="blur2 absolute w-80 h-80 bg-purple-500/8 rounded-full blur-3xl top-1/3 right-20"></div>
            <div class="blur1 absolute w-72 h-72 bg-blue-500/10 rounded-full blur-3xl bottom-20 left-1/3" style="animation-delay: -5s;"></div>
            <div class="blur2 absolute w-64 h-64 bg-pink-500/8 rounded-full blur-3xl bottom-10 right-10" style="animation-delay: -10s;"></div>
        </div>
        @if(request()->routeIs('dashboard'))
            <flux:sidebar sticky stashable class="fixed h-screen border-e border-white/10 text-black dark:text-white bg-zinc-300/50 dark:bg-black/50 backdrop-blur-sm z-10">
                <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />
                <flux:sidebar.toggle class="hidden lg:block" icon="bars-3" />

                <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                    <h1 class="text-2xl font-bold text-white" style="text-shadow: 0 0 20px rgba(0, 0, 0, 0.54); dark:text-shadow: 0 0 20px rgba(0,0,0,0.8);">
                        LinkLens<span class="text-pink-400">AI</span>
                    </h1>
                </a>

                <flux:navlist variant="outline">
                    <flux:navlist.group :heading="__('Platform')" class="grid">
                        <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
                    </flux:navlist.group>
                </flux:navlist>

                <flux:spacer />

                <flux:dropdown class="hidden lg:block" position="bottom" align="start">
                    <flux:profile
                        :name="auth()->user()->name"
                        :initials="auth()->user()->initials()"
                        icon:trailing="chevrons-up-down"
                    />

                    <flux:menu class="w-[220px]">
                        <flux:menu.radio.group>
                            <div class="p-0 text-sm font-normal">
                                <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                    <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                        <span class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                            {{ auth()->user()->initials() }}
                                        </span>
                                    </span>
                                    <div class="grid flex-1 text-start text-sm leading-tight">
                                        <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                        <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                    </div>
                                </div>
                            </div>
                        </flux:menu.radio.group>
                        <flux:menu.separator />
                        <flux:menu.radio.group>
                            <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                        </flux:menu.radio.group>
                        <flux:menu.separator />
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                                {{ __('Log Out') }}
                            </flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            </flux:sidebar>

            <flux:header class="lg:hidden">
                <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
                <flux:spacer />
                <flux:dropdown position="top" align="end">
                    <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />
                    <flux:menu>
                        <flux:menu.radio.group>
                            <div class="p-0 text-sm font-normal">
                                <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                    <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                        <span class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                            {{ auth()->user()->initials() }}
                                        </span>
                                    </span>
                                    <div class="grid flex-1 text-start text-sm leading-tight">
                                        <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                        <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                    </div>
                                </div>
                            </div>
                        </flux:menu.radio.group>
                        <flux:menu.separator />
                        <flux:menu.radio.group>
                            <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                        </flux:menu.radio.group>
                        <flux:menu.separator />
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                                {{ __('Log Out') }}
                            </flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            </flux:header>
        @else
            <!-- Simple Header for Non-Dashboard Pages -->
            <div class="fixed border-e border-white/10 bg-zinc-300/50 dark:bg-black/50 backdrop-blur-sm top-0 left-0 z-50 p-4 w-screen">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2" wire:navigate>
                    <h1 class="text-2xl font-bold text-white" style="text-shadow: 0 0 20px rgba(0, 0, 0, 0.54);">
                        LinkLens<span class="text-pink-400">AI</span>
                    </h1>
                </a>
            </div>
        @endif

        {{ $slot }}


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
    </body>
</html>
