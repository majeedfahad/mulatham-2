@extends('layouts.game')

@section('title', 'انضمام للغرفة')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="text-center mb-4 animate-fade-in">
        <img src="{{ asset('imgs/logo-nightsky.svg') }}" alt="ملثم" class="game-logo mb-3" style="width: 100px; height: 100px;">
        <h1 class="game-title mb-2">انضم للغرفة</h1>
        <p class="game-subtitle mb-0">أدخل بياناتك للانضمام</p>
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

    <!-- Room Info -->
    <div class="row justify-content-center mb-4">
        <div class="col-md-6 col-lg-5">
            <div class="game-card animate-fade-in">
                <div class="game-card-body p-3 text-center">
                    <small class="text-muted-custom d-block mb-1">رمز الغرفة</small>
                    <div class="room-code fs-3 mb-2">{{ $room->code }}</div>
                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        <span class="badge bg-secondary">
                            <i class="bi bi-people-fill me-1"></i>
                            {{ $players->count() }} لاعب
                        </span>
                        <span class="badge bg-success">
                            <i class="bi bi-check-circle me-1"></i>
                            {{ $players->where('status', 'ready')->count() }} جاهز
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Join Form -->
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5 animate-fade-in" style="animation-delay: 0.1s">
            <div class="game-card game-card-highlight">
                <div class="game-card-body p-4">
                    <form action="{{ route('game.join') }}" method="POST">
                        @csrf
                        <input type="hidden" name="code" value="{{ $room->code }}">

                        <div class="mb-3">
                            <label class="game-label">
                                <i class="bi bi-person-fill me-1"></i>
                                اسمك الحقيقي
                            </label>
                            <input
                                type="text"
                                name="name"
                                class="game-input"
                                placeholder="أدخل اسمك"
                                value="{{ old('name') }}"
                                required
                                maxlength="50"
                                autocomplete="off"
                                autofocus
                            >
                        </div>

                        <div class="mb-4">
                            <label class="game-label">
                                <i class="bi bi-mask me-1"></i>
                                اسمك المستعار
                                <span class="text-muted-custom fw-normal">(سري)</span>
                            </label>
                            <input
                                type="text"
                                name="fake_name"
                                class="game-input"
                                placeholder="اختر اسماً يخفي هويتك"
                                value="{{ old('fake_name') }}"
                                required
                                maxlength="50"
                                autocomplete="off"
                            >
                            <small class="text-muted-custom mt-1 d-block">
                                <i class="bi bi-info-circle me-1"></i>
                                هذا الاسم سيظهر للآخرين بدلاً من اسمك الحقيقي
                            </small>
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

    <!-- Players Preview -->
    @if($players->count() > 0)
        <div class="row justify-content-center mt-4">
            <div class="col-md-6 col-lg-5 animate-fade-in" style="animation-delay: 0.2s">
                <div class="game-card">
                    <div class="game-card-header py-2">
                        <span class="fw-bold small">
                            <i class="bi bi-people-fill me-1"></i>
                            اللاعبون في الغرفة
                        </span>
                    </div>
                    <div class="game-card-body py-2">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($players as $p)
                                <div class="player-chip {{ $p->status === 'ready' ? 'ready' : 'waiting' }}">
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
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Back Link -->
    <div class="text-center mt-4">
        <a href="{{ route('game.landing') }}" class="btn-game btn-game-outline btn-game-sm">
            <i class="bi bi-arrow-right"></i>
            العودة للرئيسية
        </a>
    </div>
</div>
@endsection

@push('styles')
<style>
    .room-code {
        font-family: 'Courier New', monospace;
        font-weight: 800;
        letter-spacing: 0.2em;
        color: var(--color-primary);
    }

    .player-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        background: var(--color-background);
        border: 1px solid var(--color-border);
    }

    .player-chip.ready {
        border-color: var(--color-success);
        background: rgba(34, 197, 94, 0.1);
    }

    .player-chip .avatar {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: var(--color-primary);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .player-chip .name {
        font-weight: 500;
    }
</style>
@endpush
