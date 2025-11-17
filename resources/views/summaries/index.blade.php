
@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
    <div>
        <h1 class="mb-0"><i class="fas fa-file-alt text-primary"></i> My Summaries</h1>
        <p class="text-muted mt-2">AI-powered text summarization at your fingertips</p>
    </div>
    <a href="{{ route('summaries.create') }}" class="btn btn-primary">
        <i class="fas fa-plus-circle me-2"></i>Create New Summary
    </a>
</div>

@if($summaries->count() > 0)
    <!-- Stats Overview -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-file-alt fa-2x text-primary mb-2"></i>
                    <h3 class="mb-0">{{ $summaries->count() }}</h3>
                    <p class="text-muted mb-0">Total Summaries</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-book fa-2x text-success mb-2"></i>
                    <h3 class="mb-0">{{ $summaries->sum('word_count') }}</h3>
                    <p class="text-muted mb-0">Words Processed</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-tags fa-2x text-info mb-2"></i>
                    <h3 class="mb-0">{{ $summaries->unique('category')->count() }}</h3>
                    <p class="text-muted mb-0">Categories</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                    <h3 class="mb-0">{{ round($summaries->sum('word_count') / 200) }}</h3>
                    <p class="text-muted mb-0">Min. Reading Time</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-md-4 mb-2">
                    <label class="form-label"><i class="fas fa-search me-2"></i>Search</label>
                    <input type="text" id="searchInput" class="form-control" placeholder="Search summaries...">
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label"><i class="fas fa-filter me-2"></i>Category</label>
                    <select id="categoryFilter" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($summaries->unique('category')->pluck('category') as $category)
                            <option value="{{ $category }}">{{ $category }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label"><i class="fas fa-sort me-2"></i>Sort By</label>
                    <select id="sortFilter" class="form-select">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="longest">Longest First</option>
                        <option value="shortest">Shortest First</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <button class="btn btn-outline-secondary w-100" onclick="resetFilters()">
                        <i class="fas fa-redo me-2"></i>Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Summaries Grid -->
    <div class="row" id="summariesGrid">
        @foreach($summaries as $summary)
            <div class="col-md-6 col-lg-4 mb-4 summary-item" 
                 data-category="{{ $summary->category }}" 
                 data-words="{{ $summary->word_count }}"
                 data-date="{{ $summary->created_at->timestamp }}"
                 data-title="{{ strtolower($summary->title) }}">
                <div class="card summary-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-document text-primary me-2"></i>
                                {{ $summary->title }}
                            </h5>
                        </div>
                        
                        <div class="mb-3">
                            <span class="stat-badge category-badge">
                                <i class="fas fa-tag"></i> {{ $summary->category }}
                            </span>
                            <span class="stat-badge word-count-badge">
                                <i class="fas fa-font"></i> {{ $summary->word_count }} words
                            </span>
                            <span class="stat-badge reading-time-badge">
                                <i class="fas fa-clock"></i> {{ round($summary->word_count / 200) }} min read
                            </span>
                        </div>
                        
                        <p class="card-text text-muted">
                            <i class="fas fa-align-left me-2"></i>
                            {{ Str::limit($summary->summary, 120) }}
                        </p>
                        
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="far fa-calendar-alt me-1"></i>
                                {{ $summary->created_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <div class="btn-group-custom">
                            <a href="{{ route('summaries.show', $summary) }}" class="btn btn-primary action-btn btn-sm flex-fill">
                                <i class="fas fa-eye me-1"></i> View
                            </a>
                            <a href="{{ route('summaries.edit', $summary) }}" class="btn btn-outline-secondary action-btn btn-sm flex-fill">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            <form action="{{ route('summaries.destroy', $summary) }}" method="POST" class="flex-fill">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger action-btn btn-sm w-100" 
                                    onclick="return confirm('Are you sure you want to delete this summary?')">
                                    <i class="fas fa-trash me-1"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div id="noResults" class="alert alert-info text-center" style="display: none;">
        <i class="fas fa-search fa-2x mb-2"></i>
        <p class="mb-0">No summaries found matching your criteria.</p>
    </div>
@else
    <div class="empty-state">
        <i class="fas fa-inbox"></i>
        <h3 class="mt-3">No Summaries Yet</h3>
        <p class="text-muted">Start by creating your first AI-powered summary!</p>
        <a href="{{ route('summaries.create') }}" class="btn btn-primary btn-lg mt-3">
            <i class="fas fa-plus-circle me-2"></i>Create Your First Summary
        </a>
    </div>
@endif

<script>
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const sortFilter = document.getElementById('sortFilter');
    const summariesGrid = document.getElementById('summariesGrid');
    const noResults = document.getElementById('noResults');

    function filterAndSortSummaries() {
        const searchTerm = searchInput?.value.toLowerCase() || '';
        const category = categoryFilter?.value || '';
        const sortBy = sortFilter?.value || 'newest';
        
        let items = Array.from(document.querySelectorAll('.summary-item'));
        let visibleCount = 0;

        // Filter
        items.forEach(item => {
            const title = item.dataset.title;
            const itemCategory = item.dataset.category;
            
            const matchesSearch = title.includes(searchTerm);
            const matchesCategory = !category || itemCategory === category;
            
            if (matchesSearch && matchesCategory) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        // Sort visible items
        const visibleItems = items.filter(item => item.style.display !== 'none');
        
        visibleItems.sort((a, b) => {
            switch(sortBy) {
                case 'newest':
                    return parseInt(b.dataset.date) - parseInt(a.dataset.date);
                case 'oldest':
                    return parseInt(a.dataset.date) - parseInt(b.dataset.date);
                case 'longest':
                    return parseInt(b.dataset.words) - parseInt(a.dataset.words);
                case 'shortest':
                    return parseInt(a.dataset.words) - parseInt(b.dataset.words);
                default:
                    return 0;
            }
        });

        // Reorder in DOM
        visibleItems.forEach(item => summariesGrid.appendChild(item));

        // Show/hide no results message
        if (noResults) {
            noResults.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }

    function resetFilters() {
        if (searchInput) searchInput.value = '';
        if (categoryFilter) categoryFilter.value = '';
        if (sortFilter) sortFilter.value = 'newest';
        filterAndSortSummaries();
    }

    // Add event listeners
    if (searchInput) searchInput.addEventListener('input', filterAndSortSummaries);
    if (categoryFilter) categoryFilter.addEventListener('change', filterAndSortSummaries);
    if (sortFilter) sortFilter.addEventListener('change', filterAndSortSummaries);
</script>
@endsection