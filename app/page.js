'use client'

import { useState, useEffect } from 'react'
import Link from 'next/link'
import axios from 'axios'

export default function Home() {
  const [summaries, setSummaries] = useState([])
  const [filteredSummaries, setFilteredSummaries] = useState([])
  const [loading, setLoading] = useState(true)
  const [searchTerm, setSearchTerm] = useState('')
  const [categoryFilter, setCategoryFilter] = useState('')
  const [sortBy, setSortBy] = useState('newest')

  useEffect(() => {
    fetchSummaries()
  }, [])

  useEffect(() => {
    filterAndSortSummaries()
  }, [summaries, searchTerm, categoryFilter, sortBy])

  const fetchSummaries = async () => {
    try {
      const response = await axios.get('/api/summaries')
      setSummaries(response.data)
    } catch (error) {
      console.error('Error fetching summaries:', error)
    } finally {
      setLoading(false)
    }
  }

  const filterAndSortSummaries = () => {
    let filtered = summaries.filter(summary => {
      const matchesSearch = summary.title.toLowerCase().includes(searchTerm.toLowerCase())
      const matchesCategory = !categoryFilter || summary.category === categoryFilter
      return matchesSearch && matchesCategory
    })

    filtered.sort((a, b) => {
      switch(sortBy) {
        case 'newest':
          return new Date(b.created_at) - new Date(a.created_at)
        case 'oldest':
          return new Date(a.created_at) - new Date(b.created_at)
        case 'longest':
          return b.word_count - a.word_count
        case 'shortest':
          return a.word_count - b.word_count
        default:
          return 0
      }
    })

    setFilteredSummaries(filtered)
  }

  const handleDelete = async (id) => {
    if (!confirm('Are you sure you want to delete this summary?')) return

    try {
      await axios.delete(`/api/summaries/${id}`)
      setSummaries(summaries.filter(summary => summary.id !== id))
    } catch (error) {
      console.error('Error deleting summary:', error)
    }
  }

  const resetFilters = () => {
    setSearchTerm('')
    setCategoryFilter('')
    setSortBy('newest')
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

  const totalSummaries = summaries.length
  const totalWords = summaries.reduce((sum, s) => sum + s.word_count, 0)
  const uniqueCategories = new Set(summaries.map(s => s.category)).size
  const avgReadingTime = Math.round(totalWords / 200)

  return (
    <div>
      <div className="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <div>
          <h1 className="mb-0"><i className="fas fa-file-alt text-primary"></i> My Summaries</h1>
          <p className="text-muted mt-2">AI-powered text summarization at your fingertips</p>
        </div>
        <Link href="/create" className="btn btn-primary">
          <i className="fas fa-plus-circle me-2"></i>Create New Summary
        </Link>
      </div>

      {totalSummaries > 0 && (
        <>
          {/* Stats Overview */}
          <div className="row mb-4">
            <div className="col-md-3 col-sm-6 mb-3">
              <div className="card text-center">
                <div className="card-body">
                  <i className="fas fa-file-alt fa-2x text-primary mb-2"></i>
                  <h3 className="mb-0">{totalSummaries}</h3>
                  <p className="text-muted mb-0">Total Summaries</p>
                </div>
              </div>
            </div>
            <div className="col-md-3 col-sm-6 mb-3">
              <div className="card text-center">
                <div className="card-body">
                  <i className="fas fa-book fa-2x text-success mb-2"></i>
                  <h3 className="mb-0">{totalWords.toLocaleString()}</h3>
                  <p className="text-muted mb-0">Words Processed</p>
                </div>
              </div>
            </div>
            <div className="col-md-3 col-sm-6 mb-3">
              <div className="card text-center">
                <div className="card-body">
                  <i className="fas fa-tags fa-2x text-info mb-2"></i>
                  <h3 className="mb-0">{uniqueCategories}</h3>
                  <p className="text-muted mb-0">Categories</p>
                </div>
              </div>
            </div>
            <div className="col-md-3 col-sm-6 mb-3">
              <div className="card text-center">
                <div className="card-body">
                  <i className="fas fa-clock fa-2x text-warning mb-2"></i>
                  <h3 className="mb-0">{avgReadingTime}</h3>
                  <p className="text-muted mb-0">Min. Reading Time</p>
                </div>
              </div>
            </div>
          </div>

          {/* Filter Section */}
          <div className="card mb-4">
            <div className="card-body">
              <div className="row align-items-end">
                <div className="col-md-4 mb-2">
                  <label className="form-label"><i className="fas fa-search me-2"></i>Search</label>
                  <input
                    type="text"
                    className="form-control"
                    placeholder="Search summaries..."
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                  />
                </div>
                <div className="col-md-3 mb-2">
                  <label className="form-label"><i className="fas fa-filter me-2"></i>Category</label>
                  <select
                    className="form-select"
                    value={categoryFilter}
                    onChange={(e) => setCategoryFilter(e.target.value)}
                  >
                    <option value="">All Categories</option>
                    {[...new Set(summaries.map(s => s.category))].map(category => (
                      <option key={category} value={category}>{category}</option>
                    ))}
                  </select>
                </div>
                <div className="col-md-3 mb-2">
                  <label className="form-label"><i className="fas fa-sort me-2"></i>Sort By</label>
                  <select
                    className="form-select"
                    value={sortBy}
                    onChange={(e) => setSortBy(e.target.value)}
                  >
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="longest">Longest First</option>
                    <option value="shortest">Shortest First</option>
                  </select>
                </div>
                <div className="col-md-2 mb-2">
                  <button className="btn btn-outline-secondary w-100" onClick={resetFilters}>
                    <i className="fas fa-redo me-2"></i>Reset
                  </button>
                </div>
              </div>
            </div>
          </div>

          {/* Summaries Grid */}
          <div className="row">
            {filteredSummaries.map(summary => (
              <div key={summary.id} className="col-md-6 col-lg-4 mb-4">
                <div className="card summary-card h-100">
                  <div className="card-body">
                    <div className="d-flex justify-content-between align-items-start mb-3">
                      <h5 className="card-title mb-0">
                        <i className="fas fa-document text-primary me-2"></i>
                        {summary.title}
                      </h5>
                    </div>

                    <div className="mb-3">
                      <span className="stat-badge category-badge">
                        <i className="fas fa-tag"></i> {summary.category}
                      </span>
                      <span className="stat-badge word-count-badge">
                        <i className="fas fa-font"></i> {summary.word_count} words
                      </span>
                      <span className="stat-badge reading-time-badge">
                        <i className="fas fa-clock"></i> {Math.round(summary.word_count / 200)} min read
                      </span>
                    </div>

                    <p className="card-text text-muted">
                      <i className="fas fa-align-left me-2"></i>
                      {summary.summary.length > 120 ? summary.summary.substring(0, 120) + '...' : summary.summary}
                    </p>

                    <div className="mt-3">
                      <small className="text-muted">
                        <i className="far fa-calendar-alt me-1"></i>
                        {new Date(summary.created_at).toLocaleDateString()}
                      </small>
                    </div>
                  </div>
                  <div className="card-footer bg-transparent border-0">
                    <div className="btn-group-custom">
                      <Link href={`/summary/${summary.id}`} className="btn btn-primary action-btn btn-sm flex-fill">
                        <i className="fas fa-eye me-1"></i> View
                      </Link>
                      <Link href={`/edit/${summary.id}`} className="btn btn-outline-secondary action-btn btn-sm flex-fill">
                        <i className="fas fa-edit me-1"></i> Edit
                      </Link>
                      <button
                        onClick={() => handleDelete(summary.id)}
                        className="btn btn-outline-danger action-btn btn-sm flex-fill"
                      >
                        <i className="fas fa-trash me-1"></i> Delete
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            ))}
          </div>

          {filteredSummaries.length === 0 && (
            <div className="alert alert-info text-center">
              <i className="fas fa-search fa-2x mb-2"></i>
              <p className="mb-0">No summaries found matching your criteria.</p>
            </div>
          )}
        </>
      )}

      {totalSummaries === 0 && (
        <div className="empty-state">
          <i className="fas fa-inbox"></i>
          <h3 className="mt-3">No Summaries Yet</h3>
          <p className="text-muted">Start by creating your first AI-powered summary!</p>
          <Link href="/create" className="btn btn-primary btn-lg mt-3">
            <i className="fas fa-plus-circle me-2"></i>Create Your First Summary
          </Link>
        </div>
      )}
    </div>
  )
}