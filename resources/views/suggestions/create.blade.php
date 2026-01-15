@extends('layouts.game')

@section('title', 'أضف اقتراح')

@push('styles')
    <style>
        .feedback-container {
            max-width: 700px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .feedback-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .feedback-icon {
            width: 80px;
            height: 80px;
            border-radius: var(--radius-full);
            background: linear-gradient(135deg, rgba(201, 162, 39, 0.2) 0%, rgba(99, 102, 241, 0.2) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2.5rem;
            color: var(--color-primary);
            border: 2px solid var(--color-border);
        }

        .feedback-form {
            background: var(--color-card);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-xl);
            padding: 32px;
            box-shadow: var(--shadow-lg);
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group:last-child {
            margin-bottom: 0;
        }

        .category-options {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        @media (max-width: 500px) {
            .category-options {
                grid-template-columns: 1fr;
            }
        }

        .category-option {
            position: relative;
        }

        .category-option input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .category-option label {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 16px;
            background: var(--color-background-light);
            border: 2px solid var(--color-border);
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 600;
        }

        .category-option label:hover {
            border-color: var(--color-primary);
            background: rgba(201, 162, 39, 0.1);
        }

        .category-option input:checked+label {
            border-color: var(--color-primary);
            background: linear-gradient(135deg, rgba(201, 162, 39, 0.15) 0%, rgba(201, 162, 39, 0.25) 100%);
            box-shadow: 0 0 20px rgba(201, 162, 39, 0.2);
        }

        .category-option .icon {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.125rem;
        }

        .category-feature .icon {
            background: rgba(99, 102, 241, 0.2);
            color: var(--color-secondary-light);
        }

        .category-bug .icon {
            background: rgba(239, 68, 68, 0.2);
            color: var(--color-danger-light);
        }

        .category-improvement .icon {
            background: rgba(245, 158, 11, 0.2);
            color: var(--color-warning);
        }

        .category-other .icon {
            background: rgba(139, 149, 165, 0.2);
            color: var(--color-text-muted);
        }

        .char-counter {
            font-size: 0.75rem;
            color: var(--color-text-muted);
            text-align: left;
            margin-top: 4px;
        }

        .error-text {
            color: var(--color-danger-light);
            font-size: 0.875rem;
            margin-top: 6px;
        }

        .privacy-note {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: var(--radius-md);
            padding: 16px;
            margin-top: 24px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            color: var(--color-info);
            font-size: 0.875rem;
        }

        .privacy-note i {
            font-size: 1.25rem;
            flex-shrink: 0;
        }
    </style>
@endpush

@section('content')
    <div class="feedback-container">
        <div class="feedback-header">
            <div class="feedback-icon">
                <i class="bi bi-lightbulb"></i>
            </div>
            <h1 class="h3 mb-2" style="color: var(--color-primary);">شاركنا رأيك!</h1>
            <p class="text-muted-custom">ساعدنا في تحسين اللعبة بأفكارك واقتراحاتك</p>
        </div>

        <form action="{{ route('suggestions.store') }}" method="POST" class="feedback-form">
            @csrf

            @if($roomCode)
                <input type="hidden" name="room_code" value="{{ $roomCode }}">
            @endif

            {{-- Category Selection --}}
            <div class="form-group">
                <label class="game-label">نوع الاقتراح</label>
                <div class="category-options">
                    <div class="category-option category-feature">
                        <input type="radio" name="category" id="cat-feature" value="feature" {{ old('category', 'feature') == 'feature' ? 'checked' : '' }} required>
                        <label for="cat-feature">
                            <span class="icon"><i class="bi bi-stars"></i></span>
                            <span>ميزة جديدة</span>
                        </label>
                    </div>
                    <div class="category-option category-improvement">
                        <input type="radio" name="category" id="cat-improvement" value="improvement" {{ old('category') == 'improvement' ? 'checked' : '' }}>
                        <label for="cat-improvement">
                            <span class="icon"><i class="bi bi-arrow-up-circle"></i></span>
                            <span>تحسين</span>
                        </label>
                    </div>
                    <div class="category-option category-bug">
                        <input type="radio" name="category" id="cat-bug" value="bug" {{ old('category') == 'bug' ? 'checked' : '' }}>
                        <label for="cat-bug">
                            <span class="icon"><i class="bi bi-bug"></i></span>
                            <span>خطأ تقني</span>
                        </label>
                    </div>
                    <div class="category-option category-other">
                        <input type="radio" name="category" id="cat-other" value="other" {{ old('category') == 'other' ? 'checked' : '' }}>
                        <label for="cat-other">
                            <span class="icon"><i class="bi bi-chat-dots"></i></span>
                            <span>أخرى</span>
                        </label>
                    </div>
                </div>
                @error('category')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            {{-- Title --}}
            <div class="form-group">
                <label for="title" class="game-label">عنوان الاقتراح</label>
                <input type="text" name="title" id="title" class="game-input" placeholder="مثال: إضافة وضع ليلي للعبة"
                    value="{{ old('title') }}" maxlength="255" required>
                @error('title')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            {{-- Description --}}
            <div class="form-group">
                <label for="description" class="game-label">التفاصيل</label>
                <textarea name="description" id="description" class="game-input" rows="5"
                    placeholder="اشرح فكرتك بالتفصيل..." maxlength="2000" required>{{ old('description') }}</textarea>
                <div class="char-counter">
                    <span id="charCount">0</span> / 2000
                </div>
                @error('description')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            {{-- Author Name (Optional) --}}
            <div class="form-group">
                <label for="author_name" class="game-label">اسمك (اختياري)</label>
                <input type="text" name="author_name" id="author_name" class="game-input"
                    placeholder="سيظهر كـ 'مجهول' إذا تركت الحقل فارغ" value="{{ old('author_name') }}" maxlength="100">
                @error('author_name')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            {{-- Privacy Note --}}
            <div class="privacy-note">
                <i class="bi bi-info-circle"></i>
                <div>
                    <strong>ملاحظة:</strong> سيتم مراجعة اقتراحك قبل ظهوره في لوحة الاقتراحات العامة.
                    نحرص على عرض المحتوى المناسب فقط.
                </div>
            </div>

            {{-- Submit Buttons --}}
            <div class="d-flex gap-3 mt-4 flex-wrap">
                <button type="submit" class="btn-game btn-game-primary flex-grow-1">
                    <i class="bi bi-send"></i>
                    إرسال الاقتراح
                </button>
                <a href="{{ route('suggestions.index') }}" class="btn-game btn-game-secondary">
                    <i class="bi bi-arrow-right"></i>
                    رجوع
                </a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        // Character counter for description
        const descriptionEl = document.getElementById('description');
        const charCountEl = document.getElementById('charCount');

        function updateCharCount() {
            charCountEl.textContent = descriptionEl.value.length;
        }

        descriptionEl.addEventListener('input', updateCharCount);
        updateCharCount();
    </script>
@endpush