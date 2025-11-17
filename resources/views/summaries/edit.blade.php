@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-edit me-2"></i>Edit Summary
                </h4>
                <p class="mb-0 mt-2 opacity-75">Refine and improve your AI-generated summary</p>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('summaries.update', $summary) }}" method="POST" id="editForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Left Column - Metadata -->
                        <div class="col-md-4">
                            <div class="mb-4">
                                <label for="title" class="form-label">
                                    <i class="fas fa-heading text-primary me-2"></i>Title <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control form-control-lg" 
                                       id="title" 
                                       name="title" 
                                       value="{{ $summary->title }}" 
                                       required>
                            </div>

                            <div class="mb-4">
                                <label for="category" class="form-label">
                                    <i class="fas fa-tag text-success me-2"></i>Category <span class="text-danger">*</span>
                                </label>
                                <select class="form-select form-select-lg" id="category" name="category" required>
                                    <option value="Business" {{ $summary->category == 'Business' ? 'selected' : '' }}>📊 Business</option>
                                    <option value="Technology" {{ $summary->category == 'Technology' ? 'selected' : '' }}>💻 Technology</option>
                                    <option value="Science" {{ $summary->category == 'Science' ? 'selected' : '' }}>🔬 Science</option>
                                    <option value="Education" {{ $summary->category == 'Education' ? 'selected' : '' }}>📚 Education</option>
                                    <option value="Health" {{ $summary->category == 'Health' ? 'selected' : '' }}>🏥 Health</option>
                                    <option value="News" {{ $summary->category == 'News' ? 'selected' : '' }}>📰 News</option>
                                    <option value="Research" {{ $summary->category == 'Research' ? 'selected' : '' }}>🔍 Research</option>
                                    <option value="Legal" {{ $summary->category == 'Legal' ? 'selected' : '' }}>⚖️ Legal</option>
                                    <option value="Finance" {{ $summary->category == 'Finance' ? 'selected' : '' }}>💰 Finance</option>
                                    <option value="Other" {{ $summary->category == 'Other' ? 'selected' : '' }}>📝 Other</option>
                                </select>
                            </div>

                            <div class="card bg-light mb-4">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-info-circle text-info me-2"></i>Summary Stats
                                    </h6>
                                    <hr>
                                    <div class="mb-2">
                                        <small class="text-muted">Original Words:</small>
                                        <strong class="float-end">{{ $summary->word_count }}</strong>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted">Summary Words:</small>
                                        <strong class="float-end" id="summaryWordCount">{{ str_word_count($summary->summary) }}</strong>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted">Compression:</small>
                                        <strong class="float-end" id="compressionRatio">
                                            {{ round((str_word_count($summary->summary) / $summary->word_count) * 100) }}%
                                        </strong>
                                    </div>
                                    <div>
                                        <small class="text-muted">Created:</small>
                                        <strong class="float-end">{{ $summary->created_at->format('M d, Y') }}</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Note:</strong> Editing the original text will not automatically regenerate the summary. Use the "Regenerate" button if needed.
                            </div>
                        </div>

                        <!-- Right Column - Content -->
                        <div class="col-md-8">
                            <div class="mb-4">
                                <label for="original_text" class="form-label">
                                    <i class="fas fa-file-alt text-primary me-2"></i>Original Text <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" 
                                          id="original_text" 
                                          name="original_text" 
                                          rows="10" 
                                          required>{{ $summary->original_text }}</textarea>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <span id="originalCharCount">{{ strlen($summary->original_text) }}</span> characters, 
                                        <span id="originalWordCount">{{ $summary->word_count }}</span> words
                                    </small>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label for="summary" class="form-label mb-0">
                                        <i class="fas fa-robot text-success me-2"></i>AI Summary <span class="text-danger">*</span>
                                    </label>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-secondary" onclick="enhanceSummary()">
                                            <i class="fas fa-magic me-1"></i> Enhance
                                        </button>
                                        <button type="button" class="btn btn-outline-primary" onclick="shortenSummary()">
                                            <i class="fas fa-compress me-1"></i> Shorten
                                        </button>
                                        <button type="button" class="btn btn-outline-info" onclick="expandSummary()">
                                            <i class="fas fa-expand me-1"></i> Expand
                                        </button>
                                    </div>
                                </div>
                                <textarea class="form-control" 
                                          id="summary" 
                                          name="summary" 
                                          rows="12" 
                                          required>{{ $summary->summary }}</textarea>
                                <div class="form-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    You can manually edit the AI-generated summary or use the tools above for AI assistance
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <span id="summaryCharCount">{{ strlen($summary->summary) }}</span> characters, 
                                        <span id="summaryWordCountLive">{{ str_word_count($summary->summary) }}</span> words
                                    </small>
                                </div>
                            </div>

                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-eye me-2"></i>Summary Preview
                                    </h6>
                                    <div id="summaryPreview" class="summary-section mt-3">
                                        {{ $summary->summary }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="regenerate_on_save" name="regenerate_on_save">
                                <label class="form-check-label" for="regenerate_on_save">
                                    <i class="fas fa-sync-alt me-2"></i>Regenerate summary from edited text
                                </label>
                                <small class="d-block text-muted mt-1">This will replace your current summary with a new AI-generated one</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-3 justify-content-end">
                                <a href="{{ route('summaries.show', $summary) }}" class="btn btn-outline-secondary btn-lg">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-2"></i>Save Changes
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const originalTextArea = document.getElementById('original_text');
    const summaryTextArea = document.getElementById('summary');
    const summaryPreview = document.getElementById('summaryPreview');
    
    // Update character and word counts
    function updateCounts() {
        const originalText = originalTextArea.value;
        const summaryText = summaryTextArea.value;
        
        const originalChars = originalText.length;
        const originalWords = originalText.trim().split(/\s+/).filter(w => w.length > 0).length;
        const summaryChars = summaryText.length;
        const summaryWords = summaryText.trim().split(/\s+/).filter(w => w.length > 0).length;
        
        document.getElementById('originalCharCount').textContent = originalChars;
        document.getElementById('originalWordCount').textContent = originalWords;
        document.getElementById('summaryCharCount').textContent = summaryChars;
        document.getElementById('summaryWordCountLive').textContent = summaryWords;
        document.getElementById('summaryWordCount').textContent = summaryWords;
        
        if (originalWords > 0) {
            const compression = Math.round((summaryWords / originalWords) * 100);
            document.getElementById('compressionRatio').textContent = compression + '%';
        }
        
        // Update preview
        summaryPreview.textContent = summaryText || 'Preview will appear here...';
    }
    
    originalTextArea.addEventListener('input', updateCounts);
    summaryTextArea.addEventListener('input', updateCounts);
    
    // AI Enhancement Functions (these would connect to your backend AI service)
    function enhanceSummary() {
        if (confirm('This will use AI to enhance your summary. Continue?')) {
            alert('AI enhancement feature would connect to your backend service.');
            // In production: make AJAX call to AI service
        }
    }
    
    function shortenSummary() {
        if (confirm('This will use AI to create a shorter version. Continue?')) {
            alert('AI shortening feature would connect to your backend service.');
            // In production: make AJAX call to AI service
        }
    }
    
    function expandSummary() {
        if (confirm('This will use AI to expand your summary. Continue?')) {
            alert('AI expansion feature would connect to your backend service.');
            // In production: make AJAX call to AI service
        }
    }
    
    // Auto-save draft (optional feature)
    let autoSaveTimer;
    function autoSaveDraft() {
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(() => {
            console.log('Auto-saving draft...');
            // In production: save draft to localStorage or backend
        }, 3000);
    }
    
    summaryTextArea.addEventListener('input', autoSaveDraft);
    
    // Warn before leaving with unsaved changes
    let formChanged = false;
    document.getElementById('editForm').addEventListener('change', () => {
        formChanged = true;
    });
    
    window.addEventListener('beforeunload', (e) => {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
    
    document.getElementById('editForm').addEventListener('submit', () => {
        formChanged = false;
    });
    
    // Initialize
    updateCounts();
</script>
@endsection