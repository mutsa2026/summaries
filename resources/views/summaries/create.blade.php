@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-plus-circle me-2"></i>Create New AI Summary
                </h4>
                <p class="mb-0 mt-2 opacity-75">Transform your text into concise, intelligent summaries</p>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('summaries.store') }}" method="POST" id="summaryForm">
                    @csrf
                    
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label for="title" class="form-label">
                                    <i class="fas fa-heading text-primary me-2"></i>Title <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control form-control-lg" 
                                       id="title" 
                                       name="title" 
                                       placeholder="e.g., Climate Change Report 2024"
                                       required>
                                <div class="form-text">Give your summary a descriptive title</div>
                            </div>

                            <div class="mb-4">
                                <label for="category" class="form-label">
                                    <i class="fas fa-tag text-success me-2"></i>Category <span class="text-danger">*</span>
                                </label>
                                <select class="form-select form-select-lg" id="category" name="category" required>
                                    <option value="">Select a category...</option>
                                    <option value="Business">📊 Business</option>
                                    <option value="Technology">💻 Technology</option>
                                    <option value="Science">🔬 Science</option>
                                    <option value="Education">📚 Education</option>
                                    <option value="Health">🏥 Health</option>
                                    <option value="News">📰 News</option>
                                    <option value="Research">🔍 Research</option>
                                    <option value="Legal">⚖️ Legal</option>
                                    <option value="Finance">💰 Finance</option>
                                    <option value="Other">📝 Other</option>
                                </select>
                                <div class="form-text">Helps organize your summaries</div>
                            </div>

                            <div class="mb-4">
                                <label for="summary_length" class="form-label">
                                    <i class="fas fa-sliders-h text-info me-2"></i>Summary Length
                                </label>
                                <select class="form-select" id="summary_length" name="summary_length">
                                    <option value="short">Short (2-3 sentences)</option>
                                    <option value="medium" selected>Medium (1 paragraph)</option>
                                    <option value="long">Long (2-3 paragraphs)</option>
                                    <option value="detailed">Detailed (Comprehensive)</option>
                                </select>
                                <div class="form-text">Choose how detailed you want the summary</div>
                            </div>

                            <div class="mb-4">
                                <label for="focus_areas" class="form-label">
                                    <i class="fas fa-bullseye text-warning me-2"></i>Focus Areas (Optional)
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="focus_areas" 
                                       name="focus_areas" 
                                       placeholder="e.g., key findings, statistics, recommendations">
                                <div class="form-text">What aspects should the AI focus on?</div>
                            </div>

                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="include_key_points" name="include_key_points" checked>
                                    <label class="form-check-label" for="include_key_points">
                                        <i class="fas fa-list-ul me-2"></i>Extract Key Points
                                    </label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="include_entities" name="include_entities">
                                    <label class="form-check-label" for="include_entities">
                                        <i class="fas fa-user-tag me-2"></i>Identify Named Entities
                                    </label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="sentiment_analysis" name="sentiment_analysis">
                                    <label class="form-check-label" for="sentiment_analysis">
                                        <i class="fas fa-smile me-2"></i>Sentiment Analysis
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label for="original_text" class="form-label">
                                    <i class="fas fa-file-alt text-primary me-2"></i>Original Text <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" 
                                          id="original_text" 
                                          name="original_text" 
                                          rows="15" 
                                          required 
                                          placeholder="Paste or type the text you want to summarize...

Tip: The more content you provide, the better the AI can analyze and summarize it. Minimum 100 characters recommended."
                                          minlength="100"></textarea>
                                <div id="textStats" class="mt-2 p-2 bg-light rounded">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <small class="text-muted">Characters</small>
                                            <div class="fw-bold" id="charCount">0</div>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted">Words</small>
                                            <div class="fw-bold" id="wordCount">0</div>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted">Reading Time</small>
                                            <div class="fw-bold" id="readTime">0 min</div>
                                        </div>
                                    </div>
                                    <div class="progress progress-thin mt-2">
                                        <div id="charProgress" class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <small class="text-muted d-block mt-1" id="progressText">Start typing...</small>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-lightbulb me-2"></i>
                                <strong>Pro Tips:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Longer texts (500+ words) produce better summaries</li>
                                    <li>Well-structured text with clear paragraphs works best</li>
                                    <li>Remove unnecessary formatting before pasting</li>
                                    <li>The AI preserves the most important information</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex gap-3 justify-content-end">
                        <a href="{{ route('summaries.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                            <i class="fas fa-magic me-2"></i>Generate AI Summary
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const originalText = document.getElementById('original_text');
    const charCount = document.getElementById('charCount');
    const wordCount = document.getElementById('wordCount');
    const readTime = document.getElementById('readTime');
    const charProgress = document.getElementById('charProgress');
    const progressText = document.getElementById('progressText');
    const submitBtn = document.getElementById('submitBtn');

    function updateStats() {
        const text = originalText.value;
        const chars = text.length;
        const words = text.trim().split(/\s+/).filter(w => w.length > 0).length;
        const minutes = Math.ceil(words / 200);

        charCount.textContent = chars.toLocaleString();
        wordCount.textContent = words.toLocaleString();
        readTime.textContent = `${minutes} min`;

        // Progress bar (optimal is 500+ words)
        const optimalWords = 500;
        const progress = Math.min((words / optimalWords) * 100, 100);
        charProgress.style.width = progress + '%';

        if (words < 100) {
            charProgress.classList.remove('bg-success', 'bg-warning');
            charProgress.classList.add('bg-danger');
            progressText.textContent = 'Minimum 100 words recommended';
            submitBtn.disabled = chars < 100;
        } else if (words < 300) {
            charProgress.classList.remove('bg-success', 'bg-danger');
            charProgress.classList.add('bg-warning');
            progressText.textContent = 'Good start! More text = better summary';
            submitBtn.disabled = false;
        } else {
            charProgress.classList.remove('bg-warning', 'bg-danger');
            charProgress.classList.add('bg-success');
            progressText.textContent = 'Perfect! Ready for high-quality summary';
            submitBtn.disabled = false;
        }
    }

    originalText.addEventListener('input', updateStats);
    updateStats();

    // Form validation
    document.getElementById('summaryForm').addEventListener('submit', function(e) {
        const text = originalText.value.trim();
        if (text.length < 100) {
            e.preventDefault();
            alert('Please enter at least 100 characters for a meaningful summary.');
            originalText.focus();
        }
    });
</script>
@endsection