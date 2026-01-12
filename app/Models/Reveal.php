<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reveal extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'room_question_id',
        'guesser_id',
        'target_id',
        'guessed_player_id',
        'is_correct',
        'points_transferred',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'points_transferred' => 'integer',
    ];

    /**
     * Get the room this reveal happened in
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the question during which this reveal happened
     */
    public function question()
    {
        return $this->belongsTo(RoomQuestion::class, 'room_question_id');
    }

    /**
     * Get the player who made the guess
     */
    public function guesser()
    {
        return $this->belongsTo(RoomPlayer::class, 'guesser_id');
    }

    /**
     * Get the fake name (target) being guessed
     */
    public function target()
    {
        return $this->belongsTo(RoomPlayer::class, 'target_id');
    }

    /**
     * Get the real player that the guesser thought was the target
     */
    public function guessedPlayer()
    {
        return $this->belongsTo(RoomPlayer::class, 'guessed_player_id');
    }

    /**
     * Process the reveal and handle scoring
     */
    public static function attempt(
        Room $room,
        RoomQuestion $question,
        RoomPlayer $guesser,
        RoomPlayer $targetFakeName,
        RoomPlayer $guessedRealPlayer
    ): self {
        // Check if guess is correct (target's real identity matches guessed player)
        $isCorrect = $targetFakeName->id === $guessedRealPlayer->id;

        $pointsTransferred = 0;

        if ($isCorrect) {
            // Correct guess: target's points go to guesser, target is revealed
            $pointsTransferred = $targetFakeName->transferPointsTo($guesser);
            $targetFakeName->reveal();
        } else {
            // Wrong guess: guesser's points go to target, guesser is eliminated
            $pointsTransferred = $guesser->transferPointsTo($targetFakeName);
            $guesser->eliminate();
        }

        return self::create([
            'room_id' => $room->id,
            'room_question_id' => $question->id,
            'guesser_id' => $guesser->id,
            'target_id' => $targetFakeName->id,
            'guessed_player_id' => $guessedRealPlayer->id,
            'is_correct' => $isCorrect,
            'points_transferred' => $pointsTransferred,
        ]);
    }
}
