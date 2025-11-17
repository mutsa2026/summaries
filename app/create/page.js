'use client'

import { useState } from 'react'
import { useRouter } from 'next/navigation'
import axios from 'axios'

export default function CreateSummary() {
  const router = useRouter()
  const [formData, setFormData] = useState({
    title: '',
    category: '',
    summary_length: 'medium',
    focus_areas: '',
    include_key_points: true,
    include_entities: false,
    sentiment_analysis: false,
    original_text: ''
  })
  const [loading, setLoading] = useState(false)
  const [charCount, setCharCount] = useState(0)
  const [wordCount, setWordCount] = useState(0)
  const [readTime, setReadTime] = useState(0)

  const handleInputChange = (e) => {
    const { name, value, type, checked } = e.target
    setFormData(prev => ({
      ...prev,
      [name]: type === 'checkbox' ? checked : value
    }))

    if (name === 'original_text') {
      updateStats(value)
    }
  }

  const updateStats = (text) => {
    const chars = text.length
    const words = text.trim().split(/\s+/).filter(w => w.length > 0).length
    const minutes = Math.ceil(words / 200)

    setCharCount(chars)
    setWordCount(words)
    setReadTime(minutes)
  }

  const handleSubmit = async (e) => {
    e.preventDefault()

    if (formData.original_text.trim().length < 100) {
      alert('Please enter at least 100 characters for a meaningful summary.')
      return
    }

    setLoading(true)
    document.getElementById('spinnerOverlay').classList.add('active')

    try {
      const response = await axios.post('/api/summaries', formData)
      router.push('/')
    } catch (error) {
      console.error('Error creating summary:', error)
      alert('Error creating summary. Please try again.')
    } finally {
      setLoading(false)
      document.getElementById('spinnerOverlay').classList.remove('active')
    }
  }

  const getProgressClass = () => {
    if (wordCount < 100) return 'bg-danger'
    if (wordCount < 300) return 'bg-warning'
    return 'bg-success'
  }

  const getProgressWidth = () => {
    const optimalWords = 500
    return Math.min((wordCount / optimalWords) * 100, 100)
  }

  const getProgressText = () => {
    if (wordCount < 100) return 'Minimum 100 words recommended'
    if (wordCount < 300) return 'Good start! More text = better summary'
    return 'Perfect! Ready for high-quality summary'
  }

  return (
    <div className="row justify-content-center">
      <div className="col-md-10">
        <div className="card">
          <div className="card-header">
            <h4 className="mb-0">
              <i className="fas fa-plus-circle me-2"></i>Create New AI Summary
            </h4>
            <p className="mb-0 mt-2 opacity-75">Transform your text into concise, intelligent summaries</p>
          </div>
          <div className="card-body p-4">
            <form onSubmit={handleSubmit}>
              <div className="row">
                {/* Left Column */}
                <div className="col-md-6">
                  <div className="mb-4">
                    <label htmlFor="title" className="form-label">
                      <i className="fas fa-heading text-primary me-2"></i>Title <span className="text-danger">*</span>
                    </label>
                    <input
                      type="text"
                      className="form-control form-control-lg"
                      id="title"
                      name="title"
                      placeholder="e.g., Climate Change Report 2024"
                      required
                      value={formData.title}
                      onChange={handleInputChange}
                    />
                    <div className="form-text">Give your summary a descriptive title</div>
                  </div>

                  <div className="mb-4">
                    <label htmlFor="category" className="form-label">
                      <i className="fas fa-tag text-success me-2"></i>Category <span className="text-danger">*</span>
                    </label>
                    <select
                      className="form-select form-select-lg"
                      id="category"
                      name="category"
                      required
                      value={formData.category}
                      onChange={handleInputChange}
                    >
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
                    <div className="form-text">Helps organize your summaries</div>
                  </div>

                  <div className="mb-4">
                    <label htmlFor="summary_length" className="form-label">
                      <i className="fas fa-sliders-h text-info me-2"></i>Summary Length
                    </label>
                    <select
                      className="form-select"
                      id="summary_length"
                      name="summary_length"
                      value={formData.summary_length}
                      onChange={handleInputChange}
                    >
                      <option value="short">Short (2-3 sentences)</option>
                      <option value="medium">Medium (1 paragraph)</option>
                      <option value="long">Long (2-3 paragraphs)</option>
                      <option value="detailed">Detailed (Comprehensive)</option>
                    </select>
                    <div className="form-text">Choose how detailed you want the summary</div>
                  </div>

                  <div className="mb-4">
                    <label htmlFor="focus_areas" className="form-label">
                      <i className="fas fa-bullseye text-warning me-2"></i>Focus Areas (Optional)
                    </label>
                    <input
                      type="text"
                      className="form-control"
                      id="focus_areas"
                      name="focus_areas"
                      placeholder="e.g., key findings, statistics, recommendations"
                      value={formData.focus_areas}
                      onChange={handleInputChange}
                    />
                    <div className="form-text">What aspects should the AI focus on?</div>
                  </div>

                  <div className="mb-4">
                    <div className="form-check form-switch">
                      <input
                        className="form-check-input"
                        type="checkbox"
                        id="include_key_points"
                        name="include_key_points"
                        checked={formData.include_key_points}
                        onChange={handleInputChange}
                      />
                      <label className="form-check-label" htmlFor="include_key_points">
                        <i className="fas fa-list-ul me-2"></i>Extract Key Points
                      </label>
                    </div>
                    <div className="form-check form-switch">
                      <input
                        className="form-check-input"
                        type="checkbox"
                        id="include_entities"
                        name="include_entities"
                        checked={formData.include_entities}
                        onChange={handleInputChange}
                      />
                      <label className="form-check-label" htmlFor="include_entities">
                        <i className="fas fa-user-tag me-2"></i>Identify Named Entities
                      </label>
                    </div>
                    <div className="form-check form-switch">
                      <input
                        className="form-check-input"
                        type="checkbox"
                        id="sentiment_analysis"
                        name="sentiment_analysis"
                        checked={formData.sentiment_analysis}
                        onChange={handleInputChange}
                      />
                      <label className="form-check-label" htmlFor="sentiment_analysis">
                        <i className="fas fa-smile me-2"></i>Sentiment Analysis
                      </label>
                    </div>
                  </div>
                </div>

                {/* Right Column */}
                <div className="col-md-6">
                  <div className="mb-4">
                    <label htmlFor="original_text" className="form-label">
                      <i className="fas fa-file-alt text-primary me-2"></i>Original Text <span className="text-danger">*</span>
                    </label>
                    <textarea
                      className="form-control"
                      id="original_text"
                      name="original_text"
                      rows="15"
                      required
                      minLength="100"
                      placeholder="Paste or type the text you want to summarize...

Tip: The more content you provide, the better the AI can analyze and summarize it. Minimum 100 characters recommended."
                      value={formData.original_text}
                      onChange={handleInputChange}
                    ></textarea>
                    <div id="textStats" className="mt-2 p-2 bg-light rounded">
                      <div className="row text-center">
                        <div className="col-4">
                          <small className="text-muted">Characters</small>
                          <div className="fw-bold">{charCount.toLocaleString()}</div>
                        </div>
                        <div className="col-4">
                          <small className="text-muted">Words</small>
                          <div className="fw-bold">{wordCount.toLocaleString()}</div>
                        </div>
                        <div className="col-4">
                          <small className="text-muted">Reading Time</small>
                          <div className="fw-bold">{readTime} min</div>
                        </div>
                      </div>
                      <div className="progress progress-thin mt-2">
                        <div
                          className={`progress-bar ${getProgressClass()}`}
                          role="progressbar"
                          style={{ width: `${getProgressWidth()}%` }}
                        ></div>
                      </div>
                      <small className="text-muted d-block mt-1">{getProgressText()}</small>
                    </div>
                  </div>

                  <div className="alert alert-info">
                    <i className="fas fa-lightbulb me-2"></i>
                    <strong>Pro Tips:</strong>
                    <ul className="mb-0 mt-2">
                      <li>Longer texts (500+ words) produce better summaries</li>
                      <li>Well-structured text with clear paragraphs works best</li>
                      <li>Remove unnecessary formatting before pasting</li>
                      <li>The AI preserves the most important information</li>
                    </ul>
                  </div>
                </div>
              </div>

              <hr className="my-4" />

              <div className="d-flex gap-3 justify-content-end">
                <a href="/" className="btn btn-outline-secondary btn-lg">
                  <i className="fas fa-times me-2"></i>Cancel
                </a>
                <button type="submit" className="btn btn-primary btn-lg" disabled={loading}>
                  <i className="fas fa-magic me-2"></i>Generate AI Summary
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  )
}