'use client'

import { useState, useEffect } from 'react'
import { useParams, useRouter } from 'next/navigation'
import axios from 'axios'

export default function EditSummary() {
  const params = useParams()
  const router = useRouter()
  const [summary, setSummary] = useState(null)
  const [loading, setLoading] = useState(true)
  const [saving, setSaving] = useState(false)
  const [formData, setFormData] = useState({
    title: '',
    original_text: '',
    summary: ''
  })

  useEffect(() => {
    if (params.id) {
      fetchSummary()
    }
  }, [params.id])

  const fetchSummary = async () => {
    try {
      const response = await axios.get(`/api/summaries/${params.id}`)
      const data = response.data
      setSummary(data)
      setFormData({
        title: data.title,
        original_text: data.original_text,
        summary: data.summary
      })
    } catch (error) {
      console.error('Error fetching summary:', error)
      if (error.response?.status === 404) {
        router.push('/')
      }
    } finally {
      setLoading(false)
    }
  }

  const handleInputChange = (e) => {
    const { name, value } = e.target
    setFormData(prev => ({
      ...prev,
      [name]: value
    }))
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    setSaving(true)

    try {
      await axios.put(`/api/summaries/${params.id}`, formData)
      router.push(`/summary/${params.id}`)
    } catch (error) {
      console.error('Error updating summary:', error)
      alert('Error updating summary. Please try again.')
    } finally {
      setSaving(false)
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
        <a href="/" className="btn btn-primary mt-3">Back to Summaries</a>
      </div>
    )
  }

  return (
    <div className="row justify-content-center">
      <div className="col-md-10">
        <div className="card">
          <div className="card-header">
            <h4 className="mb-0">
              <i className="fas fa-edit me-2"></i>Edit Summary
            </h4>
            <p className="mb-0 mt-2 opacity-75">Update your summary details</p>
          </div>
          <div className="card-body p-4">
            <form onSubmit={handleSubmit}>
              <div className="row">
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
                      required
                      value={formData.title}
                      onChange={handleInputChange}
                    />
                    <div className="form-text">Give your summary a descriptive title</div>
                  </div>

                  <div className="mb-4">
                    <label htmlFor="original_text" className="form-label">
                      <i className="fas fa-file-alt text-primary me-2"></i>Original Text <span className="text-danger">*</span>
                    </label>
                    <textarea
                      className="form-control"
                      id="original_text"
                      name="original_text"
                      rows="12"
                      required
                      value={formData.original_text}
                      onChange={handleInputChange}
                    ></textarea>
                    <div className="form-text">The original text that was summarized</div>
                  </div>
                </div>

                <div className="col-md-6">
                  <div className="mb-4">
                    <label htmlFor="summary" className="form-label">
                      <i className="fas fa-brain text-success me-2"></i>AI Summary <span className="text-danger">*</span>
                    </label>
                    <textarea
                      className="form-control"
                      id="summary"
                      name="summary"
                      rows="15"
                      required
                      value={formData.summary}
                      onChange={handleInputChange}
                    ></textarea>
                    <div className="form-text">The AI-generated summary of the text</div>
                  </div>
                </div>
              </div>

              <hr className="my-4" />

              <div className="d-flex gap-3 justify-content-end">
                <a href={`/summary/${summary.id}`} className="btn btn-outline-secondary btn-lg">
                  <i className="fas fa-times me-2"></i>Cancel
                </a>
                <button type="submit" className="btn btn-primary btn-lg" disabled={saving}>
                  <i className="fas fa-save me-2"></i>
                  {saving ? 'Saving...' : 'Save Changes'}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  )
}