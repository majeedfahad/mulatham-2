@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11">
                <div class="card users-card">
                    <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
                        <div>
                            <p class="eyebrow mb-1">لوحة التحكم</p>
                            <h2 class="mb-0">المتسابقين</h2>
                            <p class="text-muted mb-0">عرض النقاط والحالة الحالية</p>
                        </div>
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table align-middle users-table">
                                <thead>
                                    <tr>
                                        <th scope="col">الاسم</th>
                                        <th scope="col">اللقب</th>
                                        <th scope="col">المجموع</th>
                                        <th scope="col">الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr class="{{$user->status == 1 ? 'user-active' : ''}}">
                                            <td data-label="الاسم">{{$user->name}}</td>
                                            <td data-label="اللقب">{{$user->fakename}}</td>
                                            <td data-label="المجموع">{{$user->getTotalScore()}}</td>
                                            <td data-label="الحالة">
                                                @if($user->status == 1)
                                                    <span class="badge badge-success status-pill">مستمر</span>
                                                @else
                                                    <span class="badge badge-secondary status-pill">منسحب</span>
                                                @endif
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
            .users-card {
                border: 1px solid #eadac4;
                border-radius: 18px;
                box-shadow: 0 10px 30px rgba(73, 54, 36, 0.08);
                background: #fffcf7;
            }
            .users-card .card-header {
                border-bottom: 1px solid #f1e5d4;
                background: #f9f1e7;
            }
            .eyebrow {
                letter-spacing: .08em;
                color: #8a725e;
                font-weight: 700;
                font-size: .85rem;
            }
            .users-table thead th {
                color: #6b4a2f;
                border-bottom: 1px solid #f1e5d4;
                white-space: nowrap;
            }
            .users-table td {
                vertical-align: middle;
            }
            .user-active {
                background: linear-gradient(90deg, rgba(124, 189, 124, 0.08), rgba(124, 189, 124, 0.02));
            }
            .status-pill {
                padding: 6px 12px;
                border-radius: 999px;
                font-weight: 700;
            }
            @media (max-width: 768px) {
                .users-card {
                    border-radius: 14px;
                }
                .users-table thead {
                    display: none;
                }
                .users-table,
                .users-table tbody,
                .users-table tr,
                .users-table td {
                    display: block;
                    width: 100%;
                }
                .users-table tr {
                    margin-bottom: 14px;
                    padding: 12px;
                    border: 1px solid #f1e5d4;
                    border-radius: 14px;
                    background: #fffdf8;
                    box-shadow: 0 8px 18px rgba(73, 54, 36, 0.05);
                }
                .users-table td {
                    padding: 6px 0;
                    text-align: right;
                    position: relative;
                }
                .users-table td::before {
                    content: attr(data-label);
                    font-weight: 700;
                    color: #8a725e;
                    display: block;
                    margin-bottom: 4px;
                    font-size: .9rem;
                }
            }
        </style>
    @endpush
@endsection
