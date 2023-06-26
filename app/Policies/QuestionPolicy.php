<?php

namespace App\Policies;

use App\Models\Question;
use App\Models\User;

class QuestionPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function before(User $user, $question)
    {
        if($user->isAdmin()){
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Question $question)
    {
        if(!$question->isActive()) return false;

        return $user->isEligibleToAnswer($question);

    }
}
