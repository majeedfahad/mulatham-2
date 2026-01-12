@extends('layouts.game')

@section('title', 'النتائج')

@section('content')
<div class="container py-5">
    <!-- Winner Celebration -->
    <div class="text-center mb-5 animate-fade-in">
        <div class="mb-4">
            <i class="bi bi-trophy-fill text-warning" style="font-size: 5rem;"></i>
        </div>
        <h1 class="game-title mb-2">انتهت اللعبة!</h1>
        <p class="game-subtitle mb-4">شكراً للجميع على المشاركة</p>

        @if($winner)
            <div class="game-card game-card-highlight d-inline-block px-5 py-4">
                <div class="text-muted-custom mb-2">الفائز</div>
                <div class="d-flex align-items-center justify-content-center gap-3">
                    <div class="player-avatar" style="width: 64px; height: 64px; font-size: 1.5rem; background: linear-gradient(135deg, #ffd700 0%, #ffed4a 100%); color: #5a4737;">
                        {{ mb_substr($winner->name, 0, 1) }}
                    </div>
                    <div class="text-start">
                        <h3 class="fw-bold mb-0">{{ $winner->name }}</h3>
                        <span class="text-primary-custom fw-bold">{{ $winner->score }} نقطة</span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="row justify-content-center">
        <!-- Leaderboard -->
        <div class="col-lg-8">
            <div class="game-card animate-fade-in" style="animation-delay: 0.1s">
                <div class="game-card-header">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-bar-chart-fill me-2"></i>
                        ترتيب اللاعبين
                    </h5>
                </div>
                <div class="game-card-body">
                    <div class="d-flex flex-column gap-3">
                        @foreach($players as $index => $p)
                            <div class="player-card {{ $index === 0 ? 'is-host' : '' }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="fw-bold fs-4 text-muted-custom" style="width: 32px;">
                                        @if($index === 0)
                                            <i class="bi bi-trophy-fill text-warning"></i>
                                        @elseif($index === 1)
                                            <i class="bi bi-award-fill" style="color: #c0c0c0;"></i>
                                        @elseif($index === 2)
                                            <i class="bi bi-award-fill" style="color: #cd7f32;"></i>
                                        @else
                                            {{ $index + 1 }}
                                        @endif
                                    </div>
                                    <div class="player-avatar">
                                        {{ mb_substr($p->name, 0, 1) }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="fw-bold">{{ $p->name }}</span>
                                            @if($p->id === $player->id)
                                                <span class="badge bg-primary" style="font-size: 0.65rem;">أنت</span>
                                            @endif
                                        </div>
                                        <small class="text-muted-custom">{{ $p->fake_name }}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="fw-bold fs-5 text-primary-custom">{{ $p->score }}</span>
                                        <small class="text-muted-custom d-block">نقطة</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reveal History -->
    @if($reveals->count() > 0)
        <div class="game-card mt-4 animate-fade-in" style="animation-delay: 0.3s">
            <div class="game-card-header">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-clock-history me-2"></i>
                    سجل محاولات الكشف
                </h5>
            </div>
            <div class="game-card-body">
                <div class="table-responsive">
                    <table class="table table-borderless mb-0">
                        <thead>
                            <tr class="text-muted-custom">
                                <th>المحاول</th>
                                <th>الهدف</th>
                                <th>التخمين</th>
                                <th>النتيجة</th>
                                <th>النقاط</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reveals as $reveal)
                                <tr>
                                    <td class="fw-bold">{{ $reveal->guesser->name }}</td>
                                    <td>{{ $reveal->target->fake_name }}</td>
                                    <td>{{ $reveal->guessedPlayer->name }}</td>
                                    <td>
                                        @if($reveal->is_correct)
                                            <span class="status-badge status-badge-ready">
                                                <i class="bi bi-check-circle"></i>
                                                صحيح
                                            </span>
                                        @else
                                            <span class="status-badge status-badge-eliminated">
                                                <i class="bi bi-x-circle"></i>
                                                خطأ
                                            </span>
                                        @endif
                                    </td>
                                    <td class="fw-bold">
                                        @if($reveal->points_transferred > 0)
                                            {{ $reveal->is_correct ? '+' : '-' }}{{ $reveal->points_transferred }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Actions -->
    <div class="text-center mt-5 animate-fade-in" style="animation-delay: 0.4s">
        <a href="{{ route('game.landing') }}" class="btn-game btn-game-primary btn-game-lg">
            <i class="bi bi-arrow-repeat"></i>
            لعب مرة أخرى
        </a>
    </div>

    <!-- Stats -->
    <div class="row justify-content-center mt-5">
        <div class="col-md-8">
            <div class="row g-3 text-center">
                <div class="col-4">
                    <div class="game-card py-4">
                        <div class="fs-2 fw-bold text-primary-custom">{{ $players->count() }}</div>
                        <small class="text-muted-custom">لاعب</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="game-card py-4">
                        <div class="fs-2 fw-bold text-primary-custom">{{ $room->current_question_index }}</div>
                        <small class="text-muted-custom">سؤال</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="game-card py-4">
                        <div class="fs-2 fw-bold text-primary-custom">{{ $reveals->count() }}</div>
                        <small class="text-muted-custom">محاولة كشف</small>
                    </div>
                </div>
            </div>
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
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    .bi-trophy-fill {
        animation: bounce 2s ease-in-out infinite;
    }

    /* Table styling for reveal history */
    .table {
        color: var(--color-text);
        background: transparent;
    }

    .table thead th {
        color: var(--color-muted);
        font-weight: 600;
        border-bottom: 1px solid var(--color-border);
        padding: 12px 16px;
        background: transparent;
    }

    .table tbody tr {
        background: transparent;
    }

    .table tbody td {
        color: var(--color-text);
        padding: 12px 16px;
        border-bottom: 1px solid var(--color-border);
        vertical-align: middle;
        background: transparent;
    }

    .table tbody tr:last-child td {
        border-bottom: none;
    }

    .table-responsive {
        background: transparent;
    }
</style>
@endpush
