'use client'

import { useState, useEffect } from 'react'
import { useParams, useRouter } from 'next/navigation'
import Link from 'next/link'
import axios from 'axios'

export default function ShowSummary() {
  const params = useParams()
  const router = useRouter()
  const [summary, setSummary] = useState(null)
  const [loading, setLoading] = useState(true)
  const [regenerating, setRegenerating] = useState(false)

  useEffect(() => {
    if (params.id) {
      fetchSummary()
    }
  }, [params.id])

  const fetchSummary = async () => {
    try {
      const response = await axios.get(`/api/summaries/${params.id}`)
      setSummary(response.data)
    } catch (error) {
      console.error('Error fetching summary:', error)
      if (error.response?.status === 404) {
        router.push('/')
      }
    } finally {
      setLoading(false)
    }
  }

  const handleRegenerate = async () => {
    if (!confirm('Are you sure you want to regenerate this summary?')) return

    setRegenerating(true)
    document.getElementById('spinnerOverlay').classList.add('active')

    try {
      const response = await axios.post(`/api/summaries/${params.id}/regenerate`)
      setSummary(response.data)
    } catch (error) {
      console.error('Error regenerating summary:', error)
      alert('Error regenerating summary. Please try again.')
    } finally {
      setRegenerating(false)
      document.getElementById('spinnerOverlay').classList.remove('active')
    }
  }

  const handleDelete = async () => {
    if (!confirm('Are you sure you want to delete this summary?')) return

    try {
      await axios.delete(`/api/summaries/${params.id}`)
      router.push('/')
    } catch (error) {
      console.error('Error deleting summary:', error)
    }
  }

  if (loading) {
    return (
      <div className="text-center py-5">
        <div className="spinner-border text-primary" role="status">
          <span className="visually-hidden">Loading...</span>
        </div>
      </div>
    )
  }

  if (!summary) {
    return (
      <div className="text-center py-5">
        <h3>Summary not found</h3>
        <Link href="/" className="btn btn-primary mt-3">Back to Summaries</Link>
      </div>
    )
  }

  return (
    <div>
      <div className="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <div>
          <h1 className="mb-0">
            <i className="fas fa-document text-primary me-2"></i>
            {summary.title}
          </h1>
          <p className="text-muted mt-2">
            Created {new Date(summary.created_at).toLocaleDateString()}
          </p>
        </div>
        <div className="btn-group-custom">
          <Link href={`/edit/${summary.id}`} className="btn btn-outline-secondary">
            <i className="fas fa-edit me-2"></i>Edit
          </Link>
          <button
            onClick={handleRegenerate}
            className="btn btn-outline-info"
            disabled={regenerating}
          >
            <i className="fas fa-sync me-2"></i>
            {regenerating ? 'Regenerating...' : 'Regenerate'}
          </button>
          <button onClick={handleDelete} className="btn btn-outline-danger">
            <i className="fas fa-trash me-2"></i>Delete
          </button>
          <Link href="/" className="btn btn-outline-secondary">
            <i className="fas fa-arrow-left me-2"></i>Back
          </Link>
        </div>
      </div>

      <div className="row">
        <div className="col-md-8">
          {/* Summary Section */}
          <div className="card mb-4">
            <div className="card-header">
              <h5 className="mb-0">
                <i className="fas fa-brain text-primary me-2"></i>AI Summary
              </h5>
            </div>
            <div className="card-body">
              <div className="summary-section">
                <p className="mb-0">{summary.summary}</p>
              </div>
            </div>
          </div>

          {/* Original Text Section */}
          <div className="card">
            <div className="card-header">
              <h5 className="mb-0">
                <i className="fas fa-file-alt text-primary me-2"></i>Original Text
              </h5>
            </div>
            <div className="card-body">
              <div className="text-section">
                <p className="mb-0">{summary.original_text}</p>
              </div>
            </div>
          </div>
        </div>

        <div className="col-md-4">
          {/* Stats Card */}
          <div className="card mb-4">
            <div className="card-header">
              <h5 className="mb-0">
                <i className="fas fa-chart-bar text-primary me-2"></i>Statistics
              </h5>
            </div>
            <div className="card-body">
              <div className="mb-3">
                <span className="stat-badge category-badge">
                  <i className="fas fa-tag"></i> {summary.category}
                </span>
              </div>

              <div className="mb-3">
                <span className="stat-badge word-count-badge">
                  <i className="fas fa-font"></i> {summary.word_count} words
                </span>
              </div>

              <div className="mb-3">
                <span className="stat-badge reading-time-badge">
                  <i className="fas fa-clock"></i> {Math.round(summary.word_count / 200)} min read
                </span>
              </div>

              <div className="text-muted small">
                <i className="far fa-calendar-alt me-1"></i>
                Last updated: {new Date(summary.updated_at).toLocaleDateString()}
              </div>
            </div>
          </div>

          {/* Actions Card */}
          <div className="card">
            <div className="card-header">
              <h5 className="mb-0">
                <i className="fas fa-cogs text-primary me-2"></i>Actions
              </h5>
            </div>
            <div className="card-body">
              <div className="d-grid gap-2">
                <Link href={`/edit/${summary.id}`} className="btn btn-primary">
                  <i className="fas fa-edit me-2"></i>Edit Summary
                </Link>
                <button
                  onClick={handleRegenerate}
                  className="btn btn-info"
                  disabled={regenerating}
                >
                  <i className="fas fa-sync me-2"></i>
                  {regenerating ? 'Regenerating...' : 'Regenerate Summary'}
                </button>
                <button onClick={handleDelete} className="btn btn-danger">
                  <i className="fas fa-trash me-2"></i>Delete Summary
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}