<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <h1 class="text-3xl font-bold text-sky-500 mb-8">LinkedIn Automation Dashboard</h1>

        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('message') }}
            </div>
        @endif

        @if (!$profile)
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                <p>Please connect your LinkedIn account to start automation.</p>
                <a href="/auth/linkedin" class="bg-blue-500 text-white px-4 py-2 rounded mt-2 inline-block">
                    Connect LinkedIn
                </a>
            </div>
        @else
            <!-- Automation Controls -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Connection Automation</h3>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Auto-accept connections</span>
                            <button wire:click="toggleAutoAccept"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $profile->auto_accept_connections ? 'bg-blue-600' : 'bg-gray-200' }}">
                                <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $profile->auto_accept_connections ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </div>
                        <button wire:click="processConnections"
                            class="mt-4 bg-blue-500 text-white px-4 py-2 rounded text-sm">
                            Process Now
                        </button>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Post Automation</h3>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Auto-generate posts</span>
                            <button wire:click="togglePostAutomation"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $profile->post_automation_enabled ? 'bg-blue-600' : 'bg-gray-200' }}">
                                <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $profile->post_automation_enabled ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </div>
                        <button wire:click="generatePost"
                            class="mt-4 bg-green-500 text-white px-4 py-2 rounded text-sm">
                            Generate Post
                        </button>
                    </div>
                </div>
            </div>

            <!-- Connection Filters -->
            <div class="bg-white overflow-hidden shadow rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Connection Filters</h3>

                    <div class="mb-4">
                        <input wire:model="newFilterName"
                            placeholder="Filter name"
                            class="border rounded px-3 py-2 mr-2">
                        <button wire:click="createFilter"
                            class="bg-blue-500 text-white px-4 py-2 rounded">
                            Add Filter
                        </button>
                    </div>

                    <div class="space-y-2">
                        @foreach ($filters as $filter)
                            <div class="flex items-center justify-between bg-gray-50 p-3 rounded">
                                <span class="font-medium">{{ $filter->name }}</span>
                                <button wire:click="deleteFilter({{ $filter->id }})"
                                    class="text-red-600 hover:text-red-800">
                                    Delete
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Recent Posts -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Posts</h3>
                    <div class="space-y-4">
                        @foreach ($recentPosts as $post)
                            <div class="border-l-4 {{ $post->status === 'posted' ? 'border-green-400' : ($post->status === 'failed' ? 'border-red-400' : 'border-yellow-400') }} pl-4">
                                <p class="text-sm text-gray-600">{{ $post->content }}</p>
                                <p class="text-xs text-gray-400 mt-1">
                                    Status: {{ ucfirst($post->status) }} • {{ $post->created_at->diffForHumans() }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
