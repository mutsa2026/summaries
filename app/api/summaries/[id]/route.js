import { NextResponse } from 'next/server'
import { getSummaryById, updateSummary, deleteSummary } from '../../../../lib/data'

export async function GET(request, { params }) {
  const { id } = params
  const summary = getSummaryById(id)

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

    const wordCount = original_text.trim().split(/\s+/).filter(w => w.length > 0).length
    const category = detectCategory(original_text)

    const updateData = {
      title,
      original_text,
      summary: newSummary,
      category,
      word_count: wordCount
    }

    const updatedSummary = updateSummary(id, updateData)
    if (!updatedSummary) {
      return NextResponse.json({ error: 'Summary not found' }, { status: 404 })
    }

    return NextResponse.json(updatedSummary)
  } catch (error) {
    console.error('Error updating summary:', error)
    return NextResponse.json({ error: 'Failed to update summary' }, { status: 500 })
  }
}

export async function DELETE(request, { params }) {
  const { id } = params
  const success = deleteSummary(id)

  if (!success) {
    return NextResponse.json({ error: 'Summary not found' }, { status: 404 })
  }

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