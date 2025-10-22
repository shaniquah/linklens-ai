<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <h1 class="text-3xl font-bold text-sky-500 mb-8">LinkedIn Automation Dashboard</h1>

        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('message') }}
            </div>
        @endif

        @if (!$profile)
            <div class="bg-yellow-50 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                <p>Please connect your LinkedIn account to start automation.</p>
                <a href="/auth/linkedin" class="bg-blue-500 text-white px-4 py-2 rounded mt-2 inline-block">
                    Connect LinkedIn
                </a>
            </div>
        @else
            <!-- Automation Controls -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-zinc-300 dark:bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Connection Automation</h3>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Auto-accept connections</span>
                            <flux:switch :checked="$profile->auto_accept_connections" wire:click="toggleAutoAccept" class="blue-switch" />
                        </div>
                        <button wire:click="processConnections"
                            class="mt-4 bg-blue-500 text-white px-4 py-2 rounded text-sm">
                            Process Now
                        </button>
                    </div>
                </div>

                <div class="bg-zinc-300 dark:bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Post Automation</h3>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Auto-generate posts</span>
                            <flux:switch :checked="$profile->post_automation_enabled" wire:click="togglePostAutomation" class="blue-switch" />
                        </div>
                        <button wire:click="generatePost"
                            class="mt-4 bg-green-500 text-white px-4 py-2 rounded text-sm">
                            Configure Post Generation
                        </button>
                    </div>
                </div>
            </div>

            <!-- Connection Filters -->
            <div class="bg-zinc-300 dark:bg-white overflow-hidden shadow rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Connection Filters</h3>

                    <div class="mb-6 text-zinc-500 space-y-4">
                        <input wire:model="newFilterName" placeholder="Filter name" class="w-full border border-white dark:border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />

                        <div class="grid grid-cols-2 gap-4">
                            <select wire:model="filterIndustry" class="w-full border border-white dark:border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select Industry</option>
                                @foreach($industries as $industry)
                                    <option value="{{ $industry }}">{{ $industry }}</option>
                                @endforeach
                            </select>

                            <select wire:model="filterLocation" class="w-full border border-white dark:border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select Location</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location }}">{{ $location }}</option>
                                @endforeach
                            </select>

                            <select wire:model="filterJobTitle" class="w-full border border-white dark:border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select Job Title</option>
                                @foreach($jobTitles as $title)
                                    <option value="{{ $title }}">{{ $title }}</option>
                                @endforeach
                            </select>

                            <input wire:model="filterCompanySize" type="number" placeholder="Min Company Size" class="w-full border border-white dark:border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Keywords</label>
                            <div class="space-y-2">
                                @foreach($availableKeywords as $keyword)
                                    <label class="inline-flex items-center mr-4">
                                        <input type="checkbox" wire:model="filterKeywords" value="{{ $keyword }}" class="mr-2">
                                        <span class="text-sm">{{ $keyword }}</span>
                                    </label>
                                @endforeach
                                <div class="w-full">
                                    <label class="inline-flex items-center mr-4">
                                        <input type="checkbox" wire:click="toggleOtherKeyword" {{ $showOtherKeyword ? 'checked' : '' }} class="mr-2">
                                        <span class="text-sm">Other</span>
                                    </label>
                                    @if($showOtherKeyword)
                                        <div class="mt-2 flex gap-2">
                                            <input wire:model="customKeyword" placeholder="Enter custom keyword. Separate with commas" class="flex-1 border border-white dark:border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <button wire:click="addCustomKeyword" class="px-3 py-2 bg-blue-500 text-white rounded text-sm hover:bg-blue-600">Add</button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <button wire:click="createFilter" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Add Filter
                        </button>
                    </div>

                    <div class="space-y-2">
                        @foreach ($filters as $filter)
                            <div class="flex items-center justify-between bg-zinc-200 dark:bg-gray-50 p-3 rounded">
                                <div class="flex items-center space-x-3">
                                    <span class="font-medium {{ $filter->is_active ? 'text-gray-900' : 'text-gray-400' }}">{{ $filter->name }}</span>
                                    @if(!$filter->is_active)
                                        <span class="text-xs text-gray-500 bg-gray-200 px-2 py-1 rounded">Inactive</span>
                                    @endif
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button wire:click="toggleFilter({{ $filter->id }})"
                                        class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $filter->is_active ? 'bg-blue-600' : 'bg-gray-200' }}">
                                        <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $filter->is_active ? 'translate-x-4' : 'translate-x-0' }}"></span>
                                    </button>
                                    <button wire:click="deleteFilter({{ $filter->id }})"
                                        class="text-red-600 hover:text-red-800 text-sm">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Recent Posts -->
            <div class="bg-zinc-300 dark:bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Posts</h3>
                    <div class="space-y-4">
                        @foreach ($recentPosts as $post)
                            <div class="border-l-4 {{ $post->status === 'posted' ? 'border-green-400' : ($post->status === 'failed' ? 'border-red-400' : 'border-yellow-400') }} pl-4">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-600">{{ $post->content }}</p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            Status: {{ ucfirst($post->status) }} • {{ $post->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    @if($post->status === 'failed')
                                        <button wire:click="retryPost({{ $post->id }})"
                                            class="ml-3 bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs">
                                            Retry
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Post Generation Modal -->
    @if($showPostModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showPostModal') }" x-show="show">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" wire:click="closeModal"></div>

            <div class="inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-zinc-500 text-left align-middle transition-all transform bg-sky-50 shadow-xl rounded-2xl">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Configure Post Generation</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="savePostSettings" class="space-y-6">
                    <!-- Post Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Post Type</label>
                        <select wire:model="postType" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="short">Short Post (1-2 sentences)</option>
                            <option value="medium">Medium Post (3-5 sentences)</option>
                            <option value="long">Long Post/Article (Full content)</option>
                        </select>
                    </div>

                    <!-- Post Frequency -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Post Frequency</label>
                        <select wire:model="postFrequency" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="bi-weekly">Bi-weekly</option>
                        </select>
                    </div>

                    <!-- Approval Required -->
                    <div class="flex items-center">
                        <input type="checkbox" wire:model="requireApproval" id="approval" class="mr-2">
                        <label for="approval" class="text-sm font-medium text-gray-700">Require approval before posting</label>
                    </div>

                    <!-- Speaker Voice -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Speaker Voice</label>
                        <select wire:model="speakerVoice" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="professional">Professional</option>
                            <option value="casual">Casual</option>
                            <option value="authoritative">Authoritative</option>
                            <option value="friendly">Friendly</option>
                        </select>
                    </div>

                    <!-- Post Themes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Post Themes</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="postThemes" value="industry_insights" class="mr-2">
                                <span class="text-sm">Industry Insights</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="postThemes" value="career_tips" class="mr-2">
                                <span class="text-sm">Career Tips</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="postThemes" value="networking" class="mr-2">
                                <span class="text-sm">Networking</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="postThemes" value="motivation" class="mr-2">
                                <span class="text-sm">Motivation</span>
                            </label>
                        </div>
                    </div>

                    <!-- Tone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tone</label>
                        <select wire:model="tone" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="informative">Informative</option>
                            <option value="inspirational">Inspirational</option>
                            <option value="educational">Educational</option>
                            <option value="promotional">Promotional</option>
                        </select>
                    </div>

                    <!-- Diction -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Diction</label>
                        <select wire:model="diction" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="business">Business</option>
                            <option value="technical">Technical</option>
                            <option value="conversational">Conversational</option>
                            <option value="academic">Academic</option>
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                            Save & Generate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Echo !== 'undefined') {
            Echo.private('user.{{ auth()->id() }}')
                .listen('PostCreated', (e) => {
                    @this.call('addNewPost', e);
                });
        }
    });
</script>
