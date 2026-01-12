@extends('layouts.game')

@section('title', 'غرفة الانتظار')

@section('content')
<div class="container py-3">
    <!-- Compact Header -->
    <div class="d-flex justify-content-between align-items-center mb-3 animate-fade-in">
        <div class="d-flex align-items-center gap-2">
            <img src="{{ asset('imgs/logo-nightsky.svg') }}" alt="ملثم" style="width: 36px; height: 36px;">
            <h6 class="mb-0 fw-bold">غرفة الانتظار</h6>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn-game btn-game-outline btn-game-sm" id="showInstructionsBtn" title="التعليمات">
                <i class="bi bi-question-circle"></i>
            </button>
            <form action="{{ route('game.leave', $room->code) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn-game btn-game-outline btn-game-sm">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="game-alert game-alert-danger mb-3 animate-shake">
            <i class="bi bi-exclamation-circle"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <!-- Room Code & Your Info - Compact Row -->
    <div class="row g-2 mb-3">
        <div class="col-6">
            <div class="game-card game-card-highlight animate-fade-in p-3 text-center">
                <small class="text-muted-custom d-block mb-1">رمز الغرفة</small>
                <div class="room-code fs-4 mb-2" id="roomCode">{{ $room->code }}</div>
                <div class="d-flex gap-1 justify-content-center">
                    <button type="button" class="btn-game btn-game-secondary btn-game-sm" onclick="copyRoomCode()">
                        <i class="bi bi-clipboard"></i> الرمز
                    </button>
                    <button type="button" class="btn-game btn-game-primary btn-game-sm" onclick="copyRoomLink()">
                        <i class="bi bi-link-45deg"></i> الرابط
                    </button>
                </div>
                <div id="copyFeedback" class="text-success-custom mt-1 small" style="display: none;">
                    <i class="bi bi-check-circle"></i> <span id="copyMessage">تم!</span>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="game-card animate-fade-in p-3">
                <small class="text-muted-custom d-block mb-1">معلوماتك</small>
                <div class="fw-bold">{{ $player->name }}</div>
                <div class="text-primary-custom small">{{ $player->fake_name }}</div>
                @if($player->is_host)
                    <span class="badge bg-warning text-dark mt-1" style="font-size: 0.65rem;">
                        <i class="bi bi-star-fill"></i> مضيف
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Players List - Compact -->
    <div class="game-card animate-fade-in mb-3">
        <div class="game-card-header py-2 d-flex justify-content-between align-items-center">
            <span class="fw-bold">
                <i class="bi bi-people-fill me-1"></i>
                اللاعبون ({{ $players->count() }})
            </span>
            <span class="small">
                <span class="text-success">{{ $players->where('status', 'ready')->count() }} جاهز</span>
                /
                <span class="text-muted">{{ $players->where('status', 'waiting')->count() }} ينتظر</span>
            </span>
        </div>
        <div class="game-card-body py-2">
            <div class="d-flex flex-wrap gap-2">
                @foreach($players as $p)
                    <div class="player-chip {{ $p->status === 'ready' ? 'ready' : 'waiting' }} {{ $p->id === $player->id ? 'is-you' : '' }}">
                        <span class="avatar">{{ mb_substr($p->name, 0, 1) }}</span>
                        <span class="name">{{ $p->name }}</span>
                        @if($p->is_host)
                            <i class="bi bi-star-fill text-warning"></i>
                        @endif
                        @if($p->status === 'ready')
                            <i class="bi bi-check-circle-fill text-success"></i>
                        @endif
                    </div>
                @endforeach
            </div>

            @if($players->count() < config('game.min_players', 3))
                <div class="text-center text-muted-custom small mt-2">
                    <i class="bi bi-info-circle"></i> يلزم {{ config('game.min_players', 3) }} لاعبين على الأقل
                </div>
            @endif
        </div>
    </div>

    <!-- Actions - Always Visible -->
    <div class="d-flex gap-2 justify-content-center animate-fade-in">
        <button
            type="button"
            class="btn-game {{ $player->status !== 'ready' ? 'btn-game-success' : 'btn-game-outline' }}"
            id="readyBtn"
            onclick="toggleReady()"
        >
            <i class="bi {{ $player->status !== 'ready' ? 'bi-check-circle' : 'bi-x-circle' }}"></i>
            <span id="readyBtnText">{{ $player->status !== 'ready' ? 'أنا جاهز!' : 'إلغاء' }}</span>
        </button>

        @if($player->is_host)
            <form action="{{ route('game.start', $room->code) }}" method="POST" id="startGameForm">
                @csrf
                <button
                    type="submit"
                    class="btn-game btn-game-primary"
                    id="startGameBtn"
                    @if($players->where('status', 'ready')->count() < config('game.min_players', 3)) disabled @endif
                >
                    <i class="bi bi-play-circle"></i> ابدأ اللعبة
                </button>
            </form>
        @endif
    </div>

    <p class="text-center text-muted-custom small mt-2 mb-0" id="hostMessage" @if(!$player->is_host || $players->where('status', 'ready')->count() >= config('game.min_players', 3)) style="display: none;" @endif>
        انتظر حتى يكون {{ config('game.min_players', 3) }} لاعبين جاهزين
    </p>
</div>

<!-- Custom Modal Backdrop -->
<div id="customModalBackdrop" class="custom-modal-backdrop" style="display: none;"></div>

<!-- Instructions Modal - Multi-Step Wizard -->
<div class="modal fade" id="instructionsModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-book me-2"></i>
                    <span id="stepTitle">كيفية اللعب</span>
                </h5>
                <div class="step-indicators d-flex gap-1 ms-auto me-3">
                    <span class="step-dot active" data-step="1"></span>
                    <span class="step-dot" data-step="2"></span>
                    <span class="step-dot" data-step="3"></span>
                    <span class="step-dot" data-step="4"></span>
                </div>
            </div>
            <div class="modal-body">
                <!-- Step 1: Overview -->
                <div class="instruction-step" data-step="1">
                    <div class="text-center mb-4">
                        <div class="step-icon mb-3">
                            <i class="bi bi-info-circle"></i>
                        </div>
                        <h4 class="fw-bold mb-3">مرحباً بك في ملثم!</h4>
                        <p class="text-muted-custom">
                            لعبة أسئلة وتخمين! كل لاعب يحصل على اسم مستعار سري.
                            <br>أجب على الأسئلة وحاول كشف هوية اللاعبين الآخرين من خلال إجاباتهم.
                        </p>
                    </div>
                    <div class="d-flex flex-column gap-3">
                        <div class="instruction-card">
                            <div class="instruction-number">1</div>
                            <div>
                                <strong>بنك الأسئلة</strong>
                                <p class="text-muted-custom mb-0 small">يكتب كل لاعب أسئلة قبل بدء اللعبة</p>
                            </div>
                        </div>
                        <div class="instruction-card">
                            <div class="instruction-number">2</div>
                            <div>
                                <strong>الإجابة</strong>
                                <p class="text-muted-custom mb-0 small">يجيب الجميع على السؤال المطروح</p>
                            </div>
                        </div>
                        <div class="instruction-card">
                            <div class="instruction-number">3</div>
                            <div>
                                <strong>الكشف</strong>
                                <p class="text-muted-custom mb-0 small">حاول تخمين هوية لاعب من إجابته</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Question Types -->
                <div class="instruction-step" data-step="2" style="display: none;">
                    <div class="text-center mb-4">
                        <div class="step-icon mb-3">
                            <i class="bi bi-question-circle"></i>
                        </div>
                        <h4 class="fw-bold mb-3">أنواع الأسئلة</h4>
                    </div>
                    <div class="d-flex flex-column gap-3">
                        <div class="question-type-card">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <i class="bi bi-fonts text-info fs-3"></i>
                                <strong class="fs-5">إجابة نصية</strong>
                            </div>
                            <p class="text-muted-custom mb-0">
                                سؤال مفتوح يكتب اللاعب الإجابة بنفسه. صاحب السؤال يحصل على نقطة لكل إجابة خاطئة، لكن فقط إذا أجاب بعض اللاعبين صح وبعضهم خطأ.
                            </p>
                        </div>
                        <div class="question-type-card">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <i class="bi bi-list-check text-success fs-3"></i>
                                <strong class="fs-5">اختيار من متعدد</strong>
                            </div>
                            <p class="text-muted-custom mb-0">
                                سؤال بأربع خيارات يختار منها اللاعب. الإجابة الصحيحة تمنح نقطة للمجيب.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Scoring -->
                <div class="instruction-step" data-step="3" style="display: none;">
                    <div class="text-center mb-4">
                        <div class="step-icon mb-3">
                            <i class="bi bi-trophy"></i>
                        </div>
                        <h4 class="fw-bold mb-3">نظام النقاط</h4>
                    </div>
                    <div class="d-flex flex-column gap-3">
                        <div class="scoring-card">
                            <i class="bi bi-check-circle-fill text-success fs-4"></i>
                            <div>
                                <strong>إجابة صحيحة</strong>
                                <span class="text-primary-custom">+1 نقطة</span>
                            </div>
                        </div>
                        <div class="scoring-card">
                            <i class="bi bi-pencil-fill text-info fs-4"></i>
                            <div>
                                <strong>صاحب السؤال النصي</strong>
                                <span class="text-primary-custom">+1 نقطة لكل إجابة خاطئة</span>
                                <small class="text-muted-custom d-block">(إذا كان هناك مزيج من الإجابات)</small>
                            </div>
                        </div>
                        <div class="scoring-card">
                            <i class="bi bi-eye-fill text-warning fs-4"></i>
                            <div>
                                <strong>كشف صحيح</strong>
                                <span class="text-success">تحصل على نقاط الشخص المكشوف</span>
                            </div>
                        </div>
                        <div class="scoring-card scoring-card-danger">
                            <i class="bi bi-x-circle-fill text-danger fs-4"></i>
                            <div>
                                <strong>كشف خاطئ</strong>
                                <span class="text-danger">تخسر نقاطك وتخرج من اللعبة!</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Tips -->
                <div class="instruction-step" data-step="4" style="display: none;">
                    <div class="text-center mb-4">
                        <div class="step-icon mb-3">
                            <i class="bi bi-lightbulb"></i>
                        </div>
                        <h4 class="fw-bold mb-3">نصائح للفوز</h4>
                    </div>
                    <div class="d-flex flex-column gap-3">
                        <div class="tip-card">
                            <i class="bi bi-check2-circle text-success"></i>
                            <span>اكتب أسئلة متوازنة - ليست سهلة جداً ولا صعبة جداً</span>
                        </div>
                        <div class="tip-card">
                            <i class="bi bi-eye text-info"></i>
                            <span>راقب أسلوب إجابات اللاعبين لتخمين هوياتهم</span>
                        </div>
                        <div class="tip-card">
                            <i class="bi bi-exclamation-triangle text-warning"></i>
                            <span>لا تكشف إلا إذا كنت متأكداً - الخطأ يعني الخروج!</span>
                        </div>
                        <div class="tip-card">
                            <i class="bi bi-shuffle text-primary-custom"></i>
                            <span>حاول تغيير أسلوب إجاباتك لتصعّب على الآخرين معرفتك</span>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <p class="text-muted-custom mb-0">
                            <i class="bi bi-stars text-warning me-1"></i>
                            حظاً موفقاً!
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn-game btn-game-outline" id="skipInstructionsBtn">
                    تخطي
                </button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn-game btn-game-secondary" id="prevStepBtn" style="display: none;">
                        <i class="bi bi-arrow-right"></i>
                        السابق
                    </button>
                    <button type="button" class="btn-game btn-game-primary" id="nextStepBtn">
                        التالي
                        <i class="bi bi-arrow-left"></i>
                    </button>
                    <button type="button" class="btn-game btn-game-success" id="finishBtn" style="display: none;">
                        <i class="bi bi-check-circle"></i>
                        فهمت!
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
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

    #instructionsModal {
        z-index: 1050;
    }

    #instructionsModal .modal-dialog {
        z-index: 1051;
    }

    /* Step Indicators */
    .step-indicators {
        display: flex;
        gap: 6px;
    }

    .step-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: var(--color-border);
        transition: all 0.3s ease;
    }

    .step-dot.active {
        background: var(--color-primary);
        transform: scale(1.2);
    }

    .step-dot.completed {
        background: var(--color-success);
    }

    /* Step Icon */
    .step-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-hover) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }

    .step-icon i {
        font-size: 2.5rem;
        color: white;
    }

    /* Instruction Cards */
    .instruction-card {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 0.75rem 1rem;
        background: var(--color-card);
        border-radius: var(--radius-md);
        border: 1px solid var(--color-border);
    }

    .instruction-number {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-hover) 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        flex-shrink: 0;
    }

    /* Question Type Cards */
    .question-type-card {
        padding: 1.25rem;
        background: var(--color-card);
        border-radius: var(--radius-md);
        border: 1px solid var(--color-border);
    }

    /* Scoring Cards */
    .scoring-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: var(--color-card);
        border-radius: var(--radius-md);
        border: 1px solid var(--color-border);
    }

    .scoring-card i {
        flex-shrink: 0;
    }

    .scoring-card div {
        display: flex;
        flex-direction: column;
    }

    .scoring-card-danger {
        border-color: rgba(239, 68, 68, 0.3);
        background: rgba(239, 68, 68, 0.05);
    }

    /* Tip Cards */
    .tip-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.75rem 1rem;
        background: var(--color-card);
        border-radius: var(--radius-md);
        border: 1px solid var(--color-border);
    }

    .tip-card i {
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    /* Animation for step transitions */
    .instruction-step {
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateX(-10px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
</style>
@endpush

@push('scripts')
<script>
    const roomCode = '{{ $room->code }}';

    // Instructions Modal - Cookie & Wizard Logic
    const INSTRUCTIONS_COOKIE = 'mulatham_instructions_seen';
    let currentStep = 1;
    const totalSteps = 4;
    const stepTitles = {
        1: 'كيفية اللعب',
        2: 'أنواع الأسئلة',
        3: 'نظام النقاط',
        4: 'نصائح للفوز'
    };

    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }

    function setCookie(name, value, days = 365) {
        const expires = new Date(Date.now() + days * 864e5).toUTCString();
        document.cookie = `${name}=${value}; expires=${expires}; path=/; SameSite=Lax`;
    }

    function showInstructionsModal() {
        // Clean up any stale Bootstrap backdrops
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

        // Show custom backdrop
        document.getElementById('customModalBackdrop').style.display = 'block';

        const modalEl = document.getElementById('instructionsModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();

        // Remove Bootstrap backdrop again after modal shows
        setTimeout(() => {
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        }, 100);
    }

    function hideInstructionsModal() {
        const modalEl = document.getElementById('instructionsModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
        document.getElementById('customModalBackdrop').style.display = 'none';
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    }

    function goToStep(step) {
        if (step < 1 || step > totalSteps) return;

        // Hide all steps
        document.querySelectorAll('.instruction-step').forEach(s => s.style.display = 'none');

        // Show current step
        document.querySelector(`.instruction-step[data-step="${step}"]`).style.display = 'block';

        // Update step dots
        document.querySelectorAll('.step-dot').forEach(dot => {
            const dotStep = parseInt(dot.dataset.step);
            dot.classList.remove('active', 'completed');
            if (dotStep < step) {
                dot.classList.add('completed');
            } else if (dotStep === step) {
                dot.classList.add('active');
            }
        });

        // Update title
        document.getElementById('stepTitle').textContent = stepTitles[step];

        // Update buttons
        const prevBtn = document.getElementById('prevStepBtn');
        const nextBtn = document.getElementById('nextStepBtn');
        const finishBtn = document.getElementById('finishBtn');

        prevBtn.style.display = step > 1 ? 'inline-flex' : 'none';
        nextBtn.style.display = step < totalSteps ? 'inline-flex' : 'none';
        finishBtn.style.display = step === totalSteps ? 'inline-flex' : 'none';

        currentStep = step;
    }

    // Initialize instructions modal logic
    document.addEventListener('DOMContentLoaded', function() {
        // Clean up any stale modal backdrops on page load
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

        // Show instructions on first visit
        if (!getCookie(INSTRUCTIONS_COOKIE)) {
            setTimeout(() => {
                showInstructionsModal();
            }, 500);
        }

        // Help button click
        document.getElementById('showInstructionsBtn')?.addEventListener('click', function() {
            currentStep = 1;
            goToStep(1);
            showInstructionsModal();
        });

        // Next button
        document.getElementById('nextStepBtn')?.addEventListener('click', function() {
            goToStep(currentStep + 1);
        });

        // Previous button
        document.getElementById('prevStepBtn')?.addEventListener('click', function() {
            goToStep(currentStep - 1);
        });

        // Skip button
        document.getElementById('skipInstructionsBtn')?.addEventListener('click', function() {
            setCookie(INSTRUCTIONS_COOKIE, 'true');
            hideInstructionsModal();
        });

        // Finish button
        document.getElementById('finishBtn')?.addEventListener('click', function() {
            setCookie(INSTRUCTIONS_COOKIE, 'true');
            hideInstructionsModal();
        });

        // Hide backdrop when modal is hidden by any means
        document.getElementById('instructionsModal')?.addEventListener('hidden.bs.modal', function() {
            document.getElementById('customModalBackdrop').style.display = 'none';
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        });
    });
    const roomUrl = '{{ url("/room/{$room->code}") }}';
    const playUrl = '{{ route("game.play", $room->code) }}';
    let isHost = {{ $player->is_host ? 'true' : 'false' }};
    const currentPlayerId = {{ $player->id }};
    const minPlayers = {{ config('game.min_players', 3) }};
    let myStatus = '{{ $player->status }}';

    // Toggle ready status via AJAX
    async function toggleReady() {
        const btn = document.getElementById('readyBtn');
        const btnText = document.getElementById('readyBtnText');
        const btnIcon = btn.querySelector('i');

        btn.disabled = true;

        try {
            const response = await fetch(`/room/${roomCode}/ready`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();

            if (data.success) {
                myStatus = data.status;
                updateReadyButton();
            }
        } catch (error) {
            console.error('Error toggling ready:', error);
        } finally {
            btn.disabled = false;
        }
    }

    // Update ready button appearance
    function updateReadyButton() {
        const btn = document.getElementById('readyBtn');
        const btnText = document.getElementById('readyBtnText');
        const btnIcon = btn.querySelector('i');

        if (myStatus === 'ready') {
            btn.classList.remove('btn-game-success');
            btn.classList.add('btn-game-outline');
            btnIcon.classList.remove('bi-check-circle');
            btnIcon.classList.add('bi-x-circle');
            btnText.textContent = 'إلغاء';
        } else {
            btn.classList.remove('btn-game-outline');
            btn.classList.add('btn-game-success');
            btnIcon.classList.remove('bi-x-circle');
            btnIcon.classList.add('bi-check-circle');
            btnText.textContent = 'أنا جاهز!';
        }
    }

    function showCopyFeedback(message) {
        const feedback = document.getElementById('copyFeedback');
        const messageEl = document.getElementById('copyMessage');
        messageEl.textContent = message;
        feedback.style.display = 'block';
        setTimeout(() => {
            feedback.style.display = 'none';
        }, 2000);
    }

    function copyRoomCode() {
        const code = document.getElementById('roomCode').textContent.trim();
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(code).then(() => {
                showCopyFeedback('تم نسخ الرمز!');
            }).catch(() => {
                fallbackCopy(code, 'تم نسخ الرمز!');
            });
        } else {
            fallbackCopy(code, 'تم نسخ الرمز!');
        }
    }

    function copyRoomLink() {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(roomUrl).then(() => {
                showCopyFeedback('تم نسخ الرابط!');
            }).catch(() => {
                fallbackCopy(roomUrl, 'تم نسخ الرابط!');
            });
        } else {
            fallbackCopy(roomUrl, 'تم نسخ الرابط!');
        }
    }

    function fallbackCopy(text, successMessage) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        try {
            document.execCommand('copy');
            showCopyFeedback(successMessage);
        } catch (err) {
            alert('لم نتمكن من النسخ. الرمز هو: ' + text);
        }
        document.body.removeChild(textarea);
    }

    // Update players list UI
    function updatePlayersUI(players, playerCount, readyCount, waitingCount, action = null) {
        // Check if current player is now host
        const currentPlayer = players.find(p => p.id === currentPlayerId);
        const wasHost = isHost;
        if (currentPlayer) {
            isHost = currentPlayer.is_host;
        }

        // If we just became host, reload the page to show host controls
        if (!wasHost && isHost) {
            window.location.reload();
            return;
        }

        // Update player chips
        const playersContainer = document.querySelector('.d-flex.flex-wrap.gap-2');
        if (playersContainer) {
            let html = '';
            players.forEach(p => {
                const isYou = p.id === currentPlayerId;
                const statusClass = p.status === 'ready' ? 'ready' : 'waiting';
                const youClass = isYou ? 'is-you' : '';
                html += `
                    <div class="player-chip ${statusClass} ${youClass}">
                        <span class="avatar">${p.name.charAt(0)}</span>
                        <span class="name">${p.name}</span>
                        ${p.is_host ? '<i class="bi bi-star-fill text-warning"></i>' : ''}
                        ${p.status === 'ready' ? '<i class="bi bi-check-circle-fill text-success"></i>' : ''}
                    </div>
                `;
            });
            playersContainer.innerHTML = html;
        }

        // Update header counts
        const headerTitle = document.querySelector('.game-card-header .fw-bold');
        if (headerTitle) {
            headerTitle.innerHTML = `<i class="bi bi-people-fill me-1"></i> اللاعبون (${playerCount})`;
        }

        const headerCounts = document.querySelector('.game-card-header .small');
        if (headerCounts) {
            headerCounts.innerHTML = `
                <span class="text-success">${readyCount} جاهز</span>
                /
                <span class="text-muted">${waitingCount} ينتظر</span>
            `;
        }

        // Update minimum players message
        const cardBody = document.querySelector('.game-card-body.py-2');
        let minMsg = document.querySelector('.game-card-body .text-center.text-muted-custom.small.mt-2');

        if (playerCount < minPlayers) {
            if (!minMsg && cardBody) {
                cardBody.insertAdjacentHTML('beforeend', `
                    <div class="text-center text-muted-custom small mt-2">
                        <i class="bi bi-info-circle"></i> يلزم ${minPlayers} لاعبين على الأقل
                    </div>
                `);
            }
        } else if (minMsg) {
            minMsg.remove();
        }

        // Update Start Game button state (for host)
        if (isHost) {
            const startBtn = document.getElementById('startGameBtn');
            const hostMessage = document.getElementById('hostMessage');

            if (startBtn) {
                startBtn.disabled = readyCount < minPlayers;
            }
            if (hostMessage) {
                hostMessage.style.display = readyCount < minPlayers ? 'block' : 'none';
            }
        }
    }

    // WebSocket connection with Echo
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof window.Echo !== 'undefined') {
            console.log('Connecting to WebSocket channel: room.' + roomCode);

            window.Echo.channel('room.' + roomCode)
                .listen('.player.updated', (e) => {
                    console.log('Player updated:', e);
                    // Check if current player was removed (not in player list)
                    const currentPlayerInList = e.players.find(p => p.id === currentPlayerId);
                    if (!currentPlayerInList) {
                        // Player was removed - redirect to landing
                        window.location.href = '{{ route("game.landing") }}';
                        return;
                    }
                    updatePlayersUI(e.players, e.player_count, e.ready_count, e.waiting_count, e.action);
                })
                .listen('.game.state.updated', (e) => {
                    console.log('Game state updated:', e);
                    if (e.type === 'game_started') {
                        window.location.href = playUrl;
                    }
                    if (e.type === 'room_deleted') {
                        window.location.href = '{{ route("game.landing") }}';
                    }
                });
        } else {
            console.log('Echo not available, falling back to polling');
            // Fallback to polling if Echo is not available
            setInterval(checkForUpdates, 2000);
        }
    });

    // Fallback polling (only used if WebSocket fails)
    async function checkForUpdates() {
        try {
            const response = await fetch(window.location.href, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (response.url.includes('/play')) {
                window.location.href = response.url;
                return;
            }

            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            const newPlayersList = doc.querySelector('.d-flex.flex-wrap.gap-2');
            const currentPlayersList = document.querySelector('.d-flex.flex-wrap.gap-2');
            if (newPlayersList && currentPlayersList) {
                currentPlayersList.innerHTML = newPlayersList.innerHTML;
            }

            const newCounts = doc.querySelector('.game-card-header .small');
            const currentCounts = document.querySelector('.game-card-header .small');
            if (newCounts && currentCounts) {
                currentCounts.innerHTML = newCounts.innerHTML;
            }

            const newTitle = doc.querySelector('.game-card-header .fw-bold');
            const currentTitle = document.querySelector('.game-card-header .fw-bold');
            if (newTitle && currentTitle) {
                currentTitle.innerHTML = newTitle.innerHTML;
            }

            if (isHost) {
                const newStartBtn = doc.getElementById('startGameBtn');
                const currentStartBtn = document.getElementById('startGameBtn');
                const hostMessage = document.getElementById('hostMessage');

                if (newStartBtn && currentStartBtn) {
                    currentStartBtn.disabled = newStartBtn.disabled;
                    if (hostMessage) {
                        hostMessage.style.display = newStartBtn.disabled ? 'block' : 'none';
                    }
                }
            }
        } catch (error) {
            console.log('Polling error:', error);
        }
    }
</script>
@endpush
