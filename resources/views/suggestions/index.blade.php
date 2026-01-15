@extends('layouts.game')

@section('title', 'الاقتراحات والأفكار')

@push('styles')
    <style>
        .suggestions-header {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.15) 0%, rgba(201, 162, 39, 0.1) 100%);
            padding: 40px 0;
            border-bottom: 1px solid var(--color-border);
            margin-bottom: 30px;
        }

        .suggestion-card {
            background: var(--color-card);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            padding: 24px;
            margin-bottom: 16px;
            transition: all 0.3s ease;
        }

        .suggestion-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
            border-color: var(--color-primary);
        }

        .suggestion-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--color-text);
            margin-bottom: 8px;
        }

        .suggestion-description {
            color: var(--color-text-muted);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 16px;
        }

        .suggestion-meta {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .suggestion-author {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--color-text-muted);
            font-size: 0.875rem;
        }

        .category-badge {
            padding: 4px 12px;
            border-radius: var(--radius-full);
            font-size: 0.75rem;
            font-weight: 600;
        }

        .category-feature {
            background: rgba(99, 102, 241, 0.2);
            color: var(--color-secondary-light);
            border: 1px solid rgba(99, 102, 241, 0.3);
        }

        .category-bug {
            background: rgba(239, 68, 68, 0.2);
            color: var(--color-danger-light);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .category-improvement {
            background: rgba(245, 158, 11, 0.2);
            color: var(--color-warning);
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .category-other {
            background: rgba(139, 149, 165, 0.2);
            color: var(--color-text-muted);
            border: 1px solid rgba(139, 149, 165, 0.3);
        }

        .vote-section {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-right: auto;
        }

        .vote-count {
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 700;
            font-size: 1.125rem;
            color: var(--color-primary);
        }

        .vote-btn {
            padding: 8px 16px;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 0.875rem;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .vote-btn-up {
            background: rgba(16, 185, 129, 0.2);
            color: var(--color-success-light);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .vote-btn-up:hover {
            background: rgba(16, 185, 129, 0.3);
            transform: scale(1.05);
        }

        .vote-btn-down {
            background: rgba(239, 68, 68, 0.2);
            color: var(--color-danger-light);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .vote-btn-down:hover {
            background: rgba(239, 68, 68, 0.3);
            transform: scale(1.05);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--color-text-muted);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .rank-badge {
            width: 32px;
            height: 32px;
            border-radius: var(--radius-full);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.875rem;
            flex-shrink: 0;
        }

        .rank-1 {
            background: linear-gradient(135deg, var(--color-gold) 0%, #ffd700 100%);
            color: #1a1a1a;
        }

        .rank-2 {
            background: linear-gradient(135deg, var(--color-silver) 0%, #e8e8e8 100%);
            color: #1a1a1a;
        }

        .rank-3 {
            background: linear-gradient(135deg, #cd7f32 0%, #e8a055 100%);
            color: #1a1a1a;
        }

        .rank-default {
            background: var(--color-border);
            color: var(--color-text-muted);
        }
    </style>
@endpush

@section('content')
    <div class="suggestions-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="h3 mb-1" style="color: var(--color-primary);">
                        <i class="bi bi-lightbulb me-2"></i>
                        الاقتراحات والأفكار
                    </h1>
                    <p class="mb-0 text-muted-custom">شارك أفكارك لتحسين اللعبة وصوّت لاقتراحات الآخرين</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('game.landing') }}" class="btn-game btn-game-secondary btn-game-sm">
                        <i class="bi bi-house"></i>
                        الرئيسية
                    </a>
                    <a href="{{ route('suggestions.create') }}" class="btn-game btn-game-primary btn-game-sm">
                        <i class="bi bi-plus-lg"></i>
                        أضف اقتراح
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="game-alert game-alert-success mb-4 animate-fade-in">
                <i class="bi bi-check-circle-fill"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="game-alert game-alert-danger mb-4 animate-fade-in">
                <i class="bi bi-exclamation-circle-fill"></i>
                {{ session('error') }}
            </div>
        @endif

        @if($suggestions->isEmpty())
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <h4>لا توجد اقتراحات بعد</h4>
                <p>كن أول من يشارك فكرته!</p>
                <a href="{{ route('suggestions.create') }}" class="btn-game btn-game-primary mt-3">
                    <i class="bi bi-plus-lg"></i>
                    أضف أول اقتراح
                </a>
            </div>
        @else
            <div class="row">
                @foreach($suggestions as $index => $suggestion)
                    @php
                        $rank = ($suggestions->currentPage() - 1) * $suggestions->perPage() + $index + 1;
                        $hasVoted = $suggestion->hasVotedBy($sessionId);
                        $categoryClasses = [
                            'feature' => 'category-feature',
                            'bug' => 'category-bug',
                            'improvement' => 'category-improvement',
                            'other' => 'category-other',
                        ];
                        $categoryLabels = [
                            'feature' => 'ميزة جديدة',
                            'bug' => 'خطأ تقني',
                            'improvement' => 'تحسين',
                            'other' => 'أخرى',
                        ];
                    @endphp
                    <div class="col-12 animate-fade-in" style="animation-delay: {{ $index * 0.05 }}s;">
                        <div class="suggestion-card d-flex gap-3 align-items-start">
                            {{-- Rank Badge --}}
                            <div class="rank-badge {{ $rank <= 3 ? 'rank-' . $rank : 'rank-default' }}">
                                {{ $rank }}
                            </div>

                            {{-- Content --}}
                            <div class="flex-grow-1">
                                <div class="suggestion-title">{{ $suggestion->title }}</div>
                                <div class="suggestion-description">{{ $suggestion->description }}</div>
                                <div class="suggestion-meta">
                                    <span class="category-badge {{ $categoryClasses[$suggestion->category] ?? 'category-other' }}">
                                        {{ $categoryLabels[$suggestion->category] ?? $suggestion->category }}
                                    </span>
                                    <span class="suggestion-author">
                                        <i class="bi bi-person-circle"></i>
                                        {{ $suggestion->author_name ?: 'مجهول' }}
                                    </span>
                                    <span class="suggestion-author">
                                        <i class="bi bi-clock"></i>
                                        {{ $suggestion->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>

                            {{-- Vote Section --}}
                            <div class="vote-section">
                                <div class="vote-count">
                                    <i class="bi bi-heart-fill" style="color: var(--color-danger);"></i>
                                    {{ $suggestion->votes_count }}
                                </div>
                                @if($hasVoted)
                                    <form action="{{ route('suggestions.unvote', $suggestion) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="vote-btn vote-btn-down" title="إلغاء التصويت">
                                            <i class="bi bi-heart-fill"></i>
                                            إلغاء
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('suggestions.vote', $suggestion) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="vote-btn vote-btn-up" title="تصويت">
                                            <i class="bi bi-heart"></i>
                                            صوّت
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($suggestions->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $suggestions->links() }}
                </div>
            @endif
        @endif
    </div>
@endsection