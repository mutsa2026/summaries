import { NextResponse } from 'next/server'

// Import the summaries array from the main route
// In a real app, this would be a database
let summaries = []

// This is a workaround since we can't easily share state between route files
// In production, use a proper database
try {
  // This won't work in production, but for demo purposes
  summaries = require('../route.js').summaries || []
} catch (e) {
  summaries = []
}

export async function GET(request, { params }) {
  const { id } = params
  const summary = summaries.find(s => s.id === parseInt(id))

  if (!summary) {
    return NextResponse.json({ error: 'Summary not found' }, { status: 404 })
  }

  return NextResponse.json(summary)
}

export async function PUT(request, { params }) {
  try {
    const { id } = params
    const body = await request.json()
    const { title, original_text, summary: newSummary } = body

    const summaryIndex = summaries.findIndex(s => s.id === parseInt(id))
    if (summaryIndex === -1) {
      return NextResponse.json({ error: 'Summary not found' }, { status: 404 })
    }

    const wordCount = original_text.trim().split(/\s+/).filter(w => w.length > 0).length
    const category = detectCategory(original_text)

    summaries[summaryIndex] = {
      ...summaries[summaryIndex],
      title,
      original_text,
      summary: newSummary,
      category,
      word_count: wordCount,
      updated_at: new Date().toISOString()
    }

    return NextResponse.json(summaries[summaryIndex])
  } catch (error) {
    console.error('Error updating summary:', error)
    return NextResponse.json({ error: 'Failed to update summary' }, { status: 500 })
  }
}

export async function DELETE(request, { params }) {
  const { id } = params
  const summaryIndex = summaries.findIndex(s => s.id === parseInt(id))

  if (summaryIndex === -1) {
    return NextResponse.json({ error: 'Summary not found' }, { status: 404 })
  }

  const deletedSummary = summaries.splice(summaryIndex, 1)[0]
  return NextResponse.json({ message: 'Summary deleted successfully' })
}

function detectCategory(text) {
  const categories = ['Technology', 'Science', 'Business', 'Health', 'Education', 'Entertainment']
  const textLower = text.toLowerCase()

  for (const category of categories) {
    const keywords = getCategoryKeywords(category.toLowerCase())
    if (keywords.some(keyword => textLower.includes(keyword))) {
      return category
    }
  }

  return 'General'
}

function getCategoryKeywords(category) {
  const keywordMap = {
    technology: ['computer', 'software', 'ai', 'tech', 'digital', 'code', 'programming'],
    science: ['research', 'study', 'scientist', 'discovery', 'experiment', 'physics'],
    business: ['company', 'market', 'profit', 'investment', 'startup', 'enterprise'],
    health: ['medical', 'doctor', 'health', 'disease', 'treatment', 'hospital'],
    education: ['school', 'student', 'learn', 'teacher', 'university', 'course'],
    entertainment: ['movie', 'music', 'game', 'celebrity', 'film', 'show']
  }

  return keywordMap[category] || []
}