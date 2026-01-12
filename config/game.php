<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Game Timer Settings
    |--------------------------------------------------------------------------
    |
    | These settings control the various timers in the game.
    | All values are in seconds.
    |
    */

    // Time allowed for question bank phase (all players write questions at game start)
    'question_bank_timer' => env('GAME_QUESTION_BANK_TIMER', 60),

    // Maximum questions each player can submit during question bank phase
    'max_questions_per_player' => env('GAME_MAX_QUESTIONS_PER_PLAYER', 5),

    // Time allowed for writing a question (legacy - kept for backwards compatibility)
    'writing_timer' => env('GAME_WRITING_TIMER', 15),

    // Time allowed for reveal attempt (choosing who to reveal)
    'reveal_timer' => env('GAME_REVEAL_TIMER', 10),

    // Countdown before moving to next question
    'next_question_countdown' => env('GAME_NEXT_QUESTION_COUNTDOWN', 5),

    /*
    |--------------------------------------------------------------------------
    | Game Rules Settings
    |--------------------------------------------------------------------------
    */

    // Maximum number of questions per game
    'max_questions' => env('GAME_MAX_QUESTIONS', 8),

    // Minimum players required to start a game
    'min_players' => env('GAME_MIN_PLAYERS', 3),

    // Minimum players remaining to end game (end when this many or fewer remain unrevealed)
    'min_players_to_end' => env('GAME_MIN_PLAYERS_TO_END', 2),
];
