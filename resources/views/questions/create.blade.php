@extends('layouts.app')

@section('content')
    <div class="question-create-hero text-right">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 col-md-9">
                    <p class="eyebrow mb-2">لوحة التحكم</p>
                    <h1 class="mb-3">إنشاء سؤال جديد</h1>
                </div>
            </div>
        </div>
        <div class="pattern"></div>
    </div>

    <div class="container text-right">
        <div class="row justify-content-center">
            <div class="col-xl-9 col-lg-10">
                <div class="card shadow-sm question-card">
                    <div class="card-body p-4 p-md-5">
                        @if (session('status'))
                            <div class="alert alert-success mb-4" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form action="{{route('settings.questions.store')}}" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="section-label d-flex align-items-center mb-2">
                                        <span class="badge step-badge">1</span>
                                        <span class="label-text">اكتب السؤال</span>
                                    </label>
                                    <textarea name="question" class="form-control form-control-lg rounded-3" rows="4" placeholder="مثال: ما هي عاصمة المملكة العربية السعودية؟" required></textarea>
                                </div>

                                <div class="col-md-12">
                                    <label class="section-label d-flex align-items-center mb-2">
                                        <span class="badge step-badge">1.1</span>
                                        <span class="label-text">أرفق صورة (اختياري)</span>
                                    </label>
                                    <div class="upload-tile">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div class="d-flex align-items-center gap-2 mb-2 mb-md-0">
                                                <div class="upload-icon"><i class="bi bi-image"></i></div>
                                                <div>
                                                    <div class="fw-semibold">صورة توضيحية للسؤال</div>
                                                    <div class="text-muted small">يدعم PNG, JPG حتى 3MB</div>
                                                </div>
                                            </div>
                                            <label class="btn btn-outline-primary btn-sm mb-0">
                                                اختيار ملف
                                                <input type="file" name="question_image" id="question_image" accept="image/*" class="d-none" onchange="previewQuestionImage(this)">
                                            </label>
                                        </div>
                                        <div class="preview mt-3" id="question-image-preview" style="display: none;">
                                            <img src="" alt="معاينة صورة السؤال" class="img-fluid rounded-3 border">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="section-label d-flex align-items-center mb-2">
                                        <span class="badge step-badge">2</span>
                                        <span class="label-text">نوع الإجابة</span>
                                    </label>
                                    <div class="input-group input-group-lg">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text icon-pill">
                                                <i class="bi bi-ui-checks-grid"></i>
                                            </span>
                                        </div>
                                        <select name="answer_option" id="answer_option" class="form-select form-select-lg" onchange="showChoices(this)" required>
                                            <option value="" disabled selected>اختر نوع الإجابة</option>
                                            <option value="1">نصي</option>
                                            <option value="2">خيارات متعددة</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="section-label d-flex align-items-center mb-2">
                                        <span class="badge step-badge">3</span>
                                        <span class="label-text">الدرجة</span>
                                    </label>
                                    <div class="input-group input-group-lg">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text icon-pill">
                                                <i class="bi bi-stars"></i>
                                            </span>
                                        </div>
                                        <input type="number" name="score" class="form-control form-control-lg" min="1" placeholder="مثال: 5" required>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <label class="section-label d-flex align-items-center mb-2">
                                        <span class="badge step-badge">4</span>
                                        <span class="label-text">خيارات الإجابة (عند اختيار خيارات متعددة)</span>
                                    </label>
                                    <div class="multiple-choice disabled" id="choices-div">
                                        <div class="row g-3">
                                            @foreach(range(1,4) as $choice)
                                                <div class="col-md-6">
                                                    <div class="choice-tile">
                                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                                            <label class="mb-0 fw-semibold" for="choice-{{$choice}}">الخيار {{$choice}}</label>
                                                            <div class="form-check mb-0">
                                                                <input class="form-check-input" type="radio" name="correct_answer" id="correct-{{$choice}}" value="{{$choice}}">
                                                                <label class="form-check-label small text-muted" for="correct-{{$choice}}">الإجابة الصحيحة</label>
                                                            </div>
                                                        </div>
                                                        <input type="text" name="{{$choice}}" id="choice-{{$choice}}" class="form-control form-control-lg" placeholder="اكتب الخيار {{$choice}}">
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 d-flex gap-3 flex-wrap justify-content-between align-items-center">
                                <div class="d-flex align-items-center text-muted small gap-2">
                                    <i class="bi bi-shield-check text-success"></i>
                                    <span>تأكد من إكمال الحقول قبل الإرسال</span>
                                </div>
                                <div class="d-flex gap-2 btn-stack">
                                    <button type="reset" class="btn btn-outline-secondary btn-lg px-4">إعادة تعيين</button>
                                    <button type="submit" class="btn btn-primary btn-lg px-4">إنشاء السؤال</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            function showChoices(evt) {
                if(evt.value == 2) {
                    $('#choices-div').removeClass('disabled');
                } else {
                    $('#choices-div').addClass('disabled');
                }
            }
            function previewQuestionImage(input) {
                var preview = document.getElementById('question-image-preview');
                var img = preview ? preview.querySelector('img') : null;
                if (!input.files || !input.files[0] || !preview || !img) {
                    return;
                }
                var reader = new FileReader();
                reader.onload = function (e) {
                    img.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
            document.addEventListener('DOMContentLoaded', function () {
                var selector = document.getElementById('answer_option');
                if (selector) {
                    showChoices(selector);
                }
            });
        </script>
    @endpush
    @push('styles')
        <style>
            .question-create-hero {
                position: relative;
                padding: 48px 0 32px;
                background: radial-gradient(circle at 10% 20%, rgba(25, 135, 84, 0.25), transparent 35%), linear-gradient(120deg, #f3f7ff 0%, #e9f5ff 60%, #f8fbff 100%);
                overflow: hidden;
            }
            .question-create-hero .pattern {
                position: absolute;
                inset: 0;
                background-image: linear-gradient(135deg, rgba(0, 0, 0, 0.03) 25%, transparent 25%), linear-gradient(225deg, rgba(0, 0, 0, 0.03) 25%, transparent 25%), linear-gradient(45deg, rgba(0, 0, 0, 0.03) 25%, transparent 25%), linear-gradient(315deg, rgba(0, 0, 0, 0.03) 25%, #f8fbff 25%);
                background-size: 20px 20px;
                opacity: 0.7;
                pointer-events: none;
            }
            .question-create-hero .eyebrow {
                letter-spacing: .05em;
                color: #0d6efd;
                font-weight: 600;
            }
            .question-create-hero h1 {
                font-weight: 800;
            }
            .question-create-hero .container {
                position: relative;
                z-index: 1;
            }
            .question-card {
                margin-top: -40px;
                border: none;
                border-radius: 18px;
            }
            .section-label {
                font-weight: 700;
                color: #0b2f4a;
            }
            .step-badge {
                background: #0d6efd;
                color: #fff;
                border-radius: 50%;
                width: 36px;
                height: 36px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }
            .icon-pill {
                background: #f7f9fc;
                border-right: 0;
                font-size: 18px;
            }
            .icon-pill i {
                font-style: normal;
            }
            .multiple-choice.disabled {
                opacity: 0.35;
                pointer-events: none;
                filter: grayscale(0.2);
            }
            .choice-tile {
                border: 1px solid #e5ecf6;
                border-radius: 14px;
                padding: 14px;
                background: #fdfefe;
                transition: all .2s ease;
            }
            .choice-tile:hover {
                box-shadow: 0 8px 18px rgba(13, 110, 253, 0.08);
                transform: translateY(-2px);
            }
            .upload-tile {
                border: 1px dashed #c6d6f3;
                border-radius: 14px;
                padding: 16px;
                background: #f9fbff;
            }
            .upload-icon {
                width: 40px;
                height: 40px;
                border-radius: 10px;
                background: #e8f1ff;
                color: #0d6efd;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
            }
            .input-group .form-select,
            .input-group .form-control {
                border-top-left-radius: 0;
                border-bottom-left-radius: 0;
            }
            .input-group-prepend .input-group-text {
                border-top-right-radius: .35rem;
                border-bottom-right-radius: .35rem;
            }
            .form-select {
                display: block;
                width: 100%;
                padding: .75rem 1rem;
                font-size: 1rem;
                line-height: 1.5;
                color: #495057;
                background-color: #fff;
                border: 1px solid #ced4da;
                border-radius: .35rem;
                -webkit-appearance: none;
                -moz-appearance: none;
                appearance: none;
            }
            .form-select-lg {
                padding: .85rem 1.1rem;
            }
            .rounded-3 {
                border-radius: 1rem !important;
            }
            .row.g-3 {
                margin-left: -10px;
                margin-right: -10px;
            }
            .row.g-3 > [class*="col-"] {
                padding-left: 10px;
                padding-right: 10px;
                margin-bottom: 14px;
            }
            .gap-2 { gap: .75rem; }
            .gap-3 { gap: 1.25rem; }
            .label-text {
                margin-right: 10px;
                font-weight: 700;
            }
            .btn-stack > * + * {
                margin-right: .5rem;
                margin-left: .5rem;
            }
            .bi-ui-checks-grid::before { content: "☑"; }
            .bi-stars::before { content: "★"; }
            .bi-shield-check::before { content: "🛡"; }
            .bi-image::before { content: "🖼"; }
        </style>
    @endpush
@endsection
