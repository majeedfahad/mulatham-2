<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RoomPlayer extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'name',
        'fake_name',
        'score',
        'status',
        'is_host',
        'session_token',
        'last_seen_at',
    ];

    protected $casts = [
        'score' => 'integer',
        'is_host' => 'boolean',
        'last_seen_at' => 'datetime',
    ];

    protected $hidden = [
        'session_token',
    ];

    /**
     * Generate a unique session token
     */
    public static function generateToken(): string
    {
        return Str::random(64);
    }

    /**
     * Get the room this player belongs to
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get all answers by this player
     */
    public function answers()
    {
        return $this->hasMany(RoomAnswer::class);
    }

    /**
     * Get reveals where this player was the guesser
     */
    public function revealAttempts()
    {
        return $this->hasMany(Reveal::class, 'guesser_id');
    }

    /**
     * Get reveals where this player was the target
     */
    public function revealedBy()
    {
        return $this->hasMany(Reveal::class, 'target_id');
    }

    /**
     * Check if player is the host
     */
    public function isHost(): bool
    {
        return $this->is_host;
    }

    /**
     * Check if player is active (can still play)
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if player is eliminated
     */
    public function isEliminated(): bool
    {
        return $this->status === 'eliminated';
    }

    /**
     * Check if player has been revealed
     */
    public function isRevealed(): bool
    {
        return $this->status === 'revealed';
    }

    /**
     * Check if player can answer questions (active or revealed/ghost)
     */
    public function canAnswer(): bool
    {
        return in_array($this->status, ['active', 'revealed']);
    }

    /**
     * Check if player can attempt reveals
     */
    public function canReveal(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Mark player as ready in lobby
     */
    public function markReady(): void
    {
        $this->update(['status' => 'ready']);
    }

    /**
     * Mark player as active when game starts
     */
    public function activate(): void
    {
        $this->update(['status' => 'active']);
    }

    /**
     * Reveal this player (correct guess against them)
     */
    public function reveal(): void
    {
        $this->update(['status' => 'revealed']);
    }

    /**
     * Eliminate this player (wrong guess by them)
     */
    public function eliminate(): void
    {
        $this->update(['status' => 'eliminated']);
    }

    /**
     * Add points to score
     */
    public function addScore(int $points): void
    {
        $this->increment('score', $points);
    }

    /**
     * Add points (alias for addScore)
     */
    public function addPoints(int $points): void
    {
        $this->addScore($points);
    }

    /**
     * Subtract points from score
     */
    public function subtractPoints(int $points): void
    {
        $this->decrement('score', $points);
    }

    /**
     * Transfer points to another player
     */
    public function transferPointsTo(RoomPlayer $recipient): int
    {
        $points = $this->score;
        $this->update(['score' => 0]);
        $recipient->addScore($points);
        return $points;
    }

    /**
     * Update the last seen timestamp
     */
    public function updateLastSeen(): void
    {
        $this->update(['last_seen_at' => now()]);
    }

    /**
     * Check if player is online (seen within last 20 seconds)
     */
    public function isOnline(): bool
    {
        if (!$this->last_seen_at) {
            return false;
        }
        return $this->last_seen_at->diffInSeconds(now()) < 20;
    }

    /**
     * Check if player is offline (not seen for more than 20 seconds)
     */
    public function isOffline(): bool
    {
        return !$this->isOnline();
    }
}
