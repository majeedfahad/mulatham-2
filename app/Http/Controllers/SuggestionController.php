<?php

namespace App\Http\Controllers;

use App\Models\Suggestion;
use App\Models\SuggestionVote;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SuggestionController extends Controller
{
    public function __construct(
        protected TelegramService $telegram
    ) {}

    /**
     * Show the suggestions leaderboard (public)
     */
    public function index()
    {
        $suggestions = Suggestion::approved()
            ->orderByVotes('desc')
            ->paginate(20);

        $sessionId = Session::getId();

        return view('suggestions.index', [
            'suggestions' => $suggestions,
            'sessionId' => $sessionId,
        ]);
    }

    /**
     * Show the feedback form
     */
    public function create(Request $request)
    {
        $roomCode = $request->query('room');

        return view('suggestions.create', [
            'roomCode' => $roomCode,
        ]);
    }

    /**
     * Store a new suggestion
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'author_name' => 'nullable|string|max:100',
            'category' => 'required|in:feature,bug,improvement,other',
            'room_id' => 'nullable|exists:rooms,id',
        ]);

        $suggestion = Suggestion::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'author_name' => $validated['author_name'] ?? null,
            'author_session_id' => Session::getId(),
            'category' => $validated['category'],
            'room_id' => $validated['room_id'] ?? null,
            'status' => 'pending',
        ]);

        // Send Telegram notification
        $this->telegram->notifyNewSuggestion(
            $validated['title'],
            $validated['description'],
            $validated['category'],
            $validated['author_name'] ?? null
        );

        return redirect()
            ->route('suggestions.index')
            ->with('success', 'تم إرسال اقتراحك بنجاح! سيتم عرضه بعد مراجعته.');
    }

    /**
     * Vote for a suggestion
     */
    public function vote(Suggestion $suggestion)
    {
        $sessionId = Session::getId();

        // Check if already voted
        if ($suggestion->hasVotedBy($sessionId)) {
            return back()->with('error', 'لقد صوّت مسبقاً لهذا الاقتراح');
        }

        // Only allow voting on approved suggestions
        if ($suggestion->status !== 'approved') {
            return back()->with('error', 'لا يمكن التصويت لهذا الاقتراح');
        }

        // Create vote
        SuggestionVote::create([
            'suggestion_id' => $suggestion->id,
            'voter_session_id' => $sessionId,
        ]);

        // Increment vote count
        $suggestion->incrementVotes();

        return back()->with('success', 'تم تسجيل تصويتك!');
    }

    /**
     * Remove vote from a suggestion
     */
    public function unvote(Suggestion $suggestion)
    {
        $sessionId = Session::getId();

        $vote = $suggestion->votes()->where('voter_session_id', $sessionId)->first();

        if (!$vote) {
            return back()->with('error', 'لم تصوّت لهذا الاقتراح');
        }

        $vote->delete();
        $suggestion->decrementVotes();

        return back()->with('success', 'تم إلغاء تصويتك');
    }
}
