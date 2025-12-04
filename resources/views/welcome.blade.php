@extends('layouts.app')


@section('content')
    <div class="welcome-hero text-center">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <img src="imgs/logo-desert.svg" alt="سفرة حايل" class="hero-logo mb-3">
            <h1 class="hero-title mb-1">سفرة حايل</h1>
            <p class="hero-sub mb-4">هـ ١٤٤٧</p>
            <p class="hero-text">ورنا غموضك وفز بلقب فارس حايل الملثم.</p>
            <div class="hero-actions d-flex flex-column flex-sm-row justify-content-center gap-3 mt-4">
                <a href="{{route('login')}}" class="btn btn-primary hero-btn px-4 py-2">ادخل</a>
                <a href="{{route('register')}}" class="btn btn-outline-secondary hero-btn px-4 py-2">سو حساب</a>
            </div>
        </div>
    </div>
    @push('styles')
        <style>
            .welcome-hero {
                position: relative;
                min-height: calc(100vh - 120px);
                display: flex;
                align-items: center;
                justify-content: center;
                text-align: center;
                padding: 32px 16px;
                overflow: hidden;
                background: radial-gradient(circle at 15% 20%, rgba(191, 147, 92, 0.08), transparent 22%), radial-gradient(circle at 80% 10%, rgba(83, 120, 156, 0.08), transparent 20%), linear-gradient(180deg, #f9f3e9 0%, #f3e7d7 50%, #f7f0e5 100%);
            }
            .hero-overlay {
                position: absolute;
                inset: 0;
                background-image: url('imgs/logo-desert.svg');
                background-repeat: no-repeat;
                background-position: center 70%;
                background-size: 70%;
                opacity: 0.04;
                pointer-events: none;
            }
            .hero-content {
                position: relative;
                max-width: 540px;
                background: rgba(255, 252, 247, 0.9);
                border: 1px solid #eadac4;
                border-radius: 18px;
                padding: 28px 24px;
                box-shadow: 0 12px 30px rgba(73, 54, 36, 0.08);
            }
            .hero-logo {
                height: 120px;
                filter: drop-shadow(0 6px 12px rgba(58, 46, 36, 0.12));
            }
            .hero-title {
                font-weight: 800;
                color: #5a4737;
            }
            .hero-sub {
                color: #8a725e;
                font-weight: 700;
                letter-spacing: 0.08em;
            }
            .hero-text {
                color: #4b3a2c;
                font-size: 1.05rem;
                margin-bottom: 0;
            }
            .hero-btn {
                border-radius: 12px;
                font-weight: 700;
                min-width: 180px;
            }
            .btn-outline-secondary {
                color: #6b4a2f;
                border-color: #d9b180;
                background-color: transparent;
            }
            .btn-outline-secondary:hover {
                color: #fff;
                background-color: #6b4a2f;
                border-color: #6b4a2f;
            }
            .gap-3 { gap: 12px; }
            @media (max-width: 576px) {
                .hero-logo {
                    height: 100px;
                }
                .hero-content {
                    padding: 22px 18px;
                }
                .hero-title {
                    font-size: 1.4rem;
                }
                .hero-sub {
                    font-size: 1.1rem;
                }
                .hero-text {
                    font-size: 0.98rem;
                }
                .hero-btn {
                    width: 100%;
                }
            }
        </style>
    @endpush
@endsection
