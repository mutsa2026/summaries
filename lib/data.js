// Shared data store for the application
// In production, replace with a proper database

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

export function getSummaries() {
  return summaries
}

export function getSummaryById(id) {
  return summaries.find(s => s.id === parseInt(id))
}

export function createSummary(summaryData) {
  const newSummary = {
    id: summaries.length + 1,
    ...summaryData,
    created_at: new Date().toISOString(),
    updated_at: new Date().toISOString()
  }
  summaries.push(newSummary)
  return newSummary
}

export function updateSummary(id, updateData) {
  const index = summaries.findIndex(s => s.id === parseInt(id))
  if (index === -1) return null

  summaries[index] = {
    ...summaries[index],
    ...updateData,
    updated_at: new Date().toISOString()
  }
  return summaries[index]
}

export function deleteSummary(id) {
  const index = summaries.findIndex(s => s.id === parseInt(id))
  if (index === -1) return false

  summaries.splice(index, 1)
  return true
}

export function regenerateSummary(id, newSummary) {
  const index = summaries.findIndex(s => s.id === parseInt(id))
  if (index === -1) return null

  summaries[index] = {
    ...summaries[index],
    summary: newSummary,
    updated_at: new Date().toISOString()
  }
  return summaries[index]
}