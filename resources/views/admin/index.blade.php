@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-9">
                <div class="card admin-hub-card">
                    <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                        <div>
                            <p class="eyebrow mb-1">لوحة التحكم</p>
                            <h1 class="mb-0">الإعدادات</h1>
                            <p class="text-muted mb-0">إدارة الأسئلة والمشاركين وحالة المسابقة</p>
                        </div>
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-md-4 col-sm-12">
                                <a href="{{route('settings.questions.index')}}" class="hub-tile d-block text-center">
                                    <div class="tile-icon">؟</div>
                                    <div class="tile-title">الأسئلة</div>
                                    <div class="tile-sub text-muted">إضافة وتحرير الأسئلة</div>
                                </a>
                            </div>
                            <div class="col-md-4 col-sm-12">
                                <a href="{{route('settings.users')}}" class="hub-tile d-block text-center">
                                    <div class="tile-icon">👥</div>
                                    <div class="tile-title">المتسابقين</div>
                                    <div class="tile-sub text-muted">عرض الدرجات والحالة</div>
                                </a>
                            </div>
                            <div class="col-md-4 col-sm-12">
                                <a href="{{route('settings.admin')}}" class="hub-tile d-block text-center">
                                    <div class="tile-icon">⚙️</div>
                                    <div class="tile-title">إعدادات المسابقة</div>
                                    <div class="tile-sub text-muted">فتح/إغلاق المسابقة</div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('styles')
        <style>
            .admin-hub-card {
                border: 1px solid #eadac4;
                border-radius: 18px;
                box-shadow: 0 10px 30px rgba(73, 54, 36, 0.08);
                background: #fffcf7;
            }
            .admin-hub-card .card-header {
                border-bottom: 1px solid #f1e5d4;
                background: #f9f1e7;
            }
            .eyebrow {
                letter-spacing: .08em;
                color: #8a725e;
                font-weight: 700;
                font-size: .9rem;
            }
            .hub-tile {
                border: 1px solid #f1e5d4;
                border-radius: 14px;
                padding: 18px;
                background: #fffdf8;
                text-decoration: none;
                color: #4b3a2c;
                box-shadow: 0 8px 18px rgba(73, 54, 36, 0.05);
                transition: transform .15s ease, box-shadow .2s ease;
            }
            .hub-tile:hover {
                transform: translateY(-2px);
                box-shadow: 0 12px 24px rgba(73, 54, 36, 0.08);
                color: #4b3a2c;
            }
            .tile-icon {
                font-size: 1.6rem;
                margin-bottom: 8px;
            }
            .tile-title {
                font-weight: 800;
                margin-bottom: 4px;
            }
            .tile-sub {
                font-size: .95rem;
            }
            .row.g-3 {
                margin-left: -8px;
                margin-right: -8px;
            }
            .row.g-3 > [class*="col-"] {
                padding-left: 8px;
                padding-right: 8px;
            }
            @media (max-width: 768px) {
                .admin-hub-card {
                    border-radius: 14px;
                }
                .hub-tile {
                    margin-bottom: 6px;
                }
            }
        </style>
    @endpush
@endsection
