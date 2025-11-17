<?php

namespace App\Http\Controllers;

use App\Models\Summary;
use App\Services\AISummaryService;
use Illuminate\Http\Request;

class SummaryController extends Controller
{
    protected $aiService;

    public function __construct()
    {
        $this->aiService = new AISummaryService();
    }

    public function index()
    {
        $summaries = Summary::latest()->get();
        return view('summaries.index', compact('summaries'));
    }

    public function create()
    {
        return view('summaries.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'original_text' => 'required|string',
        ]);

        // Generate AI summary
        $summary = $this->aiService->generateSummary($request->original_text);
        $wordCount = $this->aiService->calculateWordCount($request->original_text);
        $category = $this->aiService->extractCategory($request->original_text);

        Summary::create([
            'title' => $request->title,
            'original_text' => $request->original_text,
            'summary' => $summary,
            'category' => $category,
            'word_count' => $wordCount,
        ]);

        return redirect()->route('summaries.index')
            ->with('success', 'Summary created successfully!');
    }

    public function show(Summary $summary)
    {
        return view('summaries.show', compact('summary'));
    }

    public function edit(Summary $summary)
    {
        return view('summaries.edit', compact('summary'));
    }

    public function update(Request $request, Summary $summary)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'original_text' => 'required|string',
            'summary' => 'required|string',
        ]);

        $wordCount = $this->aiService->calculateWordCount($request->original_text);
        $category = $this->aiService->extractCategory($request->original_text);

        $summary->update([
            'title' => $request->title,
            'original_text' => $request->original_text,
            'summary' => $request->summary,
            'category' => $category,
            'word_count' => $wordCount,
        ]);

        return redirect()->route('summaries.index')
            ->with('success', 'Summary updated successfully!');
    }

    public function destroy(Summary $summary)
    {
        $summary->delete();

        return redirect()->route('summaries.index')
            ->with('success', 'Summary deleted successfully!');
    }

    public function regenerateSummary(Request $request, Summary $summary)
    {
        $newSummary = $this->aiService->generateSummary($summary->original_text);
        
        $summary->update([
            'summary' => $newSummary,
        ]);

        return redirect()->route('summaries.show', $summary)
            ->with('success', 'Summary regenerated successfully!');
    }
}