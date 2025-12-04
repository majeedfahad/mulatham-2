@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11">
                <div class="card questions-card">
                    <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
                        <div>
                            <p class="eyebrow mb-1">لوحة التحكم</p>
                            <h2 class="mb-0">الأسئلة</h2>
                        </div>
                        <a class="btn btn-primary btn-lg create-btn" href="{{route('settings.questions.create')}}">إنشاء سؤال</a>
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table align-middle question-table">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">الصورة</th>
                                        <th scope="col">السؤال</th>
                                        <th scope="col">الحالة</th>
                                        <th scope="col" class="text-center">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($questions as $question)
                                        <tr>
                                            <td data-label="#"> {{$question->id}} </td>
                                            <td data-label="الصورة" style="width: 120px;">
                                                @if($question->image_path)
                                                    <img src="{{ asset('storage/'.$question->image_path) }}" alt="صورة السؤال" class="img-fluid rounded question-thumb">
                                                @else
                                                    <span class="text-muted small">لا توجد صورة</span>
                                                @endif
                                            </td>
                                            <td data-label="السؤال">
                                                <div class="fw-bold mb-1"><a href="{{route('settings.questions.show', ['id' => $question->id])}}" class="question-link" onclick="{{$question->type == 2 ? 'event.preventDefault()' : ''}}">{!! nl2br(e($question->title)) !!}</a></div>
                                                <span class="badge type-badge {{$question->type == 2 ? 'badge-secondary' : 'badge-success'}}">
                                                    {{$question->type == 2 ? 'اختيار من متعدد' : 'نصي'}}
                                                </span>
                                            </td>
                                            <td data-label="الحالة">
                                                @if($question->isActive())
                                                    <span class="badge badge-success status-pill">مفتوح</span>
                                                @else
                                                    <span class="badge badge-warning status-pill">مغلق</span>
                                                @endif
                                            </td>
                                            <td data-label="إجراءات" class="text-center action-col">
                                                <div class="btn-group btn-group-sm d-flex flex-wrap gap-2 justify-content-center">
                                                    @if($question->isActive())
                                                        <a href="{{route('settings.questions.deActiveQuestion', ['id' => $question->id])}}" class="btn btn-outline-success">إغلاق</a>
                                                    @else
                                                        <a href="{{route('settings.questions.activeQuestion', ['id' => $question->id])}}" class="btn btn-outline-warning">فتح</a>
                                                    @endif
                                                    <a href="{{route('settings.questions.destroy', ['id' => $question->id])}}" class="btn btn-outline-danger">حذف</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('styles')
        <style>
            .questions-card {
                border: 1px solid #eadac4;
                border-radius: 18px;
                box-shadow: 0 10px 30px rgba(73, 54, 36, 0.08);
                background: #fffcf7;
            }
            .questions-card .card-header {
                border-bottom: 1px solid #f1e5d4;
                background: #f9f1e7;
            }
            .eyebrow {
                letter-spacing: .08em;
                color: #8a725e;
                font-weight: 700;
                font-size: .85rem;
            }
            .create-btn {
                border-radius: 12px;
                padding-left: 18px;
                padding-right: 18px;
                font-weight: 700;
            }
            .question-table thead th {
                color: #6b4a2f;
                border-bottom: 1px solid #f1e5d4;
                white-space: nowrap;
            }
            .question-table td {
                vertical-align: middle;
            }
            .question-link {
                color: #4b3a2c;
            }
            .question-thumb {
                max-height: 80px;
            }
            .status-pill {
                padding: 6px 12px;
                border-radius: 999px;
                font-weight: 700;
            }
            .btn-outline-warning {
                color: #8a5a1f;
                border-color: #e1b36f;
            }
            .btn-outline-warning:hover {
                color: #fff;
                background-color: #8a5a1f;
                border-color: #8a5a1f;
            }
            .btn-outline-success {
                color: #4c7a4c;
                border-color: #a4c7a4;
            }
            .btn-outline-success:hover {
                color: #fff;
                background-color: #4c7a4c;
                border-color: #4c7a4c;
            }
            .btn-outline-danger {
                color: #9b2b2b;
                border-color: #e5b3b3;
            }
            .btn-outline-danger:hover {
                color: #fff;
                background-color: #9b2b2b;
                border-color: #9b2b2b;
            }
            @media (max-width: 768px) {
                .questions-card {
                    border-radius: 14px;
                }
                .create-btn {
                    width: 100%;
                    margin-top: 12px;
                    font-size: 1rem;
                }
                .question-table thead {
                    display: none;
                }
                .question-table,
                .question-table tbody,
                .question-table tr,
                .question-table td {
                    display: block;
                    width: 100%;
                }
                .question-table tr {
                    margin-bottom: 14px;
                    padding: 12px;
                    border: 1px solid #f1e5d4;
                    border-radius: 14px;
                    background: #fffdf8;
                    box-shadow: 0 8px 18px rgba(73, 54, 36, 0.05);
                }
                .question-table td {
                    padding: 6px 0;
                    text-align: right;
                    position: relative;
                }
                .question-table td::before {
                    content: attr(data-label);
                    font-weight: 700;
                    color: #8a725e;
                    display: block;
                    margin-bottom: 4px;
                    font-size: .9rem;
                }
                .action-col .btn-group {
                    gap: 8px;
                }
                .action-col .btn {
                    width: 48%;
                }
                .question-thumb {
                    max-height: 120px;
                    width: auto;
                }
            }
        </style>
    @endpush
@endsection
