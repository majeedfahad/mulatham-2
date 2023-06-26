<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isAlive()
    {
        return $this->status == 1;
    }

    public function isAdmin()
    {
        return $this->role == 1;
    }

    public function scopeCompetitors($query)
    {
        return $query->where('role', 0);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeEliminated($query)
    {
        return $query->where('status', 0);
    }

    public function answers()
    {
        return $this->hasMany(AnswerUser::class);
    }

    public function hasAnsweredQuestion($question)
    {
        return $this->answers->map->question->flatten()->contains($question);
    }

    public function assignQuestionScore($question_id)
    {
        $answer = $this->answers()->where('question_id', $question_id)->first();
        if($answer) {
            $this->score += $answer->score;
            $this->update();
        }
    }

    public function eliminate()
    {
        $this->update(['status' => 0]);
    }

    public function assignFailedEliminationScore($score)
    {
        $this->score += $score;
        $this->update();
    }

    public function assignSuccessEliminationScore($score)
    {
        $this->hidden_score += $score;
        $this->update();
    }

    public function isEligibleToAnswer($question)
    {
        if($this->isAlive() && !$this->hasAnsweredQuestion($question)) {
            return true;
        }
        return false;
    }

    public function getTotalScore()
    {
        return $this->score + $this->hidden_score;
    }

    public static function getWinners()
    {
        $winners = User::query()
            ->competitors()
            ->active()
            ->get()
            ->sortByDesc(function ($user) {
                return $user->getTotalScore();
            });

        return $winners->values()->all();
    }
}
