@extends('layouts.game')

@section('title', 'اللعب')

@section('content')
    <div class="container py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 animate-fade-in">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ asset('imgs/logo-nightsky.svg') }}" alt="ملثم" style="width: 48px; height: 48px;">
                <div>
                    <h5 class="mb-0 fw-bold">ملثم</h5>
                    <small class="text-muted-custom">غرفة {{ $room->code }}</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="text-end">
                    <small class="text-muted-custom d-block">أنت</small>
                    <span class="fw-bold text-primary-custom">{{ $player->fake_name }}</span>
                </div>
                @if($player->is_host)
                    <form action="{{ route('game.end', $room->code) }}" method="POST"
                        onsubmit="return confirm('هل أنت متأكد من إنهاء اللعبة؟')">
                        @csrf
                        <button type="submit" class="btn-game btn-game-outline btn-game-sm">
                            <i class="bi bi-stop-circle"></i>
                            إنهاء
                        </button>
                    </form>
                @endif
            </div>
        </div>

        @if(!$isQuestionBankPhase)
            <!-- Progress Bar (only show after question bank phase) -->
            <div class="mb-4 animate-fade-in">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted-custom">تقدم اللعبة</span>
                    <span class="fw-bold">السؤال {{ $currentQuestionNumber }} من {{ $totalQuestions }}</span>
                </div>
                <div class="game-progress">
                    <div class="game-progress-bar"
                        style="width: {{ $totalQuestions > 0 ? ($currentQuestionNumber / $totalQuestions) * 100 : 0 }}%"></div>
                </div>
            </div>
        @endif

        <!-- Flash Messages -->
        @if(session('reveal_result'))
            @php $result = session('reveal_result'); @endphp
            <div
                class="game-alert {{ $result['is_correct'] ? 'game-alert-success' : 'game-alert-danger' }} mb-4 animate-fade-in">
                @if($result['is_correct'])
                    <i class="bi bi-check-circle-fill fs-4"></i>
                    <div>
                        <strong>كشف صحيح!</strong><br>
                        <span>{{ $result['target_name'] }} هو {{ $result['actual_name'] }} - تم نقل النقاط إليك</span>
                    </div>
                @else
                    <i class="bi bi-x-circle-fill fs-4"></i>
                    <div>
                        <strong>كشف خاطئ!</strong><br>
                        <span>{{ $result['target_name'] }} ليس {{ $result['guessed_name'] }} - تم نقل نقاطك وأنت الآن خارج
                            اللعبة</span>
                    </div>
                @endif
            </div>
        @endif

        @if ($errors->any())
            <div class="game-alert game-alert-danger mb-4 animate-shake">
                <i class="bi bi-exclamation-circle fs-5"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        @if($isQuestionBankPhase)
            <!-- Question Bank Phase - All Players Write Questions at Start -->
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="game-card animate-fade-in">
                        <div class="game-card-body">
                            <div class="text-center mb-4">
                                <i class="bi bi-journal-plus text-primary-custom fs-1 d-block mb-3"></i>
                                <h4 class="fw-bold mb-2">بنك الأسئلة</h4>
                                <p class="text-muted-custom">اكتب أكبر عدد من الأسئلة قبل انتهاء الوقت!</p>
                            </div>

                            <!-- Timer -->
                            <div class="text-center mb-4">
                                <div class="question-bank-timer mx-auto" id="questionBankTimer">
                                    <span id="timerSeconds">{{ $remainingTime }}</span>
                                </div>
                                <small class="text-muted-custom">ثانية متبقية</small>
                            </div>

                            <!-- Stats -->
                            <div class="row g-3 mb-4">
                                <div class="col-6 col-md-3">
                                    <div class="stat-card text-center">
                                        <div class="stat-value" id="playerQuestionsCount">{{ $playerQuestionsCount }}</div>
                                        <div class="stat-label">أسئلتك</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="stat-card text-center">
                                        <div class="stat-value">{{ $maxQuestionsPerPlayer }}</div>
                                        <div class="stat-label">الحد الأقصى</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="stat-card text-center">
                                        <div class="stat-value" id="totalQuestionsInBank">{{ $totalQuestionsInBank }}</div>
                                        <div class="stat-label">إجمالي الأسئلة</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="stat-card text-center">
                                        <div class="stat-value">{{ $totalPlayers }}</div>
                                        <div class="stat-label">اللاعبين</div>
                                    </div>
                                </div>
                            </div>

                            @if($playerQuestionsCount < $maxQuestionsPerPlayer)
                                <!-- Add Question Form -->
                                <div class="question-form-container mb-4">
                                    <h5 class="fw-bold mb-3">
                                        <i class="bi bi-plus-circle me-2"></i>
                                        إضافة سؤال جديد
                                    </h5>

                                    <form id="addQuestionForm">
                                        <!-- Question Type Selection -->
                                        <div class="mb-3">
                                            <label class="game-label">نوع السؤال</label>
                                            <div class="question-type-selector d-flex gap-2">
                                                <label class="question-type-option flex-fill">
                                                    <input type="radio" name="question_type" value="text" checked class="d-none">
                                                    <div class="question-type-card text-center py-3 px-3">
                                                        <i class="bi bi-fonts fs-4 d-block mb-1"></i>
                                                        <span>إجابة نصية</span>
                                                    </div>
                                                </label>
                                                <label class="question-type-option flex-fill">
                                                    <input type="radio" name="question_type" value="choice" class="d-none">
                                                    <div class="question-type-card text-center py-3 px-3">
                                                        <i class="bi bi-list-check fs-4 d-block mb-1"></i>
                                                        <span>اختيار من متعدد</span>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Question Text -->
                                        <div class="mb-3">
                                            <label class="game-label">
                                                <i class="bi bi-question-circle me-1"></i>
                                                نص السؤال
                                            </label>
                                            <input type="text" name="question_text" class="game-input"
                                                placeholder="مثال: ما هي عاصمة فرنسا؟" required maxlength="500"
                                                id="questionTextInput">
                                        </div>

                                        <!-- Text Answer (for text type) -->
                                        <div id="textAnswerSection">
                                            <div class="mb-3">
                                                <label class="game-label">
                                                    <i class="bi bi-check-circle me-1"></i>
                                                    الإجابة الصحيحة
                                                </label>
                                                <input type="text" name="correct_answer" class="game-input"
                                                    placeholder="مثال: باريس" maxlength="200" id="correctAnswerInput">
                                            </div>
                                        </div>

                                        <!-- Multiple Choice Options (for choice type) -->
                                        <div id="choicesSection" style="display: none;">
                                            <label class="game-label">
                                                <i class="bi bi-list-ul me-1"></i>
                                                الخيارات (اختر الإجابة الصحيحة)
                                            </label>
                                            <div class="choices-container">
                                                @for($i = 0; $i < 4; $i++)
                                                    <div class="choice-input-group mb-2">
                                                        <label class="choice-option d-flex align-items-center gap-2 w-100">
                                                            <input type="radio" name="correct_choice_index" value="{{ $i }}"
                                                                class="choice-radio" {{ $i === 0 ? 'checked' : '' }}>
                                                            <input type="text" name="choices[]" class="game-input flex-fill mb-0"
                                                                placeholder="الخيار {{ $i + 1 }}" maxlength="200">
                                                            <span class="correct-indicator">
                                                                <i class="bi bi-check-circle-fill text-success"></i>
                                                            </span>
                                                        </label>
                                                    </div>
                                                @endfor
                                            </div>
                                            <small class="text-muted-custom">
                                                <i class="bi bi-info-circle me-1"></i>
                                                اضغط على الدائرة لتحديد الإجابة الصحيحة
                                            </small>
                                        </div>

                                        <button type="submit" class="btn-game btn-game-primary w-100 btn-game-lg mt-3"
                                            id="addQuestionBtn">
                                            <i class="bi bi-plus-lg"></i>
                                            إضافة السؤال
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="game-alert game-alert-success mb-4">
                                    <i class="bi bi-check-circle-fill fs-4"></i>
                                    <div>
                                        <strong>وصلت للحد الأقصى!</strong><br>
                                        <span>لقد أضفت {{ $maxQuestionsPerPlayer }} أسئلة. انتظر انتهاء الوقت.</span>
                                    </div>
                                </div>
                            @endif

                            <!-- Player's Questions List -->
                            @if($playerQuestions->count() > 0)
                                <div class="my-questions-section">
                                    <h5 class="fw-bold mb-3">
                                        <i class="bi bi-journal-text me-2"></i>
                                        أسئلتك
                                    </h5>
                                    <div class="questions-list" id="playerQuestionsList">
                                        @foreach($playerQuestions as $q)
                                            <div class="question-item d-flex align-items-center justify-content-between p-3 mb-2"
                                                data-question-id="{{ $q->id }}">
                                                <div class="flex-fill">
                                                    <div class="fw-bold">{{ $q->question_text }}</div>
                                                    <small class="text-muted-custom">
                                                        @if($q->question_type === 'choice')
                                                            <i class="bi bi-list-check me-1"></i>
                                                            اختيار من متعدد
                                                        @else
                                                            <i class="bi bi-fonts me-1"></i>
                                                            إجابة نصية: {{ $q->correct_answer }}
                                                        @endif
                                                    </small>
                                                </div>
                                                <button type="button" class="btn-game btn-game-outline btn-game-sm delete-question-btn"
                                                    data-question-id="{{ $q->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Host: End Question Bank Phase Button -->
                            @if($player->is_host)
                                <div class="text-center mt-4 pt-4 border-top">
                                    <button type="button" class="btn-game btn-game-success btn-game-lg" id="endQuestionBankBtn" {{ $totalQuestionsInBank < 1 ? 'disabled' : '' }}>
                                        <i class="bi bi-play-circle"></i>
                                        بدء اللعبة الآن
                                    </button>
                                    <small class="text-muted-custom d-block mt-2">
                                        @if($totalQuestionsInBank < 1)
                                            يجب إضافة سؤال واحد على الأقل
                                        @else
                                            {{ $totalQuestionsInBank }} سؤال جاهز
                                        @endif
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        @elseif($currentQuestion)
            <!-- Question Display (for answering/revealing phases) -->
            <div class="question-display mb-4 animate-fade-in" style="animation-delay: 0.1s">
                <div class="question-number">
                    <i class="bi bi-question-circle me-1"></i>
                    السؤال {{ $currentQuestionNumber }}
                    @if($currentQuestion->creator)
                        <span class="badge bg-secondary ms-2" style="font-size: 0.7rem;">
                            <i class="bi bi-person-fill me-1"></i>
                            {{ $currentQuestion->creator->fake_name }}
                        </span>
                    @endif
                </div>
                <div class="question-text">
                    {{ $currentQuestion->question_text }}
                </div>
            </div>

            <!-- Player Status -->
            @if($player->isEliminated())
                <div class="game-alert game-alert-danger mb-4">
                    <i class="bi bi-emoji-frown fs-4"></i>
                    <div>
                        <strong>للأسف، أنت خارج اللعبة!</strong><br>
                        <span>يمكنك مشاهدة بقية اللعبة</span>
                    </div>
                </div>
            @elseif($player->isRevealed())
                <div class="game-alert game-alert-info mb-4">
                    <i class="bi bi-eye fs-4"></i>
                    <div>
                        <strong>تم كشف هويتك!</strong><br>
                        <span>يمكنك الاستمرار بالإجابة للتشويش على الآخرين</span>
                    </div>
                </div>
            @elseif($isQuestionCreator)
                <div class="game-alert game-alert-success mb-4">
                    <i class="bi bi-star-fill fs-4"></i>
                    <div>
                        <strong>هذا سؤالك!</strong><br>
                        <span>ستحصل على نقطة لكل إجابة خاطئة (باستثناء إجابتك)</span>
                    </div>
                </div>
            @endif

            <!-- Answering Phase -->
            @if($currentQuestion->isAnswering())
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="game-card animate-fade-in" style="animation-delay: 0.2s">
                            <div class="game-card-body">
                                @if(!$hasAnswered && $player->canAnswer())
                                    <form action="{{ route('game.answer', $room->code) }}" method="POST">
                                        @csrf
                                        @if($currentQuestion->isMultipleChoice() && $currentQuestion->choices)
                                            <!-- Multiple Choice Answer -->
                                            <div class="mb-4">
                                                <label class="game-label">
                                                    <i class="bi bi-list-check me-1"></i>
                                                    اختر الإجابة الصحيحة
                                                </label>
                                                <div class="choices-answer-container">
                                                    @foreach($currentQuestion->choices as $index => $choice)
                                                        <label class="choice-answer-option d-block mb-2">
                                                            <input type="radio" name="answer" value="{{ $choice }}" class="d-none" required>
                                                            <div class="choice-answer-card p-3 d-flex align-items-center gap-3">
                                                                <span class="choice-letter">{{ ['أ', 'ب', 'ج', 'د'][$index] }}</span>
                                                                <span class="choice-text">{{ $choice }}</span>
                                                                <i class="bi bi-check-circle-fill text-success ms-auto check-icon"></i>
                                                            </div>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @else
                                            <!-- Text Answer -->
                                            <div class="mb-4">
                                                <label class="game-label">
                                                    <i class="bi bi-pencil-fill me-1"></i>
                                                    إجابتك
                                                </label>
                                                <input type="text" name="answer" class="game-input" placeholder="اكتب إجابتك هنا..."
                                                    required autofocus maxlength="500">
                                            </div>
                                        @endif
                                        <small class="text-muted-custom d-block mb-3">
                                            <i class="bi bi-shield-fill me-1"></i>
                                            إجابتك ستظهر باسمك المستعار: <strong>{{ $player->fake_name }}</strong>
                                        </small>
                                        <button type="submit" class="btn-game btn-game-primary w-100 btn-game-lg">
                                            <i class="bi bi-send"></i>
                                            إرسال الإجابة
                                        </button>
                                    </form>
                                @elseif($hasAnswered)
                                    <div class="text-center py-4">
                                        <i class="bi bi-check-circle-fill text-success-custom fs-1 mb-3 d-block animate-pulse"></i>
                                        <h5 class="fw-bold mb-2">تم إرسال إجابتك</h5>
                                        <p class="text-muted-custom mb-2">في انتظار بقية اللاعبين...</p>
                                        <small class="text-muted-custom">
                                            <i class="bi bi-people-fill me-1"></i>
                                            <span id="answeredCount">{{ $answeredCount }}</span> / <span
                                                id="onlinePlayersCount">{{ $onlinePlayersCount }}</span> أجابوا
                                        </small>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="bi bi-hourglass-split text-muted-custom fs-1 mb-3 d-block"></i>
                                        <p class="text-muted-custom mb-0">أنت مشاهد في هذه الجولة</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Reveal Phase -->
            @if($currentQuestion->isRevealing())
                <div class="animate-fade-in" style="animation-delay: 0.2s">
                    <!-- Active Reveal Notice -->
                    <div id="activeRevealNotice" class="game-alert game-alert-warning mb-4" style="display: none;">
                        <i class="bi bi-exclamation-triangle fs-4"></i>
                        <div>
                            <strong>أحدهم يحاول الكشف...</strong>
                            <span class="badge bg-danger ms-2" id="revealTimerBadge">10</span>
                        </div>
                    </div>

                    <div class="text-center mb-4">
                        <h4 class="fw-bold text-primary-custom">
                            <i class="bi bi-eye me-2"></i>
                            مرحلة الكشف!
                        </h4>
                        <p class="text-muted-custom">
                            @if($player->canReveal())
                                اضغط على "كشف" لمحاولة كشف هوية أحد اللاعبين
                            @else
                                شاهد اللاعبين وهم يحاولون الكشف
                            @endif
                        </p>
                    </div>

                    <!-- Answers Grid -->
                    <div class="row g-3 mb-4" id="answersGrid">
                        @foreach($answers as $answer)
                            <div class="col-md-4 col-sm-6">
                                <div
                                    class="answer-card {{ $answer['is_revealed'] ? 'border-info' : '' }} {{ $answer['is_eliminated'] ? 'opacity-50' : '' }} {{ $answer['is_correct'] ? 'answer-correct' : 'answer-wrong' }}">
                                    <div class="fake-name">
                                        @if($answer['is_correct'])
                                            <i class="bi bi-check-circle-fill text-success me-1"></i>
                                        @else
                                            <i class="bi bi-x-circle-fill text-danger me-1"></i>
                                        @endif
                                        @if($answer['is_revealed'])
                                            <i class="bi bi-eye-fill text-info me-1"></i>
                                        @elseif($answer['is_eliminated'])
                                            <i class="bi bi-x-circle-fill text-danger me-1"></i>
                                        @endif
                                        {{ $answer['fake_name'] }}
                                    </div>
                                    <div class="answer-text">{{ $answer['answer'] }}</div>

                                    @if($player->canReveal() && !$answer['is_revealed'] && !$answer['is_eliminated'] && $answer['player_id'] !== $player->id)
                                        <button type="button" class="btn-game btn-game-danger btn-game-sm w-100 reveal-btn"
                                            data-target-id="{{ $answer['player_id'] }}" data-target-name="{{ $answer['fake_name'] }}">
                                            <i class="bi bi-search"></i>
                                            كشف
                                        </button>
                                    @elseif($answer['is_revealed'])
                                        <span class="status-badge status-badge-revealed w-100 d-block text-center">
                                            <i class="bi bi-eye"></i>
                                            مكشوف
                                        </span>
                                    @elseif($answer['is_eliminated'])
                                        <span class="status-badge status-badge-eliminated w-100 d-block text-center">
                                            <i class="bi bi-x-circle"></i>
                                            خارج اللعبة
                                        </span>
                                    @elseif($answer['player_id'] === $player->id)
                                        <span class="status-badge status-badge-host w-100 d-block text-center">
                                            <i class="bi bi-person"></i>
                                            إجابتك
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Correct Answer Display -->
                    @if($currentQuestion)
                        <div class="game-alert game-alert-info mb-4">
                            <i class="bi bi-lightbulb fs-4"></i>
                            <div>
                                <strong>الإجابة الصحيحة:</strong> {{ $currentQuestion->getCorrectAnswer() }}
                            </div>
                        </div>
                    @endif

                    <!-- Countdown Timer & Next Question -->
                    <div class="text-center" id="nextQuestionSection">
                        <div class="game-card d-inline-block px-5 py-4 mb-3">
                            <div class="text-muted-custom mb-2">السؤال التالي في</div>
                            <div class="fs-1 fw-bold text-primary-custom" id="countdown">5</div>
                            <div class="text-muted-custom">ثواني</div>
                        </div>

                        @if($player->is_host)
                            <div>
                                <form action="{{ route('game.next', $room->code) }}" method="POST" id="nextQuestionForm">
                                    @csrf
                                    <button type="submit" class="btn-game btn-game-primary btn-game-lg">
                                        <i class="bi bi-arrow-left"></i>
                                        التالي الآن
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="bi bi-hourglass-split fs-1 text-muted-custom d-block mb-3"></i>
                <p class="text-muted-custom">جاري تحميل السؤال...</p>
            </div>
        @endif
    </div>

    <!-- Custom Modal Backdrop -->
    <div id="customModalBackdrop" class="custom-modal-backdrop" style="display: none;"></div>

    <!-- Reveal Modal -->
    <div class="modal fade" id="revealModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-search me-2"></i>
                        من هو "<span id="targetFakeName"></span>" برأيك؟
                    </h5>
                    <div class="reveal-modal-timer ms-auto me-3">
                        <span class="badge bg-danger fs-5" id="modalTimer">{{ config('game.reveal_timer', 10) }}</span>
                    </div>
                </div>
                <form action="{{ route('game.reveal', $room->code) }}" method="POST" id="revealForm">
                    @csrf
                    <input type="hidden" name="target_id" id="targetIdInput">
                    <div class="modal-body">
                        <div class="game-alert game-alert-danger mb-3">
                            <i class="bi bi-stopwatch fs-4"></i>
                            <div>
                                <strong>لديك {{ config('game.reveal_timer', 10) }} ثواني!</strong><br>
                                <span>إذا لم تختر، سيتم كشف هويتك!</span>
                            </div>
                        </div>

                        <p class="text-muted-custom mb-3">
                            <i class="bi bi-exclamation-triangle text-warning me-1"></i>
                            <strong>تحذير:</strong> إذا كان تخمينك خاطئاً، ستفقد نقاطك وتخرج من اللعبة!
                        </p>

                        <div class="d-flex flex-column gap-2" id="playersList">
                            @foreach($realPlayers as $rp)
                                @if($rp->id !== $player->id)
                                    <button type="button"
                                        class="player-card player-select-card d-flex align-items-center gap-3 w-100 text-start"
                                        data-player-id="{{ $rp->id }}">
                                        <div class="player-avatar" style="width: 40px; height: 40px; font-size: 1rem;">
                                            {{ mb_substr($rp->name, 0, 1) }}
                                        </div>
                                        <span class="fw-bold">{{ $rp->name }}</span>
                                        <i class="bi bi-check-circle-fill text-success ms-auto check-icon"
                                            style="display: none;"></i>
                                    </button>
                                @endif
                            @endforeach
                            <input type="hidden" name="guessed_player_id" id="guessedPlayerInput" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn-game btn-game-danger w-100 btn-game-lg">
                            <i class="bi bi-search"></i>
                            كشف!
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reveal Timeout Form (hidden) -->
    <form action="{{ route('game.revealTimeout', $room->code) }}" method="POST" id="revealTimeoutForm"
        style="display: none;">
        @csrf
    </form>

@endsection

@push('styles')
    <style>
        button.player-select-card {
            cursor: pointer !important;
            border: 1px solid var(--color-border);
            background: var(--color-card);
            color: var(--color-text);
            padding: 16px;
            border-radius: var(--radius-md);
            transition: all 0.2s ease;
            outline: none;
        }

        button.player-select-card:hover {
            border-color: var(--color-primary) !important;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        button.player-select-card:focus {
            border-color: var(--color-primary) !important;
            box-shadow: 0 0 0 3px rgba(201, 162, 39, 0.3);
        }

        button.player-select-card.selected {
            border-color: var(--color-success) !important;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(52, 211, 153, 0.2) 100%) !important;
        }

        button.player-select-card.selected .check-icon {
            display: block !important;
        }

        /* Custom Modal Backdrop */
        .custom-modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(6, 8, 16, 0.85);
            z-index: 1040;
        }

        /* Ensure modal is above backdrop */
        #revealModal {
            z-index: 1050;
        }

        #revealModal .modal-dialog {
            z-index: 1051;
        }

        /* Question Bank Timer */
        .question-bank-timer {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-hover) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 25px rgba(201, 162, 39, 0.3), 0 0 40px rgba(201, 162, 39, 0.2);
        }

        .question-bank-timer span {
            font-size: 2.5rem;
            font-weight: bold;
            color: white;
        }

        .question-bank-timer.warning {
            background: linear-gradient(135deg, #e67e22 0%, #d35400 100%);
            animation: pulse-warning 1s ease-in-out infinite;
        }

        .question-bank-timer.danger {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            animation: pulse-danger 0.5s ease-in-out infinite;
        }

        @keyframes pulse-warning {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        @keyframes pulse-danger {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        /* Stat Cards */
        .stat-card {
            background: var(--color-card);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            padding: 1rem;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: bold;
            color: var(--color-primary);
        }

        .stat-label {
            font-size: 0.8rem;
            color: var(--color-muted);
        }

        /* Question Type Selector */
        .question-type-option input:checked+.question-type-card {
            border-color: var(--color-primary);
            background: linear-gradient(135deg, rgba(201, 162, 39, 0.15) 0%, rgba(201, 162, 39, 0.2) 100%);
        }

        .question-type-card {
            border: 2px solid var(--color-border);
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .question-type-card:hover {
            border-color: var(--color-primary);
        }

        /* Choice Input Group */
        .choice-option .correct-indicator {
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .choice-option:has(.choice-radio:checked) .correct-indicator {
            opacity: 1;
        }

        .choice-radio {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        /* Question Item */
        .question-item {
            background: var(--color-card);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
        }

        /* Choice Answer Cards */
        .choice-answer-option input:checked+.choice-answer-card {
            border-color: var(--color-primary);
            background: linear-gradient(135deg, rgba(201, 162, 39, 0.15) 0%, rgba(201, 162, 39, 0.2) 100%);
        }

        .choice-answer-option input:checked+.choice-answer-card .check-icon {
            display: block !important;
        }

        .choice-answer-card {
            border: 2px solid var(--color-border);
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .choice-answer-card:hover {
            border-color: var(--color-primary);
        }

        .choice-answer-card .check-icon {
            display: none;
        }

        .choice-letter {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--color-primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        /* Answer Card Correct/Wrong States */
        .answer-card.answer-correct {
            border-color: var(--color-success) !important;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(52, 211, 153, 0.15) 100%) !important;
        }

        .answer-card.answer-wrong {
            border-color: var(--color-danger) !important;
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(248, 113, 113, 0.15) 100%) !important;
        }

        /* Question Form Container */
        .question-form-container {
            background: rgba(99, 102, 241, 0.05);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            border: 1px dashed var(--color-border);
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Game state
        const roomCode = '{{ $room->code }}';
        const playerId = {{ $player->id }};
        const isHost = {{ $player->is_host ? 'true' : 'false' }};
        const totalPlayers = {{ $totalPlayers }};
        const isQuestionBankPhase = {{ $isQuestionBankPhase ? 'true' : 'false' }};
        const maxQuestionsPerPlayer = {{ $maxQuestionsPerPlayer ?? 5 }};

        let currentState = {
            questionIndex: {{ $currentQuestionNumber ?? 0 }},
            phase: '{{ $isQuestionBankPhase ? "question_bank" : ($currentQuestion ? $currentQuestion->status : "waiting") }}',
            playerQuestionsCount: {{ $playerQuestionsCount ?? 0 }},
            revealingPlayerId: {{ $room->revealing_player_id ?? 'null' }}
        };
        let isTyping = false;
        let revealTimerInterval = null;
        let countdownInterval = null;
        let isInRevealMode = false;

        // Heartbeat - ping server every 10 seconds to stay "online"
        async function sendHeartbeat() {
            try {
                const response = await fetch('/room/{{ $room->code }}/heartbeat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();

                // If phase changed due to offline players, reload the page
                if (data.phase_changed) {
                    console.log('Phase changed to:', data.new_phase);
                    window.location.reload();
                }
            } catch (error) {
                console.log('Heartbeat error:', error);
            }
        }

        // Start heartbeat
        sendHeartbeat();
        setInterval(sendHeartbeat, 10000);

        // Clean up any stale modal backdrops on page load
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

        // Track typing in inputs
        document.querySelectorAll('input[type="text"]').forEach(input => {
            input.addEventListener('focus', () => isTyping = true);
            input.addEventListener('blur', () => isTyping = false);
            input.addEventListener('input', () => isTyping = true);
        });

        @if($isQuestionBankPhase)
            // Question Bank Phase Logic
            let questionBankCountdown = {{ $remainingTime }};
            const qbTimerEl = document.getElementById('timerSeconds');
            const qbTimerContainer = document.getElementById('questionBankTimer');

            const questionBankInterval = setInterval(() => {
                questionBankCountdown--;
                if (qbTimerEl) qbTimerEl.textContent = questionBankCountdown;

                if (questionBankCountdown <= 10 && questionBankCountdown > 5) {
                    qbTimerContainer.classList.add('warning');
                    qbTimerContainer.classList.remove('danger');
                } else if (questionBankCountdown <= 5) {
                    qbTimerContainer.classList.remove('warning');
                    qbTimerContainer.classList.add('danger');
                }

                if (questionBankCountdown <= 0) {
                    clearInterval(questionBankInterval);
                    // Auto-end question bank phase
                    endQuestionBankPhase();
                }
            }, 1000);

            // Question type toggle
            document.querySelectorAll('input[name="question_type"]').forEach(radio => {
                radio.addEventListener('change', function () {
                    const textSection = document.getElementById('textAnswerSection');
                    const choicesSection = document.getElementById('choicesSection');

                    if (this.value === 'choice') {
                        textSection.style.display = 'none';
                        choicesSection.style.display = 'block';
                    } else {
                        textSection.style.display = 'block';
                        choicesSection.style.display = 'none';
                    }
                });
            });

            // Add question form submission
            document.getElementById('addQuestionForm')?.addEventListener('submit', async function (e) {
                e.preventDefault();

                const form = this;
                const btn = document.getElementById('addQuestionBtn');
                const questionType = form.querySelector('input[name="question_type"]:checked').value;
                const questionText = form.querySelector('input[name="question_text"]').value;

                if (!questionText.trim()) {
                    alert('يرجى إدخال نص السؤال');
                    return;
                }

                const data = {
                    question_text: questionText,
                    question_type: questionType
                };

                if (questionType === 'choice') {
                    const choiceInputs = form.querySelectorAll('input[name="choices[]"]');
                    const choices = Array.from(choiceInputs).map(input => input.value);

                    if (choices.some(c => !c.trim())) {
                        alert('يرجى ملء جميع الخيارات الأربعة');
                        return;
                    }

                    data.choices = choices;
                    data.correct_choice_index = parseInt(form.querySelector('input[name="correct_choice_index"]:checked').value);
                } else {
                    const correctAnswer = form.querySelector('input[name="correct_answer"]').value;
                    if (!correctAnswer.trim()) {
                        alert('يرجى إدخال الإجابة الصحيحة');
                        return;
                    }
                    data.correct_answer = correctAnswer;
                }

                btn.disabled = true;
                btn.innerHTML = '<i class="bi bi-hourglass-split"></i> جاري الإضافة...';

                try {
                    const response = await fetch(`/room/${roomCode}/question-bank/add`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();

                    if (!response.ok) {
                        alert(result.error || 'حدث خطأ أثناء إضافة السؤال');
                        return;
                    }

                    if (result.success) {
                        // Update counts
                        document.getElementById('playerQuestionsCount').textContent = result.player_questions_count;
                        document.getElementById('totalQuestionsInBank').textContent = result.total_questions;

                        currentState.playerQuestionsCount = result.player_questions_count;

                        // Add to questions list or reload if at max
                        if (result.player_questions_count >= maxQuestionsPerPlayer) {
                            window.location.reload();
                        } else {
                            // Clear form
                            form.reset();
                            form.querySelector('input[name="question_type"][value="text"]').checked = true;
                            document.getElementById('textAnswerSection').style.display = 'block';
                            document.getElementById('choicesSection').style.display = 'none';

                            // Add question to list visually
                            const list = document.getElementById('playerQuestionsList');
                            if (list) {
                                const item = document.createElement('div');
                                item.className = 'question-item d-flex align-items-center justify-content-between p-3 mb-2';
                                item.dataset.questionId = result.question_id;
                                item.innerHTML = `
                                    <div class="flex-fill">
                                        <div class="fw-bold">${questionText}</div>
                                        <small class="text-muted-custom">
                                            ${questionType === 'choice' ? '<i class="bi bi-list-check me-1"></i>اختيار من متعدد' : '<i class="bi bi-fonts me-1"></i>إجابة نصية'}
                                        </small>
                                    </div>
                                    <button type="button" class="btn-game btn-game-outline btn-game-sm delete-question-btn" data-question-id="${result.question_id}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                `;
                                list.appendChild(item);
                                attachDeleteListeners();
                            } else {
                                window.location.reload();
                            }

                            // Enable start button if host
                            const startBtn = document.getElementById('endQuestionBankBtn');
                            if (startBtn && result.total_questions >= 1) {
                                startBtn.disabled = false;
                            }
                        }
                    } else {
                        alert(result.error || 'حدث خطأ أثناء إضافة السؤال');
                    }
                } catch (error) {
                    console.error('Error adding question:', error);
                    alert('حدث خطأ في الاتصال');
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-plus-lg"></i> إضافة السؤال';
                }
            });

            // Delete question
            function attachDeleteListeners() {
                document.querySelectorAll('.delete-question-btn').forEach(btn => {
                    btn.onclick = async function () {
                        const questionId = this.dataset.questionId;

                        if (!confirm('هل أنت متأكد من حذف هذا السؤال؟')) return;

                        try {
                            const response = await fetch(`/room/${roomCode}/question-bank/${questionId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            });

                            const result = await response.json();

                            if (result.success) {
                                // Remove from DOM
                                const item = document.querySelector(`.question-item[data-question-id="${questionId}"]`);
                                if (item) item.remove();

                                // Update counts
                                document.getElementById('playerQuestionsCount').textContent = result.player_questions_count;
                                document.getElementById('totalQuestionsInBank').textContent = result.total_questions;

                                currentState.playerQuestionsCount = result.player_questions_count;

                                // Show form if was hidden
                                if (result.player_questions_count < maxQuestionsPerPlayer) {
                                    window.location.reload();
                                }
                            } else {
                                alert(result.error || 'حدث خطأ أثناء حذف السؤال');
                            }
                        } catch (error) {
                            console.error('Error deleting question:', error);
                        }
                    };
                });
            }

            attachDeleteListeners();

            // End question bank phase (host only)
            document.getElementById('endQuestionBankBtn')?.addEventListener('click', endQuestionBankPhase);

            async function endQuestionBankPhase() {
                try {
                    const response = await fetch(`/room/${roomCode}/question-bank/end`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    const result = await response.json();

                    if (result.success) {
                        window.location.reload();
                    } else {
                        if (!isHost) {
                            // Non-host timer ended, just reload
                            window.location.reload();
                        } else {
                            alert(result.error || 'حدث خطأ');
                        }
                    }
                } catch (error) {
                    console.error('Error ending question bank:', error);
                    window.location.reload();
                }
            }
        @endif

            @if(!$isQuestionBankPhase && $currentQuestion && $currentQuestion->isRevealing())
                // Reveal phase countdown to next question
                let countdown = {{ config('game.next_question_countdown', 5) }};
                const countdownEl = document.getElementById('countdown');

                function startNextQuestionCountdown() {
                    if (countdownInterval) clearInterval(countdownInterval);

                    countdownInterval = setInterval(() => {
                        // Pause countdown if someone is revealing
                        if (currentState.revealingPlayerId) {
                            return;
                        }

                        countdown--;
                        if (countdownEl) countdownEl.textContent = countdown;

                        if (countdown <= 0) {
                            clearInterval(countdownInterval);
                            if (isHost) {
                                document.getElementById('nextQuestionForm').submit();
                            }
                        }
                    }, 1000);
                }

                startNextQuestionCountdown();
            @endif

        // Handle reveal button clicks
        function attachRevealButtonListeners() {
            document.querySelectorAll('.reveal-btn').forEach(btn => {
                btn.addEventListener('click', async function () {
                    const targetId = this.dataset.targetId;
                    const targetName = this.dataset.targetName;

                    // Try to lock the reveal
                    try {
                        const response = await fetch(`/room/${roomCode}/start-reveal`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Successfully locked - show modal with timer
                            isInRevealMode = true;
                            document.getElementById('targetFakeName').textContent = targetName;
                            document.getElementById('targetIdInput').value = targetId;

                            // Reset player selection
                            document.querySelectorAll('.player-select-card').forEach(c => {
                                c.classList.remove('selected');
                                const checkIcon = c.querySelector('.check-icon');
                                if (checkIcon) checkIcon.style.display = 'none';
                            });
                            document.getElementById('guessedPlayerInput').value = '';
                            document.getElementById('modalTimer').textContent = {{ config('game.reveal_timer', 10) }};

                            // Remove any Bootstrap backdrops that might exist
                            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

                            // Show our custom backdrop
                            document.getElementById('customModalBackdrop').style.display = 'block';

                            // Use getOrCreateInstance to avoid creating multiple modals/backdrops
                            const modalEl = document.getElementById('revealModal');
                            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                            modal.show();

                            // Remove Bootstrap backdrop again after modal shows (just in case)
                            setTimeout(() => {
                                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                            }, 100);

                            // Start reveal timer
                            let revealCountdown = {{ config('game.reveal_timer', 10) }};
                            const modalTimerEl = document.getElementById('modalTimer');

                            revealTimerInterval = setInterval(() => {
                                revealCountdown--;
                                if (modalTimerEl) modalTimerEl.textContent = revealCountdown;

                                if (revealCountdown <= 0) {
                                    clearInterval(revealTimerInterval);
                                    // Player didn't choose in time - they get revealed
                                    document.getElementById('revealTimeoutForm').submit();
                                }
                            }, 1000);
                        } else {
                            alert(data.error || 'لاعب آخر يحاول الكشف حالياً');
                        }
                    } catch (error) {
                        console.error('Error starting reveal:', error);
                    }
                });
            });
        }

        attachRevealButtonListeners();

        // When reveal form is submitted, clear the timer
        document.getElementById('revealForm')?.addEventListener('submit', function (e) {
            // Check if a player is selected
            const selectedPlayer = document.getElementById('guessedPlayerInput').value;
            if (!selectedPlayer) {
                e.preventDefault();
                alert('يرجى اختيار لاعب للكشف عنه');
                return false;
            }
            if (revealTimerInterval) {
                clearInterval(revealTimerInterval);
            }
            isInRevealMode = false;
            // Hide custom backdrop
            document.getElementById('customModalBackdrop').style.display = 'none';
        });

        // Hide custom backdrop when modal is hidden
        document.getElementById('revealModal')?.addEventListener('hidden.bs.modal', function () {
            document.getElementById('customModalBackdrop').style.display = 'none';
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        });

        // Handle player card selection in reveal modal
        function initPlayerCardListeners() {
            document.querySelectorAll('#playersList .player-select-card').forEach(card => {
                card.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Remove selected class from all cards
                    document.querySelectorAll('#playersList .player-select-card').forEach(c => {
                        c.classList.remove('selected');
                        const checkIcon = c.querySelector('.check-icon');
                        if (checkIcon) checkIcon.style.display = 'none';
                    });

                    // Add selected class to clicked card
                    this.classList.add('selected');
                    const checkIcon = this.querySelector('.check-icon');
                    if (checkIcon) checkIcon.style.display = 'block';

                    // Set the hidden input value
                    document.getElementById('guessedPlayerInput').value = this.dataset.playerId;
                });
            });
        }

        // Initialize on page load
        initPlayerCardListeners();

        // Also re-initialize when modal is shown (just in case)
        document.getElementById('revealModal')?.addEventListener('shown.bs.modal', function () {
            initPlayerCardListeners();
        });

        // Handle reveal state update
        function handleRevealStateUpdate(revealingPlayerId) {
            currentState.revealingPlayerId = revealingPlayerId;

            const notice = document.getElementById('activeRevealNotice');

            if (revealingPlayerId && revealingPlayerId !== playerId) {
                // Someone else is revealing - show notice, pause countdown
                if (notice) {
                    notice.style.display = 'flex';
                }
                // Disable reveal buttons for others
                document.querySelectorAll('.reveal-btn').forEach(btn => {
                    btn.disabled = true;
                    btn.classList.add('disabled');
                });
            } else if (!revealingPlayerId) {
                // Reveal finished - hide notice
                if (notice) {
                    notice.style.display = 'none';
                }
                // Re-enable buttons
                document.querySelectorAll('.reveal-btn').forEach(btn => {
                    btn.disabled = false;
                    btn.classList.remove('disabled');
                });
            }
        }

        // WebSocket connection with Echo
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof window.Echo !== 'undefined') {
                console.log('Connecting to WebSocket channel for game: room.' + roomCode);

                window.Echo.channel('room.' + roomCode)
                    .listen('.game.state.updated', (e) => {
                        console.log('Game state updated:', e);

                        switch (e.type) {
                            case 'game_ended':
                                window.location.href = e.data.redirect_url || `/room/${roomCode}/results`;
                                break;

                            case 'room_deleted':
                                window.location.href = '{{ route("game.landing") }}';
                                break;

                            case 'question_bank_updated':
                                // Update total questions count
                                const totalEl = document.getElementById('totalQuestionsInBank');
                                if (totalEl) {
                                    totalEl.textContent = e.data.total_questions;
                                }
                                // Enable start button if host
                                const startBtn = document.getElementById('endQuestionBankBtn');
                                if (startBtn && e.data.total_questions >= 1) {
                                    startBtn.disabled = false;
                                }
                                break;

                            case 'question_bank_ended':
                                // Question bank phase ended, reload to show first question
                                window.location.reload();
                                break;

                            case 'answer_submitted':
                                console.log('Answer count:', e.data.answers_count, '/', e.data.total_players);
                                // Update the answered count display
                                const answeredCountEl = document.getElementById('answeredCount');
                                const onlinePlayersEl = document.getElementById('onlinePlayersCount');
                                if (answeredCountEl) {
                                    answeredCountEl.textContent = e.data.answers_count;
                                }
                                if (onlinePlayersEl && e.data.total_players) {
                                    onlinePlayersEl.textContent = e.data.total_players;
                                }
                                break;

                            case 'reveal_phase_started':
                                if (currentState.phase === 'answering') {
                                    window.location.reload();
                                }
                                break;

                            case 'reveal_started':
                                handleRevealStateUpdate(e.data.revealing_player_id);
                                break;

                            case 'reveal_cancelled':
                                handleRevealStateUpdate(null);
                                break;

                            case 'reveal_completed':
                                window.location.reload();
                                break;

                            case 'next_question':
                                if (e.data.question_number !== currentState.questionIndex) {
                                    window.location.reload();
                                }
                                break;
                        }
                    });
            } else {
                console.log('Echo not available, falling back to polling');
                setInterval(pollGameState, 1500);
            }
        });

        // Fallback polling
        async function pollGameState() {
            if (isTyping || isInRevealMode) return;

            try {
                const response = await fetch(`/room/${roomCode}/state`);
                const state = await response.json();

                if (state.room_status === 'finished') {
                    window.location.href = `/room/${roomCode}/results`;
                    return;
                }

                // Phase changed
                if (state.phase !== currentState.phase) {
                    window.location.reload();
                    return;
                }

                // Question changed
                if (state.current_question_index !== currentState.questionIndex && state.current_question_index > 0) {
                    window.location.reload();
                    return;
                }

                // Update question bank count
                if (state.phase === 'question_bank') {
                    const totalEl = document.getElementById('totalQuestionsInBank');
                    if (totalEl && state.total_questions_in_bank) {
                        totalEl.textContent = state.total_questions_in_bank;
                    }
                }

                // Reveal state update
                if (state.revealing_player_id !== currentState.revealingPlayerId) {
                    handleRevealStateUpdate(state.revealing_player_id);
                    if (!state.revealing_player_id && currentState.revealingPlayerId) {
                        window.location.reload();
                    }
                }

            } catch (error) {
                console.log('Polling error:', error);
            }
        }
    </script>
@endpush