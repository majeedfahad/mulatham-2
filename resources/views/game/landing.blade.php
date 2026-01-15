@extends('layouts.game')

@section('title', 'الرئيسية')

@section('content')
    <div class="container py-4">
        <!-- Hero Section -->
        <div class="text-center mb-4 animate-fade-in">
            <img src="{{ asset('imgs/logo-nightsky.svg') }}" alt="ملثم" class="game-logo mb-3"
                style="width: 140px; height: 140px;">
            <h1 class="game-title mb-2">ملثم</h1>
            <p class="game-subtitle mb-0">لعبة الهوية الغامضة</p>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="row justify-content-center mb-4">
                <div class="col-md-6 col-lg-5">
                    <div class="game-alert game-alert-danger animate-shake">
                        <i class="bi bi-exclamation-circle fs-5"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                </div>
            </div>
        @endif

        <!-- Main Card - Unified Create/Join -->
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-5 animate-fade-in" style="animation-delay: 0.1s">
                <div class="game-card game-card-highlight">
                    <div class="game-card-body p-4">
                        <!-- Mode Toggle -->
                        <div class="mode-toggle mb-4">
                            <button type="button" class="mode-btn active" data-mode="create">
                                <i class="bi bi-plus-lg"></i>
                                غرفة جديدة
                            </button>
                            <button type="button" class="mode-btn" data-mode="join">
                                <i class="bi bi-door-open"></i>
                                انضمام
                            </button>
                        </div>

                        <!-- Create Room Form -->
                        <form action="{{ route('game.create') }}" method="POST" id="createForm">
                            @csrf
                            <div class="mb-3">
                                <label class="game-label">
                                    <i class="bi bi-person-fill me-1"></i>
                                    اسمك الحقيقي
                                </label>
                                <input type="text" name="name" class="game-input player-name-input" placeholder="أدخل اسمك"
                                    value="{{ old('name') }}" required maxlength="50" autocomplete="off">
                            </div>
                            <div class="mb-4">
                                <label class="game-label">
                                    <i class="bi bi-mask me-1"></i>
                                    اسمك المستعار
                                    <span class="text-muted-custom fw-normal">(سري)</span>
                                </label>
                                <input type="text" name="fake_name" class="game-input" placeholder="اختر اسماً يخفي هويتك"
                                    value="{{ old('fake_name') }}" required maxlength="50" autocomplete="off">
                            </div>
                            <button type="submit" class="btn-game btn-game-primary w-100 btn-game-lg">
                                <i class="bi bi-play-fill"></i>
                                إنشاء الغرفة
                            </button>
                        </form>

                        <!-- Join Room Form (Hidden by default) -->
                        <form action="{{ route('game.join') }}" method="POST" id="joinForm" style="display: none;">
                            @csrf
                            <div class="mb-3">
                                <label class="game-label">
                                    <i class="bi bi-key-fill me-1"></i>
                                    رمز الغرفة
                                </label>
                                <input type="text" name="code" class="game-input room-code-input" placeholder="أدخل الرمز"
                                    value="{{ old('code') }}" required maxlength="6" autocomplete="off">
                            </div>
                            <div class="mb-3">
                                <label class="game-label">
                                    <i class="bi bi-person-fill me-1"></i>
                                    اسمك الحقيقي
                                </label>
                                <input type="text" name="name" class="game-input player-name-input" placeholder="أدخل اسمك"
                                    value="{{ old('name') }}" required maxlength="50" autocomplete="off">
                            </div>
                            <div class="mb-4">
                                <label class="game-label">
                                    <i class="bi bi-mask me-1"></i>
                                    اسمك المستعار
                                    <span class="text-muted-custom fw-normal">(سري)</span>
                                </label>
                                <input type="text" name="fake_name" class="game-input" placeholder="اختر اسماً يخفي هويتك"
                                    value="{{ old('fake_name') }}" required maxlength="50" autocomplete="off">
                            </div>
                            <button type="submit" class="btn-game btn-game-primary w-100 btn-game-lg">
                                <i class="bi bi-arrow-left-circle"></i>
                                انضمام للغرفة
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- How to Play - Compact -->
        <div class="row justify-content-center mt-5">
            <div class="col-md-10 col-lg-8 animate-fade-in" style="animation-delay: 0.2s">
                <div class="text-center mb-4">
                    <h5 class="fw-bold text-primary-custom">
                        <i class="bi bi-stars me-2"></i>
                        كيف تلعب؟
                    </h5>
                </div>
                <div class="how-to-play-grid">
                    <div class="how-to-play-item">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h6 class="fw-bold mb-1">اختر هوية سرية</h6>
                            <p class="text-muted-custom small mb-0">كل لاعب يختار اسماً مستعاراً لا يعرفه الآخرون</p>
                        </div>
                    </div>
                    <div class="how-to-play-item">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h6 class="fw-bold mb-1">اكتبوا الأسئلة</h6>
                            <p class="text-muted-custom small mb-0">كل لاعب يكتب أسئلة معرفية في بداية اللعبة</p>
                        </div>
                    </div>
                    <div class="how-to-play-item">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h6 class="fw-bold mb-1">أجب وراقب</h6>
                            <p class="text-muted-custom small mb-0">أجب على الأسئلة وراقب إجابات الآخرين بأسمائهم المستعارة
                            </p>
                        </div>
                    </div>
                    <div class="how-to-play-item">
                        <div class="step-number">4</div>
                        <div class="step-content">
                            <h6 class="fw-bold mb-1">اكشف واربح</h6>
                            <p class="text-muted-custom small mb-0">خمّن من وراء كل اسم - الصح = نقاط، الخطأ = خروج!</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Suggestions Link -->
            <div class="row justify-content-center mt-4">
                <div class="col-md-6 col-lg-4 animate-fade-in" style="animation-delay: 0.3s">
                    <a href="{{ route('suggestions.index') }}" class="suggestions-link">
                        <div class="suggestions-link-content">
                            <div class="suggestions-icon">
                                <i class="bi bi-lightbulb-fill"></i>
                            </div>
                            <div class="suggestions-text">
                                <span class="suggestions-title">الاقتراحات والأفكار</span>
                                <span class="suggestions-subtitle">شارك أفكارك وصوّت للميزات القادمة</span>
                            </div>
                            <i class="bi bi-chevron-left suggestions-arrow"></i>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-5 text-muted-custom">
                <small>صُنع بـ <i class="bi bi-heart-fill text-danger-custom"></i> للسهرات الممتعة</small>
            </div>
        </div>
@endsection

    @push('styles')
        <style>
            /* Mode Toggle */
            .mode-toggle {
                display: flex;
                background: var(--color-background-light);
                border-radius: var(--radius-lg);
                padding: 4px;
                gap: 4px;
            }

            .mode-btn {
                flex: 1;
                padding: 12px 16px;
                border: none;
                background: transparent;
                color: var(--color-text-muted);
                font-family: inherit;
                font-size: 0.95rem;
                font-weight: 600;
                border-radius: var(--radius-md);
                cursor: pointer;
                transition: all 0.2s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }

            .mode-btn:hover {
                color: var(--color-text);
            }

            .mode-btn.active {
                background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-light) 100%);
                color: #0a0e1a;
                box-shadow: var(--shadow-sm);
            }

            /* Room Code Input */
            .room-code-input {
                font-size: 1.5rem !important;
                font-weight: 700 !important;
                letter-spacing: 0.15em !important;
                text-transform: uppercase;
                text-align: center;
            }

            /* How to Play Grid */
            .how-to-play-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 16px;
            }

            @media (max-width: 576px) {
                .how-to-play-grid {
                    grid-template-columns: 1fr;
                }
            }

            .how-to-play-item {
                display: flex;
                align-items: flex-start;
                gap: 12px;
                padding: 16px;
                background: var(--color-card);
                border: 1px solid var(--color-border);
                border-radius: var(--radius-md);
                transition: all 0.2s ease;
            }

            .how-to-play-item:hover {
                border-color: var(--color-primary);
                transform: translateY(-2px);
            }

            .step-number {
                width: 32px;
                height: 32px;
                min-width: 32px;
                border-radius: 50%;
                background: linear-gradient(135deg, var(--color-secondary) 0%, var(--color-secondary-light) 100%);
                color: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 800;
                font-size: 0.9rem;
            }

            .step-content {
                flex: 1;
            }

            .step-content h6 {
                color: var(--color-text);
            }

            /* Suggestions Link */
            .suggestions-link {
                display: block;
                text-decoration: none;
                background: linear-gradient(135deg, rgba(201, 162, 39, 0.08) 0%, rgba(99, 102, 241, 0.08) 100%);
                border: 1px solid var(--color-border);
                border-radius: var(--radius-lg);
                padding: 16px 20px;
                transition: all 0.3s ease;
            }

            .suggestions-link:hover {
                border-color: var(--color-primary);
                transform: translateY(-3px);
                box-shadow: var(--shadow-md), 0 0 20px rgba(201, 162, 39, 0.15);
            }

            .suggestions-link-content {
                display: flex;
                align-items: center;
                gap: 14px;
            }

            .suggestions-icon {
                width: 44px;
                height: 44px;
                border-radius: var(--radius-md);
                background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-light) 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.25rem;
                color: #0a0e1a;
                flex-shrink: 0;
            }

            .suggestions-text {
                flex: 1;
                display: flex;
                flex-direction: column;
                gap: 2px;
            }

            .suggestions-title {
                font-weight: 700;
                font-size: 0.95rem;
                color: var(--color-text);
            }

            .suggestions-subtitle {
                font-size: 0.8rem;
                color: var(--color-text-muted);
            }

            .suggestions-arrow {
                color: var(--color-text-muted);
                font-size: 1.25rem;
                transition: transform 0.2s ease;
            }

            .suggestions-link:hover .suggestions-arrow {
                transform: translateX(-4px);
                color: var(--color-primary);
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            // Mode toggle functionality
            const modeBtns = document.querySelectorAll('.mode-btn');
            const createForm = document.getElementById('createForm');
            const joinForm = document.getElementById('joinForm');

            modeBtns.forEach(btn => {
                btn.addEventListener('click', function () {
                    // Update active state
                    modeBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    // Show/hide forms
                    const mode = this.dataset.mode;
                    if (mode === 'create') {
                        createForm.style.display = 'block';
                        joinForm.style.display = 'none';
                    } else {
                        createForm.style.display = 'none';
                        joinForm.style.display = 'block';
                        // Focus on room code input
                        joinForm.querySelector('.room-code-input')?.focus();
                    }
                });
            });

            // Auto-uppercase room code
            document.querySelectorAll('.room-code-input').forEach(input => {
                input.addEventListener('input', function () {
                    this.value = this.value.toUpperCase();
                });
            });

            // Sync player name between forms
            document.querySelectorAll('.player-name-input').forEach(input => {
                input.addEventListener('input', function () {
                    const value = this.value;
                    document.querySelectorAll('.player-name-input').forEach(other => {
                        if (other !== this) {
                            other.value = value;
                        }
                    });
                });
            });

            // Check URL for room code parameter (for shared links)
            const urlParams = new URLSearchParams(window.location.search);
            const roomCode = urlParams.get('code');
            if (roomCode) {
                // Switch to join mode
                document.querySelector('[data-mode="join"]')?.click();
                document.querySelector('.room-code-input').value = roomCode.toUpperCase();
            }
        </script>
    @endpush