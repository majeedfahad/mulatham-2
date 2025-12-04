@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-10">
                <div class="card settings-card">
                    <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
                        <div>
                            <p class="eyebrow mb-1">لوحة التحكم</p>
                            <h2 class="mb-0">إعدادات المسابقة</h2>
                            <p class="text-muted mb-0">فتح أو إغلاق المراحل</p>
                        </div>
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table align-middle settings-table">
                                <thead>
                                    <tr>
                                        <th scope="col">الإعداد</th>
                                        <th scope="col" class="text-center">الأمر</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($settings as $setting)
                                        <tr>
                                            <td data-label="الإعداد">{{$setting->label}}</td>
                                            <td data-label="الأمر" class="text-center">
                                                @if($setting->value == 1)
                                                    <a href="{{route('settings.deActiveSetting', ['id' => $setting->id])}}" class="btn btn-outline-success btn-sm">إغلاق</a>
                                                @else
                                                    <a href="{{route('settings.activeSetting', ['id' => $setting->id])}}" class="btn btn-outline-warning btn-sm">فتح</a>
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
            .settings-card {
                border: 1px solid #eadac4;
                border-radius: 18px;
                box-shadow: 0 10px 30px rgba(73, 54, 36, 0.08);
                background: #fffcf7;
            }
            .settings-card .card-header {
                border-bottom: 1px solid #f1e5d4;
                background: #f9f1e7;
            }
            .eyebrow {
                letter-spacing: .08em;
                color: #8a725e;
                font-weight: 700;
                font-size: .85rem;
            }
            .settings-table thead th {
                color: #6b4a2f;
                border-bottom: 1px solid #f1e5d4;
                white-space: nowrap;
            }
            .settings-table td {
                vertical-align: middle;
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
            @media (max-width: 768px) {
                .settings-card {
                    border-radius: 14px;
                }
                .settings-table thead {
                    display: none;
                }
                .settings-table,
                .settings-table tbody,
                .settings-table tr,
                .settings-table td {
                    display: block;
                    width: 100%;
                }
                .settings-table tr {
                    margin-bottom: 14px;
                    padding: 12px;
                    border: 1px solid #f1e5d4;
                    border-radius: 14px;
                    background: #fffdf8;
                    box-shadow: 0 8px 18px rgba(73, 54, 36, 0.05);
                }
                .settings-table td {
                    padding: 6px 0;
                    text-align: right;
                    position: relative;
                }
                .settings-table td::before {
                    content: attr(data-label);
                    font-weight: 700;
                    color: #8a725e;
                    display: block;
                    margin-bottom: 4px;
                    font-size: .9rem;
                }
                .settings-table .btn {
                    width: 100%;
                }
            }
        </style>
    @endpush
@endsection
