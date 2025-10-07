<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">LinkLens AI Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-400">Automate your LinkedIn presence with AI-powered posts and smart connection management</p>
        </div>

        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <a href="{{ route('linkedin.dashboard') }}" class="group relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 hover:border-blue-300 transition-colors">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20">
                    <div class="flex h-full flex-col justify-center items-center p-6">
                        <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">LinkedIn Automation</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 text-center">Manage posts and connections</p>
                    </div>
                </div>
            </a>

            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20">
                    <div class="flex h-full flex-col justify-center items-center p-6">
                        <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Analytics</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 text-center">Coming Soon</p>
                    </div>
                </div>
            </div>

            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20">
                    <div class="flex h-full flex-col justify-center items-center p-6">
                        <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Settings</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 text-center">Configure automation</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <div class="absolute inset-0 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900/20 dark:to-gray-800/20">
                <div class="flex h-full flex-col justify-center items-center p-8">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Welcome to LinkLens AI</h2>
                    <p class="text-gray-600 dark:text-gray-400 text-center max-w-md">Your AI-powered LinkedIn automation assistant is ready to help you grow your professional network and maintain an engaging presence.</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
