<div class="flex items-start max-md:flex-col">
    <div class="me-10 w-full pb-4 md:w-[220px]">
        <nav class="space-y-1">
            <a href="{{ route('settings.profile') }}" wire:navigate class="block px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('settings.profile') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">{{ __('Profile') }}</a>
            <a href="{{ route('settings.password') }}" wire:navigate class="block px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('settings.password') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">{{ __('Password') }}</a>
            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <a href="{{ route('two-factor.show') }}" wire:navigate class="block px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('two-factor.show') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">{{ __('Two-Factor Auth') }}</a>
            @endif
            <a href="{{ route('settings.appearance') }}" wire:navigate class="block px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('settings.appearance') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">{{ __('Appearance') }}</a>
        </nav>
    </div>

    <div class="border-t border-gray-200 dark:border-gray-700 md:hidden my-4"></div>

    <div class="flex-1 self-stretch max-md:pt-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $heading ?? '' }}</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $subheading ?? '' }}</p>

        <div class="mt-5 w-full max-w-lg">
            {{ $slot }}
        </div>
    </div>
</div>
