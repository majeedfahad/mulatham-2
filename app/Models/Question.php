<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'score',
        'type',
        'status',
    ];

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function answersUser()
    {
        return $this->hasMany(AnswerUser::class);
    }

    public function active()
    {
        $this->update(['status' => 1]);
    }
    public function deActive()
    {
        $this->update(['status' => 0]);
    }

    public function isActive()
    {
        return $this->status == 1;
    }

    public function isText()
    {
        return $this->type == 1;
    }
}
