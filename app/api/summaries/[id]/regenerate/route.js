import { NextResponse } from 'next/server'
import { getSummaryById, regenerateSummary } from '../../../../lib/data'

export async function POST(request, { params }) {
  try {
    const { id } = params
    const existingSummary = getSummaryById(id)

    if (!existingSummary) {
      return NextResponse.json({ error: 'Summary not found' }, { status: 404 })
    }

    const newSummaryText = await generateSummary(existingSummary.original_text)
    const updatedSummary = regenerateSummary(id, newSummaryText)

    if (!updatedSummary) {
      return NextResponse.json({ error: 'Failed to regenerate summary' }, { status: 500 })
    }

    return NextResponse.json(updatedSummary)
  } catch (error) {
    console.error('Error regenerating summary:', error)
    return NextResponse.json({ error: 'Failed to regenerate summary' }, { status: 500 })
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