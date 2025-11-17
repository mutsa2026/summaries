@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <!-- Header Card -->
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h4 class="mb-1">
                            <i class="fas fa-file-alt me-2"></i>{{ $summary->title }}
                        </h4>
                        <div class="mt-2">
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
                    </div>
                    <div class="btn-group-custom mt-3 mt-md-0">
                        <a href="{{ route('summaries.edit', $summary) }}" class="btn btn-outline-secondary action-btn">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <form action="{{ route('summaries.regenerate', $summary) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary action-btn">
                                <i class="fas fa-sync-alt me-1"></i> Regenerate
                            </button>
                        </form>
                        <button class="btn btn-outline-success action-btn" onclick="copyToClipboard()">
                            <i class="fas fa-copy me-1"></i> Copy Summary
                        </button>
                        <form action="{{ route('summaries.destroy', $summary) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger action-btn" 
                                onclick="return confirm('Are you sure you want to delete this summary?')">
                                <i class="fas fa-trash me-1"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comparison View -->
        <div class="row mb-4">
            <!-- Original Text Column -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-light text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-align-left me-2"></i>Original Text
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="text-section" id="originalText" style="max-height: 600px; overflow-y: auto;">
                            {{ $summary->original_text }}
                        </div>
                        <hr>
                        <div class="row text-center">
                            <div class="col-6">
                                <small class="text-muted d-block">Original Words</small>
                                <strong class="h5">{{ $summary->word_count }}</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Reading Time</small>
                                <strong class="h5">{{ round($summary->word_count / 200) }} min</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- AI Summary Column -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header" style="background: var(--primary-gradient); color: white;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-robot me-2"></i>AI-Generated Summary
                            </h5>
                            <span class="badge bg-white text-dark">
                                <i class="fas fa-compress-alt me-1"></i>
                                {{ round((str_word_count($summary->summary) / $summary->word_count) * 100) }}% of original
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="summary-section" id="summaryText">
                            {{ $summary->summary }}
                        </div>
                        <hr>
                        <div class="row text-center">
                            <div class="col-6">
                                <small class="text-muted d-block">Summary Words</small>
                                <strong class="h5">{{ str_word_count($summary->summary) }}</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Reading Time</small>
                                <strong class="h5">{{ max(1, round(str_word_count($summary->summary) / 200)) }} min</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analysis & Metadata -->
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-chart-line fa-3x text-primary mb-3"></i>
                        <h5>Compression Ratio</h5>
                        <h2 class="text-primary">
                            {{ round((str_word_count($summary->summary) / $summary->word_count) * 100) }}%
                        </h2>
                        <p class="text-muted mb-0">
                            Reduced by {{ 100 - round((str_word_count($summary->summary) / $summary->word_count) * 100) }}%
                        </p>
                        <div class="progress mt-3" style="height: 10px;">
                            <div class="progress-bar bg-success" 
                                 role="progressbar" 
                                 style="width: {{ round((str_word_count($summary->summary) / $summary->word_count) * 100) }}%">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-save fa-3x text-success mb-3"></i>
                        <h5>Time Saved</h5>
                        <h2 class="text-success">
                            {{ max(0, round($summary->word_count / 200) - round(str_word_count($summary->summary) / 200)) }} min
                        </h2>
                        <p class="text-muted mb-0">
                            From {{ round($summary->word_count / 200) }} to {{ round(str_word_count($summary->summary) / 200) }} minutes
                        </p>
                        <div class="mt-3">
                            <i class="fas fa-clock text-success me-2"></i>
                            <span class="text-muted">Reading Time Optimization</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-alt fa-3x text-info mb-3"></i>
                        <h5>Summary Details</h5>
                        <div class="text-start mt-3">
                            <div class="mb-2">
                                <i class="fas fa-clock text-info me-2"></i>
                                <strong>Created:</strong><br>
                                <small class="text-muted ms-4">{{ $summary->created_at->format('M d, Y h:i A') }}</small>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-sync text-info me-2"></i>
                                <strong>Updated:</strong><br>
                                <small class="text-muted ms-4">{{ $summary->updated_at->diffForHumans() }}</small>
                            </div>
                            <div>
                                <i class="fas fa-tag text-info me-2"></i>
                                <strong>Category:</strong><br>
                                <small class="text-muted ms-4">{{ $summary->category }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex gap-3 justify-content-between flex-wrap">
                    <a href="{{ route('summaries.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Back to All Summaries
                    </a>
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-outline-secondary" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>Print
                        </button>
                        <button class="btn btn-outline-info" onclick="downloadAsText()">
                            <i class="fas fa-download me-2"></i>Download TXT
                        </button>
                        <button class="btn btn-outline-success" onclick="shareLink()">
                            <i class="fas fa-share-alt me-2"></i>Share
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function copyToClipboard() {
        const summaryText = document.getElementById('summaryText').innerText;
        navigator.clipboard.writeText(summaryText).then(() => {
            alert('Summary copied to clipboard!');
        });
    }

    function downloadAsText() {
        const title = "{{ $summary->title }}";
        const summary = document.getElementById('summaryText').innerText;
        const original = document.getElementById('originalText').innerText;
        
        const content = `${title}\n\n` +
                       `Category: {{ $summary->category }}\n` +
                       `Created: {{ $summary->created_at->format('M d, Y') }}\n\n` +
                       `=== SUMMARY ===\n${summary}\n\n` +
                       `=== ORIGINAL TEXT ===\n${original}`;
        
        const blob = new Blob([content], { type: 'text/plain' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `${title.replace(/[^a-z0-9]/gi, '_')}_summary.txt`;
        a.click();
        window.URL.revokeObjectURL(url);
    }

    function shareLink() {
        const url = window.location.href;
        if (navigator.share) {
            navigator.share({
                title: '{{ $summary->title }}',
                text: 'Check out this AI-generated summary',
                url: url
            });
        } else {
            navigator.clipboard.writeText(url).then(() => {
                alert('Link copied to clipboard!');
            });
        }
    }

    // Print styles
    const style = document.createElement('style');
    style.textContent = `
        @media print {
            .card-header, .btn, .navbar, .card-footer { display: none !important; }
            .card { box-shadow: none !important; border: 1px solid #ddd !important; }
        }
    `;
    document.head.appendChild(style);
</script>
@endsection