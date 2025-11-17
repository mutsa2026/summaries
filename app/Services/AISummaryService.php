<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AISummaryService
{
    public function generateSummary($text, $maxLength = 150)
    {
        // Try Ollama first (local, free)
        if (config('services.ollama.enabled', false)) {
            try {
                $summary = $this->summarizeWithOllama($text, $maxLength);
                if ($summary) return $summary;
            } catch (\Exception $e) {
                // Log error and continue to next
            }
        }

        // Try HuggingFace
        if (config('services.huggingface.key')) {
            try {
                $summary = $this->summarizeWithHuggingFace($text, $maxLength);
                if ($summary) return $summary;
            } catch (\Exception $e) {
                // Log error and continue
            }
        }

        // Try OpenRouter
        if (config('services.openrouter.key')) {
            try {
                $summary = $this->summarizeWithOpenRouter($text, $maxLength);
                if ($summary) return $summary;
            } catch (\Exception $e) {
                // Log error and continue
            }
        }

        // Fallback to simple algorithm
        return $this->simpleSummary($text, $maxLength);
    }

    private function summarizeWithOllama($text, $maxLength)
    {
        $response = Http::timeout(30)->post(config('services.ollama.base_url') . '/api/generate', [
            'model' => 'llama2', // or other model
            'prompt' => "Summarize the following text in {$maxLength} words or less:\n\n{$text}",
            'stream' => false,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return $data['response'] ?? null;
        }

        return null;
    }

    private function summarizeWithHuggingFace($text, $maxLength)
    {
        $response = Http::timeout(30)->withHeaders([
            'Authorization' => 'Bearer ' . config('services.huggingface.key'),
        ])->post('https://api-inference.huggingface.co/models/Falconsai/text_summarization', [
            'inputs' => $text,
            'parameters' => [
                'max_length' => $maxLength,
                'min_length' => 30,
                'do_sample' => false,
            ],
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return $data[0]['summary_text'] ?? null;
        }

        return null;
    }

    private function summarizeWithOpenRouter($text, $maxLength)
    {
        $response = Http::timeout(30)->withHeaders([
            'Authorization' => 'Bearer ' . config('services.openrouter.key'),
            'Content-Type' => 'application/json',
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => 'microsoft/wizardlm-2-8x22b', // Free tier model
            'messages' => [
                [
                    'role' => 'user',
                    'content' => "Summarize the following text in {$maxLength} words or less:\n\n{$text}",
                ],
            ],
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return $data['choices'][0]['message']['content'] ?? null;
        }

        return null;
    }

    private function simpleSummary($text, $maxLength)
    {
        $sentences = preg_split('/(?<=[.?!])\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        if (empty($sentences)) {
            return "No content to summarize.";
        }

        // Simple algorithm: take first sentence and key sentences
        $summary = $sentences[0];

        if (str_word_count($summary) < $maxLength / 2 && count($sentences) > 1) {
            $summary .= ' ' . $sentences[1];
        }

        // Ensure summary doesn't exceed max length
        $words = str_word_count($summary, 1);
        if (count($words) > $maxLength) {
            $words = array_slice($words, 0, $maxLength);
            $summary = implode(' ', $words) . '...';
        }

        return $summary;
    }

    public function calculateWordCount($text)
    {
        return str_word_count($text);
    }

    public function extractCategory($text)
    {
        $categories = ['Technology', 'Science', 'Business', 'Health', 'Education', 'Entertainment'];
        
        // Simple category detection based on keywords
        $text = strtolower($text);
        
        foreach ($categories as $category) {
            $keywords = $this->getCategoryKeywords(strtolower($category));
            foreach ($keywords as $keyword) {
                if (strpos($text, $keyword) !== false) {
                    return $category;
                }
            }
        }
        
        return 'General';
    }

    private function getCategoryKeywords($category)
    {
        $keywordMap = [
            'technology' => ['computer', 'software', 'ai', 'tech', 'digital', 'code', 'programming'],
            'science' => ['research', 'study', 'scientist', 'discovery', 'experiment', 'physics'],
            'business' => ['company', 'market', 'profit', 'investment', 'startup', 'enterprise'],
            'health' => ['medical', 'doctor', 'health', 'disease', 'treatment', 'hospital'],
            'education' => ['school', 'student', 'learn', 'teacher', 'university', 'course'],
            'entertainment' => ['movie', 'music', 'game', 'celebrity', 'film', 'show']
        ];

        return $keywordMap[$category] ?? [];
    }
}