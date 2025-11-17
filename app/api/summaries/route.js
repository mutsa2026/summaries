import { NextResponse } from 'next/server'

// In-memory storage for demo purposes
// In production, you'd use a database
let summaries = [
  {
    id: 1,
    title: 'Sample Summary',
    original_text: 'This is a sample text for demonstration purposes. It contains enough content to generate a meaningful summary.',
    summary: 'This sample text demonstrates the summarization functionality.',
    category: 'General',
    word_count: 15,
    created_at: new Date().toISOString(),
    updated_at: new Date().toISOString()
  }
]

export async function GET() {
  return NextResponse.json(summaries)
}

export async function POST(request) {
  try {
    const body = await request.json()
    const { title, original_text, category } = body

    // Generate AI summary (simplified for demo)
    const summary = await generateSummary(original_text)
    const wordCount = original_text.trim().split(/\s+/).filter(w => w.length > 0).length
    const detectedCategory = category || detectCategory(original_text)

    const newSummary = {
      id: summaries.length + 1,
      title,
      original_text,
      summary,
      category: detectedCategory,
      word_count: wordCount,
      created_at: new Date().toISOString(),
      updated_at: new Date().toISOString()
    }

    summaries.push(newSummary)
    return NextResponse.json(newSummary, { status: 201 })
  } catch (error) {
    console.error('Error creating summary:', error)
    return NextResponse.json({ error: 'Failed to create summary' }, { status: 500 })
  }
}

async function generateSummary(text) {
  // Simple fallback summarization
  const sentences = text.split(/[.!?]+/).filter(s => s.trim().length > 0)
  if (sentences.length === 0) return 'No content to summarize.'

  // Take first sentence and maybe second if it's not too long
  let summary = sentences[0].trim()
  if (sentences.length > 1 && summary.length < 100) {
    summary += '. ' + sentences[1].trim()
  }

  // Ensure summary isn't too long
  if (summary.length > 150) {
    summary = summary.substring(0, 147) + '...'
  }

  return summary
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