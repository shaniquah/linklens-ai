<div class="flex flex-col h-full">
    <div class="flex justify-between items-center flex-shrink-0 mb-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Automation History</h3>
        <div class="text-sm text-gray-500 dark:text-gray-400">
            {{ $totalPosts }} posts • {{ $totalFilters }} filters
        </div>
    </div>
    
    <div class="flex-1 overflow-y-auto space-y-4">
        @if($recentPosts->count() > 0)
            <div class="space-y-3">
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Recent Posts</h4>
                @foreach($recentPosts as $post)
                    <div class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="flex-shrink-0">
                            <div class="w-6 h-6 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                <svg class="w-3 h-3 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0v12h8V4H6z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900 dark:text-white truncate">{{ Str::limit($post->content, 60) }}</p>
                            <div class="flex items-center space-x-2 mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    @if($post->status === 'posted') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($post->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                    {{ ucfirst($post->status) }}
                                </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $post->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        
        @if($activeFilters->count() > 0)
            <div class="space-y-3">
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Active Filters</h4>
                @foreach($activeFilters as $filter)
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="flex-shrink-0">
                            <div class="w-6 h-6 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                                <svg class="w-3 h-3 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $filter->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ count($filter->criteria ?? []) }} criteria • Created {{ $filter->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    
    <div class="flex-shrink-0 pt-4 border-t border-gray-200 dark:border-gray-700">
        <a href="{{ route('linkedin.dashboard') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">
            Manage automation →
        </a>
    </div>
</div>