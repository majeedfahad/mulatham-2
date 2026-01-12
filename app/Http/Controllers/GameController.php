<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomPlayer;
use App\Models\RoomQuestion;
use App\Models\RoomAnswer;
use App\Models\Reveal;
use App\Events\GameStateUpdated;
use App\Events\PlayerUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class GameController extends Controller
{
    /**
     * Show the landing page
     */
    public function landing()
    {
        return view('game.landing');
    }

    /**
     * Create a new game room
     */
    public function createRoom(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'fake_name' => 'required|string|max:50',
        ]);

        // Create the room
        $room = Room::create([
            'code' => Room::generateCode(),
            'status' => 'lobby',
        ]);

        // Create the host player
        $token = RoomPlayer::generateToken();
        $player = RoomPlayer::create([
            'room_id' => $room->id,
            'name' => $request->name,
            'fake_name' => $request->fake_name,
            'is_host' => true,
            'status' => 'waiting',
            'session_token' => $token,
        ]);

        // Store token in session
        session(['player_token' => $token, 'room_code' => $room->code]);

        // Broadcast player joined
        broadcast(new PlayerUpdated($room, 'joined', $player))->toOthers();

        return redirect()->route('game.lobby', $room->code);
    }

    /**
     * Join an existing room
     */
    public function joinRoom(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
            'name' => 'required|string|max:50',
            'fake_name' => 'required|string|max:50',
        ]);

        $room = Room::where('code', strtoupper($request->code))->first();

        if (!$room) {
            return back()->withErrors(['code' => 'رمز الغرفة غير صحيح']);
        }

        if (!$room->isInLobby()) {
            return back()->withErrors(['code' => 'اللعبة بدأت بالفعل']);
        }

        // Check if fake name is already taken in this room
        if ($room->players()->where('fake_name', $request->fake_name)->exists()) {
            return back()->withErrors(['fake_name' => 'هذا الاسم المستعار مستخدم بالفعل']);
        }

        // Create the player
        $token = RoomPlayer::generateToken();
        $player = RoomPlayer::create([
            'room_id' => $room->id,
            'name' => $request->name,
            'fake_name' => $request->fake_name,
            'is_host' => false,
            'status' => 'waiting',
            'session_token' => $token,
        ]);

        // Store token in session
        session(['player_token' => $token, 'room_code' => $room->code]);

        // Broadcast player joined
        broadcast(new PlayerUpdated($room, 'joined', $player))->toOthers();

        return redirect()->route('game.lobby', $room->code);
    }

    /**
     * Show the lobby
     */
    public function lobby(Request $request, $code)
    {
        $room = Room::where('code', $code)->first();

        // Room doesn't exist
        if (!$room) {
            return redirect()->route('game.landing')
                ->withErrors(['error' => 'الغرفة غير موجودة']);
        }

        // Allow token-based auth via URL (for testing)
        if ($request->has('token')) {
            $player = $room->players()->where('session_token', $request->token)->first();
            if ($player) {
                session(['player_token' => $request->token, 'room_code' => $room->code]);
            }
        }

        $player = $this->getCurrentPlayer($room);

        // Player not in room - show join form if room is in lobby
        if (!$player) {
            if ($room->isInLobby()) {
                // Show join form directly on this page
                return view('game.join-room', [
                    'room' => $room,
                    'players' => $room->players,
                ]);
            }

            // Game already started or finished
            return redirect()->route('game.landing')
                ->withErrors(['error' => 'لا يمكن الانضمام - اللعبة بدأت أو انتهت']);
        }

        if ($room->isPlaying()) {
            return redirect()->route('game.play', $code);
        }

        if ($room->isFinished()) {
            return redirect()->route('game.results', $code);
        }

        return view('game.lobby', [
            'room' => $room,
            'player' => $player,
            'players' => $room->players,
        ]);
    }

    /**
     * Toggle player ready status
     */
    public function toggleReady(Request $request, $code)
    {
        $room = Room::where('code', $code)->firstOrFail();
        $player = $this->getCurrentPlayer($room);

        if (!$player || !$room->isInLobby()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Cannot toggle ready status'], 400);
            }
            return back();
        }

        $player->update([
            'status' => $player->status === 'ready' ? 'waiting' : 'ready'
        ]);

        // Broadcast player status changed
        broadcast(new PlayerUpdated($room, 'ready_changed', $player))->toOthers();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'status' => $player->status,
            ]);
        }

        return back();
    }

    /**
     * Start the game (host only)
     */
    public function startGame(Request $request, $code)
    {
        $room = Room::where('code', $code)->firstOrFail();
        $player = $this->getCurrentPlayer($room);

        if (!$player || !$player->isHost()) {
            return back()->withErrors(['error' => 'فقط المضيف يمكنه بدء اللعبة']);
        }

        if (!$room->isInLobby()) {
            return back();
        }

        $readyCount = $room->players()->where('status', 'ready')->count();
        $minPlayers = config('game.min_players', 3);
        if ($readyCount < $minPlayers) {
            return back()->withErrors(['error' => "يجب أن يكون {$minPlayers} لاعبين جاهزين على الأقل"]);
        }

        // Get the question bank duration from host (30-180 seconds)
        $duration = $request->input('question_bank_duration', 60);
        $duration = max(30, min(180, (int) $duration)); // Clamp between 30 and 180

        // Activate all ready players
        $room->players()->where('status', 'ready')->update(['status' => 'active']);

        // Remove players who weren't ready
        $room->players()->where('status', 'waiting')->delete();

        // Start the game with question bank phase
        $room->update([
            'status' => 'playing',
            'phase' => 'question_bank',
            'question_bank_started_at' => now(),
            'question_bank_duration' => $duration,
            'current_question_index' => 0, // Will be set to 1 when first question is selected
        ]);

        // Broadcast game started with question bank phase
        broadcast(new GameStateUpdated($room, 'game_started', [
            'redirect_url' => route('game.play', $code),
            'phase' => 'question_bank',
            'timer' => $duration,
        ]));

        return redirect()->route('game.play', $code);
    }

    /**
     * Show the gameplay screen
     */
    public function play($code)
    {
        $room = Room::where('code', $code)->firstOrFail();
        $player = $this->getCurrentPlayer($room);

        if (!$player) {
            return redirect()->route('game.landing');
        }

        if ($room->isInLobby()) {
            return redirect()->route('game.lobby', $code);
        }

        if ($room->isFinished()) {
            return redirect()->route('game.results', $code);
        }

        // Question Bank Phase - all players write questions at the start
        if ($room->isInQuestionBankPhase()) {
            // Get player's questions in the bank
            $playerQuestions = $room->questions()
                ->where('creator_id', $player->id)
                ->where('status', 'pending')
                ->get();

            $maxQuestionsPerPlayer = config('game.max_questions_per_player', 5);
            $remainingTime = $room->getQuestionBankRemainingTime();
            $totalQuestionsInBank = $room->getQuestionBankCount();

            return view('game.play', [
                'room' => $room,
                'player' => $player,
                'isQuestionBankPhase' => true,
                'playerQuestions' => $playerQuestions,
                'playerQuestionsCount' => $playerQuestions->count(),
                'maxQuestionsPerPlayer' => $maxQuestionsPerPlayer,
                'remainingTime' => $remainingTime,
                'totalQuestionsInBank' => $totalQuestionsInBank,
                'totalPlayers' => $room->players()->whereIn('status', ['active', 'revealed'])->count(),
                // Default values for other phases
                'isWritingPhase' => false,
                'hasAnswered' => false,
                'answers' => [],
                'realPlayers' => collect(),
                'totalQuestions' => $room->max_questions,
                'currentQuestionNumber' => 0,
                'isQuestionCreator' => false,
            ]);
        }

        // Get the current question (from question bank, selected for this round)
        $currentQuestion = $room->currentQuestion()->with('creator')->first();

        $answers = [];
        $hasAnswered = false;
        $isQuestionCreator = false;

        if ($currentQuestion) {
            $isQuestionCreator = $currentQuestion->creator_id === $player->id;
            $hasAnswered = $currentQuestion->hasPlayerAnswered($player);

            // Only show answers if in reveal phase or completed
            if ($currentQuestion->isRevealing() || $currentQuestion->isCompleted()) {
                $answers = $currentQuestion->answers()
                    ->with('player')
                    ->get()
                    ->map(function ($answer) {
                        return [
                            'fake_name' => $answer->player->fake_name,
                            'answer' => $answer->answer,
                            'player_id' => $answer->player->id,
                            'is_revealed' => $answer->player->isRevealed(),
                            'is_eliminated' => $answer->player->isEliminated(),
                            'is_correct' => $answer->is_correct,
                        ];
                    });
            }
        }

        // Get list of real names for reveal modal (excluding eliminated and already revealed)
        $realPlayers = $room->players()
            ->where('status', 'active')
            ->get(['id', 'name', 'fake_name']);

        // Calculate the effective total questions (min of max_questions config and actual questions in bank)
        $totalQuestionsInBank = $room->questions()->count();
        $effectiveTotalQuestions = min($room->max_questions, $totalQuestionsInBank);

        // Get answer statistics for waiting display
        $answeredCount = $currentQuestion ? $currentQuestion->getAnsweredCount() : 0;
        $onlinePlayersCount = $currentQuestion ? $currentQuestion->getOnlineAnswerablePlayersCount() : 0;

        return view('game.play', [
            'room' => $room,
            'player' => $player,
            'currentQuestion' => $currentQuestion,
            'isQuestionBankPhase' => false,
            'isWritingPhase' => false,
            'hasAnswered' => $hasAnswered,
            'answers' => $answers,
            'realPlayers' => $realPlayers,
            'totalQuestions' => $effectiveTotalQuestions,
            'currentQuestionNumber' => $room->current_question_index,
            'isQuestionCreator' => $isQuestionCreator,
            'totalPlayers' => $room->players()->whereIn('status', ['active', 'revealed'])->count(),
            'answeredCount' => $answeredCount,
            'onlinePlayersCount' => $onlinePlayersCount,
        ]);
    }

    /**
     * Submit a question to the question bank (during question bank phase)
     */
    public function submitQuestionToBank(Request $request, $code)
    {
        try {
            $rules = [
                'question_text' => 'required|string|max:500',
                'question_type' => 'required|in:text,choice',
            ];

            // Validate based on question type
            if ($request->question_type === 'choice') {
                $rules['choices'] = 'required|array|size:4';
                $rules['choices.*'] = 'required|string|max:200';
                $rules['correct_choice_index'] = 'required|integer|min:0|max:3';
            } else {
                $rules['correct_answer'] = 'required|string|max:200';
            }

            $request->validate($rules);

            $room = Room::where('code', $code)->firstOrFail();
            $player = $this->getCurrentPlayer($room);

            if (!$player) {
                return response()->json(['error' => 'لا يمكنك إرسال السؤال'], 403);
            }

            // Must be in question bank phase
            if (!$room->isInQuestionBankPhase()) {
                return response()->json(['error' => 'انتهت مرحلة كتابة الأسئلة'], 400);
            }

            // Check if player hasn't exceeded max questions
            $maxQuestionsPerPlayer = config('game.max_questions_per_player', 5);
            $playerQuestionsCount = $room->questions()
                ->where('creator_id', $player->id)
                ->where('status', 'pending')
                ->count();

            if ($playerQuestionsCount >= $maxQuestionsPerPlayer) {
                return response()->json(['error' => 'لقد وصلت للحد الأقصى من الأسئلة'], 400);
            }

            // Create the question in the bank
            $questionData = [
                'room_id' => $room->id,
                'creator_id' => $player->id,
                'question_text' => $request->question_text,
                'question_type' => $request->question_type,
                'status' => 'pending',
            ];

            if ($request->question_type === 'choice') {
                $questionData['choices'] = $request->choices;
                $questionData['correct_choice_index'] = $request->correct_choice_index;
            } else {
                $questionData['correct_answer'] = $request->correct_answer;
            }

            $question = RoomQuestion::create($questionData);

            // Broadcast question bank update
            broadcast(new GameStateUpdated($room, 'question_bank_updated', [
                'total_questions' => $room->getQuestionBankCount(),
                'player_questions_count' => $playerQuestionsCount + 1,
            ]))->toOthers();

            return response()->json([
                'success' => true,
                'question_id' => $question->id,
                'player_questions_count' => $playerQuestionsCount + 1,
                'total_questions' => $room->getQuestionBankCount(),
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => $e->validator->errors()->first(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Question bank error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'error' => 'حدث خطأ: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * End question bank phase and start the game (host only or auto-triggered by timer)
     */
    public function endQuestionBankPhase($code)
    {
        $room = Room::where('code', $code)->firstOrFail();
        $player = $this->getCurrentPlayer($room);

        if (!$player) {
            return response()->json(['error' => 'غير مصرح'], 403);
        }

        // Must be in question bank phase
        if (!$room->isInQuestionBankPhase()) {
            return response()->json(['error' => 'اللعبة ليست في مرحلة كتابة الأسئلة'], 400);
        }

        // Only host can end manually, or timer must have expired
        if (!$player->isHost() && !$room->hasQuestionBankTimerExpired()) {
            return response()->json(['error' => 'فقط المضيف يمكنه إنهاء المرحلة'], 403);
        }

        // Check if there are enough questions
        $questionCount = $room->getQuestionBankCount();
        if ($questionCount < 1) {
            return response()->json(['error' => 'يجب إضافة سؤال واحد على الأقل'], 400);
        }

        // Select first random question and start answering phase
        $firstQuestion = $room->getRandomPendingQuestion();

        if (!$firstQuestion) {
            return response()->json(['error' => 'لا توجد أسئلة متاحة'], 400);
        }

        // Update question status and order
        $firstQuestion->update([
            'status' => 'answering',
            'question_order' => 1,
        ]);

        // Update room phase to answering
        $room->update([
            'phase' => 'answering',
            'current_question_index' => 1,
        ]);

        // Broadcast phase change
        broadcast(new GameStateUpdated($room, 'question_bank_ended', [
            'phase' => 'answering',
            'question_text' => $firstQuestion->question_text,
            'question_type' => $firstQuestion->question_type,
            'choices' => $firstQuestion->choices,
            'creator_fake_name' => $firstQuestion->creator ? $firstQuestion->creator->fake_name : null,
            'current_question_number' => 1,
            'total_questions' => min($questionCount, $room->max_questions),
        ]));

        return response()->json([
            'success' => true,
            'phase' => 'answering',
        ]);
    }

    /**
     * Delete a question from the question bank (during question bank phase)
     */
    public function deleteQuestionFromBank(Request $request, $code, $questionId)
    {
        $room = Room::where('code', $code)->firstOrFail();
        $player = $this->getCurrentPlayer($room);

        if (!$player) {
            return response()->json(['error' => 'غير مصرح'], 403);
        }

        // Must be in question bank phase
        if (!$room->isInQuestionBankPhase()) {
            return response()->json(['error' => 'انتهت مرحلة كتابة الأسئلة'], 400);
        }

        // Find the question
        $question = RoomQuestion::find($questionId);

        if (!$question || $question->room_id !== $room->id) {
            return response()->json(['error' => 'السؤال غير موجود'], 404);
        }

        // Only the creator can delete their own question
        if ($question->creator_id !== $player->id) {
            return response()->json(['error' => 'لا يمكنك حذف سؤال لاعب آخر'], 403);
        }

        $question->delete();

        return response()->json([
            'success' => true,
            'player_questions_count' => $room->questions()->where('creator_id', $player->id)->where('status', 'pending')->count(),
            'total_questions' => $room->getQuestionBankCount(),
        ]);
    }

    /**
     * Submit an answer (everyone including creator can answer)
     */
    public function submitAnswer(Request $request, $code)
    {
        $request->validate([
            'answer' => 'required|string|max:500',
        ]);

        $room = Room::where('code', $code)->firstOrFail();
        $player = $this->getCurrentPlayer($room);

        if (!$player || !$player->canAnswer()) {
            return back()->withErrors(['error' => 'لا يمكنك الإجابة']);
        }

        // Get the current question
        $currentQuestion = $room->currentQuestion()->first();

        if (!$currentQuestion || !$currentQuestion->isAnswering()) {
            return back();
        }

        // Check if already answered
        if ($currentQuestion->hasPlayerAnswered($player)) {
            return back();
        }

        // Check if answer is correct using the model method (handles both text and choice types)
        $isCorrect = $currentQuestion->isAnswerCorrect($request->answer);

        // Create the answer
        $answer = RoomAnswer::create([
            'room_question_id' => $currentQuestion->id,
            'room_player_id' => $player->id,
            'answer' => $request->answer,
            'is_correct' => $isCorrect,
        ]);

        // Award point if correct (but not for the question creator - they know the answer)
        if ($isCorrect && $currentQuestion->creator_id !== $player->id) {
            $player->addPoints(1);
        }

        // Check if all players have answered
        if ($currentQuestion->allPlayersAnswered()) {
            // Incentive Shift: Award creator points only if question had mixed results
            // (at least 1 correct AND at least 1 wrong - excluding creator's own answer)
            $otherPlayersAnswers = $currentQuestion->answers()
                ->where('room_player_id', '!=', $currentQuestion->creator_id)
                ->get();

            $correctAnswers = $otherPlayersAnswers->where('is_correct', true)->count();
            $wrongAnswers = $otherPlayersAnswers->where('is_correct', false)->count();

            // Only award points if there's a mix (not all correct, not all wrong)
            // This prevents trivial questions (all correct) and unfair questions (all wrong)
            if ($wrongAnswers > 0 && $correctAnswers > 0 && $currentQuestion->creator) {
                $currentQuestion->creator->addPoints($wrongAnswers);
            }

            $currentQuestion->startRevealing();

            // Update room phase
            $room->update(['phase' => 'revealing']);

            // Broadcast reveal phase started
            $answers = $currentQuestion->answers()
                ->with('player')
                ->get()
                ->map(function ($ans) {
                    return [
                        'fake_name' => $ans->player->fake_name,
                        'answer' => $ans->answer,
                        'player_id' => $ans->player->id,
                        'is_revealed' => $ans->player->isRevealed(),
                        'is_eliminated' => $ans->player->isEliminated(),
                        'is_correct' => $ans->is_correct,
                    ];
                });

            broadcast(new GameStateUpdated($room, 'reveal_phase_started', [
                'phase' => 'revealing',
                'answers' => $answers->toArray(),
                'correct_answer' => $currentQuestion->getCorrectAnswer(),
            ]));
        } else {
            // Broadcast answer submitted (for progress tracking)
            broadcast(new GameStateUpdated($room, 'answer_submitted', [
                'answers_count' => $currentQuestion->answers()->count(),
            ]))->toOthers();
        }

        return back();
    }

    /**
     * Attempt to reveal a player
     */
    public function attemptReveal(Request $request, $code)
    {
        $request->validate([
            'target_id' => 'required|exists:room_players,id',
            'guessed_player_id' => 'required|exists:room_players,id',
        ]);

        $room = Room::where('code', $code)->firstOrFail();
        $player = $this->getCurrentPlayer($room);

        if (!$player || !$player->canReveal()) {
            return back()->withErrors(['error' => 'لا يمكنك الكشف الآن']);
        }

        $currentQuestion = $room->currentQuestion()->first();

        if (!$currentQuestion || !$currentQuestion->isRevealing()) {
            return back()->withErrors(['error' => 'لا يمكنك الكشف الآن']);
        }

        $target = RoomPlayer::findOrFail($request->target_id);
        $guessedPlayer = RoomPlayer::findOrFail($request->guessed_player_id);

        // Can't reveal yourself
        if ($target->id === $player->id) {
            return back()->withErrors(['error' => 'لا يمكنك الكشف عن نفسك']);
        }

        // Can't reveal already revealed players
        if ($target->isRevealed() || $target->isEliminated()) {
            return back()->withErrors(['error' => 'هذا اللاعب مكشوف بالفعل']);
        }

        // Process the reveal
        $reveal = Reveal::attempt($room, $currentQuestion, $player, $target, $guessedPlayer);

        // Clear the reveal lock
        $room->clearRevealLock();

        // Broadcast reveal result
        broadcast(new GameStateUpdated($room, 'reveal_completed', [
            'is_correct' => $reveal->is_correct,
            'guesser_name' => $player->fake_name,
            'target_fake_name' => $target->fake_name,
            'target_real_name' => $target->name,
            'guessed_name' => $guessedPlayer->name,
            'guesser_eliminated' => !$reveal->is_correct,
            'target_revealed' => $reveal->is_correct,
        ]));

        // Check if game should end
        if ($room->shouldEndGame()) {
            $room->update(['status' => 'finished']);

            broadcast(new GameStateUpdated($room, 'game_ended', [
                'redirect_url' => route('game.results', $code),
            ]));

            return redirect()->route('game.results', $code);
        }

        return back()->with('reveal_result', [
            'is_correct' => $reveal->is_correct,
            'target_name' => $target->fake_name,
            'actual_name' => $target->name,
            'guessed_name' => $guessedPlayer->name,
        ]);
    }

    /**
     * Move to next question (host only)
     */
    public function nextQuestion($code)
    {
        $room = Room::where('code', $code)->firstOrFail();
        $player = $this->getCurrentPlayer($room);

        if (!$player || !$player->isHost()) {
            return back();
        }

        $result = $this->moveToNextQuestion($room);

        if ($result === 'finished') {
            return redirect()->route('game.results', $code);
        }

        return back();
    }

    /**
     * Move to the next question (helper method)
     */
    private function moveToNextQuestion(Room $room): string
    {
        // Clear any active reveal lock when moving to next question
        $room->clearRevealLock();

        // Mark current question as completed
        $currentQuestion = $room->currentQuestion()->first();
        if ($currentQuestion) {
            $currentQuestion->complete();
        }

        // Check if game should end
        if ($room->shouldEndGame()) {
            $room->update(['status' => 'finished', 'phase' => null]);
            // Broadcast game ended to all players
            broadcast(new GameStateUpdated($room, 'game_ended', [
                'redirect_url' => route('game.results', $room->code),
            ]));
            return 'finished';
        }

        // Move to next question index
        $nextIndex = $room->current_question_index + 1;

        // Get actual total questions count (min of max_questions and questions in bank)
        $totalQuestionsInBank = $room->questions()->count();
        $effectiveMaxQuestions = min($room->max_questions, $totalQuestionsInBank);

        // Check if we've reached max questions
        if ($nextIndex > $effectiveMaxQuestions) {
            $room->update(['status' => 'finished', 'phase' => null]);
            // Broadcast game ended to all players
            broadcast(new GameStateUpdated($room, 'game_ended', [
                'redirect_url' => route('game.results', $room->code),
            ]));
            return 'finished';
        }

        // Get next question from the question bank
        $nextQuestion = $room->getRandomPendingQuestion();

        if (!$nextQuestion) {
            // No more questions in the bank
            $room->update(['status' => 'finished', 'phase' => null]);
            // Broadcast game ended to all players
            broadcast(new GameStateUpdated($room, 'game_ended', [
                'redirect_url' => route('game.results', $room->code),
            ]));
            return 'finished';
        }

        // Update next question status and order
        $nextQuestion->update([
            'status' => 'answering',
            'question_order' => $nextIndex,
        ]);

        // Update room
        $room->update([
            'current_question_index' => $nextIndex,
            'phase' => 'answering',
        ]);

        // Broadcast next question started
        broadcast(new GameStateUpdated($room, 'next_question', [
            'question_number' => $nextIndex,
            'phase' => 'answering',
            'question_text' => $nextQuestion->question_text,
            'question_type' => $nextQuestion->question_type,
            'choices' => $nextQuestion->choices,
            'creator_fake_name' => $nextQuestion->creator ? $nextQuestion->creator->fake_name : null,
        ]));

        return 'continue';
    }

    /**
     * End the game early (host only)
     */
    public function endGame($code)
    {
        $room = Room::where('code', $code)->firstOrFail();
        $player = $this->getCurrentPlayer($room);

        if (!$player || !$player->isHost()) {
            return back();
        }

        $room->update(['status' => 'finished']);

        // Broadcast game ended
        broadcast(new GameStateUpdated($room, 'game_ended', [
            'redirect_url' => route('game.results', $code),
        ]));

        return redirect()->route('game.results', $code);
    }

    /**
     * Get current game state as JSON (for polling)
     */
    public function getState($code)
    {
        $room = Room::where('code', $code)->first();

        if (!$room) {
            return response()->json(['error' => 'Room not found'], 404);
        }

        // Check for stale reveal locks (older than 15 seconds) and clear them
        if ($room->revealing_player_id && $room->reveal_started_at) {
            $secondsSinceStart = now()->diffInSeconds($room->reveal_started_at);
            if ($secondsSinceStart > 15) {
                $room->clearRevealLock();
                $room->refresh();
            }
        }

        $totalPlayers = $room->players()->whereIn('status', ['active', 'revealed'])->count();

        // Question bank phase
        if ($room->isInQuestionBankPhase()) {
            return response()->json([
                'room_status' => $room->status,
                'phase' => 'question_bank',
                'remaining_time' => $room->getQuestionBankRemainingTime(),
                'total_questions_in_bank' => $room->getQuestionBankCount(),
                'total_players' => $totalPlayers,
                'current_question_index' => 0,
            ]);
        }

        // Get the current question
        $currentQuestion = $room->currentQuestion()->with('creator')->first();

        return response()->json([
            'room_status' => $room->status,
            'current_question_index' => $room->current_question_index,
            'phase' => $room->phase ?? ($currentQuestion ? $currentQuestion->status : null),
            'question_status' => $currentQuestion ? $currentQuestion->status : null,
            'question_text' => $currentQuestion ? $currentQuestion->question_text : null,
            'question_type' => $currentQuestion ? $currentQuestion->question_type : null,
            'choices' => $currentQuestion ? $currentQuestion->choices : null,
            'creator_fake_name' => $currentQuestion && $currentQuestion->creator ? $currentQuestion->creator->fake_name : null,
            'answers_count' => $currentQuestion ? $currentQuestion->answers()->count() : 0,
            'total_players' => $totalPlayers,
            'revealing_player_id' => $room->revealing_player_id ?? null,
            'reveal_started_at' => $room->reveal_started_at ?? null,
        ]);
    }

    /**
     * Start a reveal attempt (locks the reveal phase for this player)
     */
    public function startReveal($code)
    {
        $room = Room::where('code', $code)->firstOrFail();
        $player = $this->getCurrentPlayer($room);

        if (!$player || !$player->canReveal()) {
            return response()->json(['error' => 'لا يمكنك الكشف'], 403);
        }

        // Check if the current question is still in revealing phase
        $currentQuestion = $room->currentQuestion()->first();

        if (!$currentQuestion || !$currentQuestion->isRevealing()) {
            return response()->json(['error' => 'انتهت مرحلة الكشف'], 410);
        }

        // Check for stale reveal locks (older than 15 seconds) and clear them
        if ($room->revealing_player_id && $room->reveal_started_at) {
            $secondsSinceStart = now()->diffInSeconds($room->reveal_started_at);
            if ($secondsSinceStart > 15) {
                $room->clearRevealLock();
                $room->refresh();
            }
        }

        // Check if someone else is already revealing
        if ($room->revealing_player_id && $room->revealing_player_id !== $player->id) {
            return response()->json(['error' => 'لاعب آخر يحاول الكشف'], 409);
        }

        // Lock the reveal for this player
        $room->update([
            'revealing_player_id' => $player->id,
            'reveal_started_at' => now(),
        ]);

        // Broadcast that someone is attempting to reveal
        broadcast(new GameStateUpdated($room, 'reveal_started', [
            'revealing_player_id' => $player->id,
            'revealing_player_name' => $player->fake_name,
        ]))->toOthers();

        return response()->json(['success' => true, 'timeout' => 10]);
    }

    /**
     * Cancel a reveal attempt
     */
    public function cancelReveal($code)
    {
        $room = Room::where('code', $code)->firstOrFail();
        $player = $this->getCurrentPlayer($room);

        if ($player && $room->revealing_player_id === $player->id) {
            $room->update([
                'revealing_player_id' => null,
                'reveal_started_at' => null,
            ]);

            // Broadcast reveal cancelled
            broadcast(new GameStateUpdated($room, 'reveal_cancelled', []))->toOthers();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Handle reveal timeout (player didn't reveal in time - they get revealed)
     */
    public function revealTimeout($code)
    {
        $room = Room::where('code', $code)->firstOrFail();
        $player = $this->getCurrentPlayer($room);

        if (!$player || $room->revealing_player_id !== $player->id) {
            return back();
        }

        // Reveal the player who timed out
        $player->reveal();

        // Clear the reveal lock
        $room->update([
            'revealing_player_id' => null,
            'reveal_started_at' => null,
        ]);

        // Broadcast reveal cancelled so others know
        broadcast(new GameStateUpdated($room, 'reveal_completed', [
            'player_revealed' => $player->fake_name,
        ]));

        // Check if game should end
        if ($room->shouldEndGame()) {
            $room->update(['status' => 'finished', 'phase' => null]);

            // Broadcast game ended to all players
            broadcast(new GameStateUpdated($room, 'game_ended', [
                'redirect_url' => route('game.results', $code),
            ]));

            return redirect()->route('game.results', $code);
        }

        return back()->with('reveal_timeout', true);
    }

    /**
     * Show the results screen
     */
    public function results($code)
    {
        $room = Room::where('code', $code)->firstOrFail();
        $player = $this->getCurrentPlayer($room);

        if (!$player) {
            return redirect()->route('game.landing');
        }

        // Get all players with their correct answers count and successful reveals count
        $allPlayers = $room->players()
            ->withCount([
                'answers as correct_answers_count' => function ($query) {
                    $query->where('is_correct', true);
                },
                'revealAttempts as successful_reveals_count' => function ($query) {
                    $query->where('is_correct', true);
                },
            ])
            ->get();

        // Separate active (hidden) players from revealed players
        $activePlayers = $allPlayers->where('status', 'active')
            ->sortBy([
                ['score', 'desc'],
                ['correct_answers_count', 'desc'],
                ['successful_reveals_count', 'desc'],
                ['created_at', 'asc'],
            ])
            ->values();

        $revealedPlayers = $allPlayers->where('status', 'revealed')
            ->sortBy([
                ['score', 'desc'],
                ['correct_answers_count', 'desc'],
                ['successful_reveals_count', 'desc'],
                ['created_at', 'asc'],
            ])
            ->values();

        // Winner is only from active (non-revealed) players
        $winner = $activePlayers->first();

        // Combine for display: active players first, then revealed
        $players = $activePlayers->concat($revealedPlayers);

        // Get all reveals
        $reveals = $room->reveals()
            ->with(['guesser', 'target', 'guessedPlayer'])
            ->orderBy('created_at')
            ->get();

        return view('game.results', [
            'room' => $room,
            'player' => $player,
            'players' => $players,
            'activePlayers' => $activePlayers,
            'revealedPlayers' => $revealedPlayers,
            'reveals' => $reveals,
            'winner' => $winner,
        ]);
    }

    /**
     * Leave the room
     */
    public function leaveRoom($code)
    {
        $room = Room::where('code', $code)->first();
        $player = $this->getCurrentPlayer($room);

        if ($player && $room) {
            $wasHost = $player->isHost();

            // Delete the player first
            $player->delete();

            // Refresh player count after deletion
            $remainingPlayers = $room->players()->count();

            if ($remainingPlayers === 0) {
                // No players left - delete the room entirely (cascade deletes related records)
                $room->delete();
            } else {
                // If host left, transfer to next player
                if ($wasHost) {
                    $newHost = $room->transferHost();

                    if ($newHost) {
                        // Broadcast host change along with player left
                        broadcast(new PlayerUpdated($room, 'host_changed', $newHost))->toOthers();
                    }
                }

                // Broadcast player left
                broadcast(new PlayerUpdated($room, 'left', null))->toOthers();

                // If game is playing and not enough players remain, end the game
                if ($room->isPlaying()) {
                    $activePlayersCount = $room->activePlayers()->count();
                    if ($activePlayersCount < 2) {
                        $room->update(['status' => 'finished', 'phase' => null]);
                        broadcast(new GameStateUpdated($room, 'game_ended', [
                            'reason' => 'not_enough_players',
                            'redirect_url' => route('game.results', $code),
                        ]));
                    }
                }
            }
        }

        session()->forget(['player_token', 'room_code']);

        return redirect()->route('game.landing');
    }

    /**
     * Get the current player from session
     */
    private function getCurrentPlayer(?Room $room): ?RoomPlayer
    {
        $token = session('player_token');

        if (!$token || !$room) {
            return null;
        }

        return $room->players()->where('session_token', $token)->first();
    }

    /**
     * Load questions for a room from the question bank
     */
    private function loadQuestionsForRoom(Room $room): void
    {
        $questions = [
            // جغرافيا
            ['text' => 'ما هي عاصمة اليابان؟', 'answer' => 'طوكيو'],
            ['text' => 'ما هي عاصمة فرنسا؟', 'answer' => 'باريس'],
            ['text' => 'ما هي عاصمة مصر؟', 'answer' => 'القاهرة'],
            ['text' => 'ما هي عاصمة الجزائر؟', 'answer' => 'الجزائر'],
            ['text' => 'ما هي عاصمة تركيا؟', 'answer' => 'أنقرة'],
            ['text' => 'ما هي أكبر قارة في العالم؟', 'answer' => 'آسيا'],
            ['text' => 'ما هو أكبر محيط في العالم؟', 'answer' => 'المحيط الهادئ'],
            ['text' => 'ما هو أطول نهر في العالم؟', 'answer' => 'نهر النيل'],
            ['text' => 'ما هي أصغر دولة في العالم؟', 'answer' => 'الفاتيكان'],
            ['text' => 'في أي قارة تقع البرازيل؟', 'answer' => 'أمريكا الجنوبية'],

            // علوم
            ['text' => 'كم عدد كواكب المجموعة الشمسية؟', 'answer' => '8'],
            ['text' => 'ما هو العنصر الكيميائي الذي رمزه O؟', 'answer' => 'الأكسجين'],
            ['text' => 'ما هو العنصر الكيميائي الذي رمزه Au؟', 'answer' => 'الذهب'],
            ['text' => 'كم عدد عظام جسم الإنسان البالغ؟', 'answer' => '206'],
            ['text' => 'ما هو أقرب كوكب للشمس؟', 'answer' => 'عطارد'],
            ['text' => 'ما هو أكبر عضو في جسم الإنسان؟', 'answer' => 'الجلد'],
            ['text' => 'كم تبلغ درجة غليان الماء بالدرجة المئوية؟', 'answer' => '100'],

            // تاريخ
            ['text' => 'في أي عام تأسست المملكة العربية السعودية؟', 'answer' => '1932'],
            ['text' => 'في أي عام وقعت الحرب العالمية الأولى؟', 'answer' => '1914'],
            ['text' => 'من هو أول رائد فضاء في التاريخ؟', 'answer' => 'يوري غاغارين'],
            ['text' => 'في أي عام هبط الإنسان على القمر لأول مرة؟', 'answer' => '1969'],

            // رياضة
            ['text' => 'كم عدد لاعبي فريق كرة القدم؟', 'answer' => '11'],
            ['text' => 'في أي عام فازت السعودية على الأرجنتين في كأس العالم؟', 'answer' => '2022'],
            ['text' => 'ما هي الدولة الأكثر فوزاً بكأس العالم لكرة القدم؟', 'answer' => 'البرازيل'],
            ['text' => 'كم شوطاً في مباراة كرة السلة؟', 'answer' => '4'],

            // عامة
            ['text' => 'كم عدد أيام السنة الكبيسة؟', 'answer' => '366'],
            ['text' => 'كم عدد ألوان قوس قزح؟', 'answer' => '7'],
            ['text' => 'ما هي اللغة الرسمية في البرازيل؟', 'answer' => 'البرتغالية'],
            ['text' => 'كم عدد أركان الإسلام؟', 'answer' => '5'],
            ['text' => 'ما هو الحيوان الأسرع في العالم؟', 'answer' => 'الفهد'],
            ['text' => 'كم عدد أحرف اللغة العربية؟', 'answer' => '28'],
            ['text' => 'ما هي أكبر دولة عربية من حيث المساحة؟', 'answer' => 'الجزائر'],
            ['text' => 'كم عدد أيام الأسبوع؟', 'answer' => '7'],
            ['text' => 'ما هو لون دم الأخطبوط؟', 'answer' => 'أزرق'],
            ['text' => 'كم ساعة في اليوم؟', 'answer' => '24'],

            // ثقافة عربية
            ['text' => 'من هو مؤلف كتاب "مقدمة ابن خلدون"؟', 'answer' => 'ابن خلدون'],
            ['text' => 'ما هي أطول سورة في القرآن الكريم؟', 'answer' => 'البقرة'],
            ['text' => 'كم عدد السور في القرآن الكريم؟', 'answer' => '114'],
            ['text' => 'ما هي أقصر سورة في القرآن الكريم؟', 'answer' => 'الكوثر'],
        ];

        // Shuffle and take max_questions
        shuffle($questions);
        $selectedQuestions = array_slice($questions, 0, $room->max_questions);

        foreach ($selectedQuestions as $index => $question) {
            RoomQuestion::create([
                'room_id' => $room->id,
                'question_text' => $question['text'],
                'correct_answer' => $question['answer'],
                'question_order' => $index + 1,
                'status' => 'pending',
            ]);
        }
    }

    /**
     * Heartbeat endpoint - players ping this to stay "online"
     */
    public function heartbeat($code)
    {
        $room = Room::where('code', $code)->first();
        $player = $this->getCurrentPlayer($room);

        if (!$player) {
            return response()->json(['success' => false], 401);
        }

        $player->updateLastSeen();

        $response = [
            'success' => true,
        ];

        // Check if we need to advance the game due to offline players
        if ($room->isPlaying()) {
            $currentQuestion = $room->currentQuestion()->first();

            if ($currentQuestion && $currentQuestion->isAnswering()) {
                // Check if all online players have answered
                if ($currentQuestion->allPlayersAnswered()) {
                    // Move to reveal phase
                    $this->transitionToRevealPhase($room, $currentQuestion);
                    $response['phase_changed'] = true;
                    $response['new_phase'] = 'revealing';
                }
            }
        }

        // Return list of players with their online status
        $players = $room->fresh()->players->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'is_online' => $p->isOnline(),
                'is_host' => $p->is_host,
            ];
        });

        $response['players'] = $players;

        return response()->json($response);
    }

    /**
     * Transition from answering to reveal phase
     */
    private function transitionToRevealPhase(Room $room, $currentQuestion): void
    {
        // Incentive Shift: Award creator points only if question had mixed results
        $otherPlayersAnswers = $currentQuestion->answers()
            ->where('room_player_id', '!=', $currentQuestion->creator_id)
            ->get();

        if ($otherPlayersAnswers->count() > 0) {
            $hasCorrect = $otherPlayersAnswers->where('is_correct', true)->count() > 0;
            $hasWrong = $otherPlayersAnswers->where('is_correct', false)->count() > 0;

            if ($hasCorrect && $hasWrong && $currentQuestion->creator_id) {
                $wrongCount = $otherPlayersAnswers->where('is_correct', false)->count();
                $currentQuestion->creator->addPoints($wrongCount);
            }
        }

        // Move to reveal phase
        $currentQuestion->startRevealing();
        $room->update(['phase' => 'revealing']);

        // Broadcast phase change
        broadcast(new GameStateUpdated($room, 'phase_changed', [
            'phase' => 'revealing',
            'question_status' => 'revealing',
        ]));
    }

    /**
     * Kick a player from the room (host only)
     */
    public function kickPlayer($code, $playerId)
    {
        $room = Room::where('code', $code)->first();
        $currentPlayer = $this->getCurrentPlayer($room);

        if (!$currentPlayer || !$currentPlayer->isHost()) {
            return response()->json(['success' => false, 'message' => 'غير مصرح'], 403);
        }

        $playerToKick = $room->players()->find($playerId);

        if (!$playerToKick) {
            return response()->json(['success' => false, 'message' => 'اللاعب غير موجود'], 404);
        }

        if ($playerToKick->isHost()) {
            return response()->json(['success' => false, 'message' => 'لا يمكن طرد المضيف'], 400);
        }

        // Delete the player
        $playerToKick->delete();

        // Broadcast player kicked
        broadcast(new PlayerUpdated($room, 'kicked', null));

        // If game is playing and not enough players remain, end the game
        if ($room->isPlaying()) {
            $activePlayersCount = $room->activePlayers()->count();
            if ($activePlayersCount < 2) {
                $room->update(['status' => 'finished', 'phase' => null]);
                broadcast(new GameStateUpdated($room, 'game_ended', [
                    'reason' => 'not_enough_players',
                    'redirect_url' => route('game.results', $code),
                ]));
            }
        }

        return response()->json(['success' => true]);
    }
}
