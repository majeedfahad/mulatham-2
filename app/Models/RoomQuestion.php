<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'creator_id',
        'question_text',
        'question_type',
        'choices',
        'correct_choice_index',
        'correct_answer',
        'question_order',
        'round_number',
        'is_selected',
        'status',
    ];

    protected $casts = [
        'question_order' => 'integer',
        'round_number' => 'integer',
        'is_selected' => 'boolean',
        'choices' => 'array',
        'correct_choice_index' => 'integer',
    ];

    /**
     * Get the room this question belongs to
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the player who created this question
     */
    public function creator()
    {
        return $this->belongsTo(RoomPlayer::class, 'creator_id');
    }

    /**
     * Get all answers to this question
     */
    public function answers()
    {
        return $this->hasMany(RoomAnswer::class);
    }

    /**
     * Get reveals that happened during this question
     */
    public function reveals()
    {
        return $this->hasMany(Reveal::class);
    }

    /**
     * Check if all online players have answered (including creator since everyone answers now)
     * Offline players (no heartbeat for 20+ seconds) are excluded from the count
     */
    public function allPlayersAnswered(): bool
    {
        $answerablePlayers = $this->room->players()
            ->whereIn('status', ['active', 'revealed'])
            ->get();

        // Count only online players
        $onlinePlayersCount = $answerablePlayers->filter(function ($player) {
            return $player->isOnline();
        })->count();

        // If no one is online, consider all answered to avoid stuck state
        if ($onlinePlayersCount === 0) {
            return true;
        }

        return $this->answers()->count() >= $onlinePlayersCount;
    }

    /**
     * Get count of online answerable players
     */
    public function getOnlineAnswerablePlayersCount(): int
    {
        return $this->room->players()
            ->whereIn('status', ['active', 'revealed'])
            ->get()
            ->filter(fn($p) => $p->isOnline())
            ->count();
    }

    /**
     * Get count of players who have answered
     */
    public function getAnsweredCount(): int
    {
        return $this->answers()->count();
    }

    /**
     * Check if all players have submitted their questions for this round
     */
    public function allPlayersSubmittedQuestions(): bool
    {
        $activePlayersCount = $this->room->players()
            ->whereIn('status', ['active', 'revealed'])
            ->count();

        $submittedCount = $this->room->questions()
            ->where('round_number', $this->round_number)
            ->whereNotNull('question_text')
            ->where('question_text', '!=', '')
            ->count();

        return $submittedCount >= $activePlayersCount;
    }

    /**
     * Check if this question is selected for the round
     */
    public function isSelected(): bool
    {
        return $this->is_selected;
    }

    /**
     * Mark this question as selected
     */
    public function select(): void
    {
        $this->update(['is_selected' => true]);
    }

    /**
     * Check if in writing phase
     */
    public function isWriting(): bool
    {
        return $this->status === 'writing';
    }

    /**
     * Start writing phase for a new question
     */
    public function startWriting(): void
    {
        $this->update(['status' => 'writing']);
    }

    /**
     * Award points to creator for wrong answers
     */
    public function awardCreatorPoints(): int
    {
        if (!$this->creator_id) {
            return 0;
        }

        $wrongAnswers = $this->answers()->where('is_correct', false)->count();

        if ($wrongAnswers > 0) {
            $this->creator->addPoints($wrongAnswers);
        }

        return $wrongAnswers;
    }

    /**
     * Get answer by a specific player
     */
    public function getAnswerByPlayer(RoomPlayer $player): ?RoomAnswer
    {
        return $this->answers()->where('room_player_id', $player->id)->first();
    }

    /**
     * Check if player has answered
     */
    public function hasPlayerAnswered(RoomPlayer $player): bool
    {
        return $this->answers()->where('room_player_id', $player->id)->exists();
    }

    /**
     * Start answering phase
     */
    public function startAnswering(): void
    {
        $this->update(['status' => 'answering']);
    }

    /**
     * Start reveal phase
     */
    public function startRevealing(): void
    {
        $this->update(['status' => 'revealing']);
    }

    /**
     * Mark as completed
     */
    public function complete(): void
    {
        $this->update(['status' => 'completed']);
    }

    /**
     * Check if in answering phase
     */
    public function isAnswering(): bool
    {
        return $this->status === 'answering';
    }

    /**
     * Check if in revealing phase
     */
    public function isRevealing(): bool
    {
        return $this->status === 'revealing';
    }

    /**
     * Check if completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if this is a multiple choice question
     */
    public function isMultipleChoice(): bool
    {
        return $this->question_type === 'choice';
    }

    /**
     * Check if this is a text answer question
     */
    public function isTextAnswer(): bool
    {
        return $this->question_type === 'text';
    }

    /**
     * Get the correct answer (works for both types)
     */
    public function getCorrectAnswer(): ?string
    {
        if ($this->isMultipleChoice() && $this->choices && isset($this->correct_choice_index)) {
            return $this->choices[$this->correct_choice_index] ?? null;
        }
        return $this->correct_answer;
    }

    /**
     * Check if an answer is correct
     */
    public function isAnswerCorrect(string $answer): bool
    {
        if ($this->isMultipleChoice()) {
            // For multiple choice, check if the answer matches the correct choice
            $correctAnswer = $this->getCorrectAnswer();
            return $correctAnswer && mb_strtolower(trim($answer)) === mb_strtolower(trim($correctAnswer));
        }
        // For text answers, case-insensitive comparison
        return mb_strtolower(trim($answer)) === mb_strtolower(trim($this->correct_answer));
    }

    /**
     * Check if pending (in question bank, not yet used)
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
