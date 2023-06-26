<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnswerUser extends Model
{
    protected $table = 'question_user';

    protected $fillable = [
        'user_id',
        'question_id',
        'answer_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function answer()
    {
        return $this->belongsTo(Answer::class);
    }

    public function isCorrect()
    {
        return Answer::find($this->answer_id)->iscorrect == 1;
    }

    public function assignScore($question_id)
    {
        $question = Question::find($question_id);
        if(!$question->isText()) {
            $this->isCorrect() ? $this->score = Question::find($question_id)->score : $this->score = 0;
        }
        $this->update();
    }

    public function hasAnswered()
    {
        return $this->score != -1;
    }
}
