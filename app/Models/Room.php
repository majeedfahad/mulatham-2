<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'status',
        'phase',
        'question_bank_started_at',
        'question_bank_duration',
        'max_questions_per_player',
        'acknowledged_players',
        'question_bank_timer_started',
        'max_questions',
        'min_players_to_end',
        'current_question_index',
        'revealing_player_id',
        'reveal_started_at',
    ];

    protected $casts = [
        'max_questions' => 'integer',
        'question_bank_duration' => 'integer',
        'max_questions_per_player' => 'integer',
        'acknowledged_players' => 'array',
        'question_bank_timer_started' => 'boolean',
        'min_players_to_end' => 'integer',
        'current_question_index' => 'integer',
        'reveal_started_at' => 'datetime',
        'question_bank_started_at' => 'datetime',
    ];

    /**
     * Generate a unique room code
     */
    public static function generateCode(): string
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    /**
     * Get all players in the room
     */
    public function players()
    {
        return $this->hasMany(RoomPlayer::class);
    }

    /**
     * Get the host player
     */
    public function host()
    {
        return $this->hasOne(RoomPlayer::class)->where('is_host', true);
    }

    /**
     * Get all questions for this room
     */
    public function questions()
    {
        return $this->hasMany(RoomQuestion::class)->orderBy('question_order');
    }

    /**
     * Get the current active question
     */
    public function currentQuestion()
    {
        return $this->hasOne(RoomQuestion::class)
            ->where('question_order', $this->current_question_index);
    }

    /**
     * Get all reveals in this room
     */
    public function reveals()
    {
        return $this->hasMany(Reveal::class);
    }

    /**
     * Get active (not eliminated/revealed) players
     */
    public function activePlayers()
    {
        return $this->players()->where('status', 'active');
    }

    /**
     * Get players who can still be revealed (active only)
     */
    public function revealablePlayers()
    {
        return $this->activePlayers();
    }

    /**
     * Check if game should end
     */
    public function shouldEndGame(): bool
    {
        $activeCount = $this->activePlayers()->count();

        // Use actual questions in bank instead of max_questions config
        $totalQuestionsInBank = $this->questions()->count();
        $questionsCompleted = $this->current_question_index >= $totalQuestionsInBank;

        return $activeCount <= $this->min_players_to_end || $questionsCompleted;
    }

    /**
     * Get total questions count (actual questions in bank)
     */
    public function getTotalQuestionsCount(): int
    {
        return $this->questions()->count();
    }

    /**
     * Check if room is in lobby
     */
    public function isInLobby(): bool
    {
        return $this->status === 'lobby';
    }

    /**
     * Check if game is playing
     */
    public function isPlaying(): bool
    {
        return $this->status === 'playing';
    }

    /**
     * Check if game is finished
     */
    public function isFinished(): bool
    {
        return $this->status === 'finished';
    }

    /**
     * Transfer host to next available player
     */
    public function transferHost(): ?RoomPlayer
    {
        $newHost = $this->players()
            ->where('is_host', false)
            ->whereIn('status', ['waiting', 'ready', 'active'])
            ->oldest()
            ->first();

        if ($newHost) {
            $this->players()->update(['is_host' => false]);
            $newHost->update(['is_host' => true]);
        }

        return $newHost;
    }

    /**
     * Get the player currently attempting a reveal
     */
    public function revealingPlayer()
    {
        return $this->belongsTo(RoomPlayer::class, 'revealing_player_id');
    }

    /**
     * Check if someone is currently in a reveal attempt
     */
    public function hasActiveReveal(): bool
    {
        return $this->revealing_player_id !== null;
    }

    /**
     * Clear the reveal lock
     */
    public function clearRevealLock(): void
    {
        $this->update([
            'revealing_player_id' => null,
            'reveal_started_at' => null,
        ]);
    }

    /**
     * Check if room is in question bank phase
     */
    public function isInQuestionBankPhase(): bool
    {
        return $this->phase === 'question_bank';
    }

    /**
     * Check if room is in answering phase
     */
    public function isInAnsweringPhase(): bool
    {
        return $this->phase === 'answering';
    }

    /**
     * Check if room is in revealing phase
     */
    public function isInRevealingPhase(): bool
    {
        return $this->phase === 'revealing';
    }

    /**
     * Start question bank phase
     */
    public function startQuestionBankPhase(): void
    {
        $this->update([
            'phase' => 'question_bank',
            'question_bank_started_at' => now(),
        ]);
    }

    /**
     * Get remaining time for question bank phase in seconds
     */
    public function getQuestionBankRemainingTime(): int
    {
        $total = $this->question_bank_duration ?? config('game.question_bank_timer', 60);

        // If timer hasn't started yet (waiting for acknowledgments), return full time
        if (! $this->question_bank_timer_started || ! $this->question_bank_started_at) {
            return $total;
        }

        $elapsed = now()->diffInSeconds($this->question_bank_started_at);
        $remaining = $total - $elapsed;

        return max(0, $remaining);
    }

    /**
     * Check if a player has acknowledged the question bank phase
     */
    public function hasPlayerAcknowledged(int $playerId): bool
    {
        $acknowledged = $this->acknowledged_players ?? [];

        return in_array($playerId, $acknowledged);
    }

    /**
     * Add a player to the acknowledged list
     */
    public function acknowledgePlayer(int $playerId): void
    {
        $acknowledged = $this->acknowledged_players ?? [];

        if (! in_array($playerId, $acknowledged)) {
            $acknowledged[] = $playerId;
            $this->update(['acknowledged_players' => $acknowledged]);
        }
    }

    /**
     * Check if all active players have acknowledged
     */
    public function allPlayersAcknowledged(): bool
    {
        $activePlayerIds = $this->activePlayers()->pluck('id')->toArray();
        $acknowledged = $this->acknowledged_players ?? [];

        foreach ($activePlayerIds as $playerId) {
            if (! in_array($playerId, $acknowledged)) {
                return false;
            }
        }

        return count($activePlayerIds) > 0;
    }

    /**
     * Get acknowledged players count
     */
    public function getAcknowledgedCount(): int
    {
        return count($this->acknowledged_players ?? []);
    }

    /**
     * Start the question bank timer (after all players acknowledged)
     */
    public function startQuestionBankTimer(): void
    {
        $this->update([
            'question_bank_started_at' => now(),
            'question_bank_timer_started' => true,
        ]);
    }

    /**
     * Check if question bank timer has started
     */
    public function hasQuestionBankTimerStarted(): bool
    {
        return $this->question_bank_timer_started ?? false;
    }

    /**
     * Check if question bank timer has expired
     */
    public function hasQuestionBankTimerExpired(): bool
    {
        return $this->getQuestionBankRemainingTime() <= 0;
    }

    /**
     * Get count of questions in the question bank for this room
     */
    public function getQuestionBankCount(): int
    {
        return $this->questions()
            ->where('status', 'pending')
            ->whereNotNull('question_text')
            ->where('question_text', '!=', '')
            ->count();
    }

    /**
     * Get a random unused question from the bank
     */
    public function getRandomPendingQuestion(): ?RoomQuestion
    {
        return $this->questions()
            ->where('status', 'pending')
            ->whereNotNull('question_text')
            ->where('question_text', '!=', '')
            ->inRandomOrder()
            ->first();
    }
}
