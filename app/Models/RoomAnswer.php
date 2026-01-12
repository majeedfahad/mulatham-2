<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_question_id',
        'room_player_id',
        'answer',
        'is_correct',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    /**
     * Get the question this answer belongs to
     */
    public function question()
    {
        return $this->belongsTo(RoomQuestion::class, 'room_question_id');
    }

    /**
     * Get the player who submitted this answer
     */
    public function player()
    {
        return $this->belongsTo(RoomPlayer::class, 'room_player_id');
    }

    /**
     * Mark answer as correct and award point
     */
    public function markCorrect(): void
    {
        if (!$this->is_correct) {
            $this->update(['is_correct' => true]);

            // Only award points if player is active (not ghost)
            if ($this->player->isActive()) {
                $this->player->addScore(1);
            }
        }
    }

    /**
     * Mark answer as incorrect
     */
    public function markIncorrect(): void
    {
        $this->update(['is_correct' => false]);
    }
}
