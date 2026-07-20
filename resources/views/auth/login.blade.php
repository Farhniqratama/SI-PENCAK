<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Log In | Sipencak LLDIKTI III</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Sipencak Lldikti Wilayah III" name="description" />
    
    <!-- Theme Config Js -->
    @vite(['resources/js/head.js'])

    <!-- App css -->
    @vite(['resources/scss/app.scss'])

    <!-- Icons css -->
    @vite(['resources/scss/icons.scss'])

    <style>
        :root {
            --primary-blue: #2B79B4;
            --secondary-blue: #3F96CD;
        }

        .techauth-page {
            position: relative;
            min-height: 100vh;
            overflow-x: hidden;
            background-color: #f3f8fc;
            background-image:
                radial-gradient(circle at 16% 12%, rgba(43, 121, 180, 0.2), transparent 28rem),
                radial-gradient(circle at 84% 18%, rgba(63, 150, 205, 0.16), transparent 22rem),
                linear-gradient(120deg, #fbfdff 0%, #f3f8fc 48%, #eef7fc 100%);
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }

        .techauth-page::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background-image:
                linear-gradient(rgba(43, 121, 180, 0.07) 1px, transparent 1px),
                linear-gradient(90deg, rgba(43, 121, 180, 0.07) 1px, transparent 1px);
            background-size: 42px 42px;
            mask-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.65), transparent 72%);
        }

        .techauth-page::after {
            content: "";
            position: fixed;
            top: -18%;
            right: -18%;
            width: 58vw;
            height: 58vw;
            pointer-events: none;
            background: linear-gradient(135deg, rgba(43, 121, 180, 0.22), rgba(63, 150, 205, 0.16));
            clip-path: polygon(22% 0, 100% 16%, 78% 100%, 0 80%);
            transform: rotate(8deg);
        }

        .techauth-shell {
            position: relative;
            z-index: 1;
            width: min(100%, 1040px);
        }

        .techauth-card {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.82);
            border-radius: 0.875rem;
            background: rgba(255, 255, 255, 0.88);
            padding: 1.25rem;
            box-shadow: 0 28px 80px rgba(49, 58, 70, 0.16);
            backdrop-filter: blur(18px);
        }

        .techauth-card::before {
            content: "";
            position: absolute;
            inset: 0 0 auto;
            height: 5px;
            background: linear-gradient(90deg, #76b3d8, var(--secondary-blue) 42%, var(--primary-blue));
        }

        .techauth-left {
            position: relative;
            overflow: hidden;
            border-radius: 0.75rem;
            color: white;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
            min-height: 430px;
            background:
                linear-gradient(145deg, rgba(63, 150, 205, 0.96) 0%, rgba(43, 121, 180, 0.97) 58%, rgba(27, 91, 143, 0.95) 100%),
                url("{{ url('/assets/img/login.png') }}");
            background-position: center, center;
            background-size: cover, cover;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.24);
        }

        .techauth-left::before {
            content: "";
            position: absolute;
            inset: auto -18% -24% -14%;
            height: 48%;
            background: rgba(255, 255, 255, 0.16);
            clip-path: polygon(0 38%, 100% 0, 86% 100%, 12% 88%);
        }

        .techauth-left::after {
            content: "";
            position: absolute;
            top: 1.25rem;
            right: -3.5rem;
            width: 14rem;
            height: 4.5rem;
            background: rgba(118, 179, 216, 0.34);
            clip-path: polygon(12% 0, 100% 22%, 84% 100%, 0 70%);
            transform: rotate(-10deg);
        }

        .techauth-left > * {
            position: relative;
            z-index: 1;
        }

        .techauth-brand {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            border: 1px solid rgba(255, 255, 255, 0.26);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.14);
            padding: 0.45rem 0.8rem;
            letter-spacing: 0.1em;
            font-size: 0.75rem;
        }

        .techauth-left-box {
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 0.75rem;
            background: rgba(27, 91, 143, 0.28);
            padding: 1.25rem;
            box-shadow: 0 18px 44px rgba(49, 58, 70, 0.18);
            backdrop-filter: blur(12px);
        }

        .techauth-form-panel {
            min-height: 430px;
        }

        .techauth-input {
            border-radius: 0.625rem !important;
            padding: 0.7rem 1rem !important;
            font-size: 0.875rem !important;
            border-color: #dce2f1 !important;
            background-color: rgba(255, 255, 255, 0.88) !important;
        }

        .techauth-input:focus {
            border-color: var(--primary-blue) !important;
            box-shadow: 0 0 0 0.2rem rgba(43, 121, 180, 0.14) !important;
        }

        .techauth-password-wrap {
            border-radius: 0.625rem;
            overflow: hidden;
            border-color: #dce2f1 !important;
            background: rgba(255, 255, 255, 0.88);
        }

        .techauth-btn {
            background: linear-gradient(135deg, var(--secondary-blue), var(--primary-blue));
            color: white;
            border-radius: 0.625rem;
            padding: 0.72rem 1.55rem;
            font-size: 0.875rem;
            font-weight: 700;
            border: none;
            box-shadow: 0 12px 24px rgba(43, 121, 180, 0.24);
            transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
        }

        .techauth-btn:hover {
            color: white;
            filter: brightness(0.98);
            transform: translateY(-1px);
            box-shadow: 0 16px 30px rgba(43, 121, 180, 0.3);
        }

        .techauth-link {
            color: var(--primary-blue);
        }

        .techauth-link:hover {
            color: var(--secondary-blue);
        }

        @media (max-width: 991.98px) {
            .techauth-page {
                align-items: flex-start !important;
                padding: 2rem 0;
            }

            .techauth-form-panel {
                min-height: auto;
            }
        }

        @media (max-width: 575.98px) {
            .techauth-page {
                padding: 1rem 0;
            }

            .techauth-card {
                padding: 1rem;
            }

            .techauth-btn {
                width: 100%;
            }
        }
    </style>
</head>

<body class="techauth-page d-flex align-items-center justify-content-center min-vh-100">

    <div class="container techauth-shell">
        <div class="techauth-card">
            <div class="row g-4 align-items-stretch">
                
                <!-- Left Side -->
                <div class="col-lg-5 col-xl-5 d-none d-lg-block">
                    <div class="techauth-left">
                        <div class="mt-2 mb-4">
                            <img src="{{ url('assets/img/sipencak3.png') }}" alt="SIPENCAK" style="max-width: 320px; object-fit: contain; filter: brightness(0) invert(1);">
                        </div>
                        
                        <div>
                            <div class="techauth-left-box">
                                <p class="text-white-50 mb-3" style="font-size: 0.875rem; line-height: 1.5;">
                                    "Memudahkan proses monitoring dan pencairan KIP Kuliah secara real-time yang terintegrasi langsung dengan PDDIKTI."
                                </p>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <img src="{{ url('/assets/img/logo-lldikti3-putih.png') }}" alt="LLDIKTI III" style="height: 40px; object-fit: contain;">
                                    </div>
                                    <div>
                                        <span class="text-white-50" style="font-size: 0.65rem;">
                                            <strong>Lembaga Layanan Pendidikan Tinggi Wilayah III Jakarta</strong>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side (Form) -->
                <div class="col-lg-7 col-xl-7 d-flex align-items-center techauth-form-panel">
                    <div class="w-100 px-md-4 py-3">
                        <div class="mb-4">
                            <h2 class="mb-2 fw-bold" style="font-size: 1.5rem; color: #1f2937;">Masuk Sistem</h2>
                            <p class="text-muted" style="font-size: 0.875rem; font-weight: 500;">Silakan masukkan username dan password Anda untuk melanjutkan.</p>
                        </div>

                        @if(session('error'))
                            <div class="alert alert-danger bg-danger-subtle text-danger border-0 rounded-3 d-flex align-items-center" role="alert">
                                <i class="ri-error-warning-line fs-5 me-2"></i>
                                <div>{!! session('error') !!}</div>
                            </div>
                        @endif

                        <form action="{!! url('login') !!}" method="post" class="mt-4">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label d-block mb-2 text-dark" for="username" style="font-size: 0.875rem; font-weight: 500;">Username</label>
                                <input class="form-control techauth-input" type="text" id="username" name="username" placeholder="Masukkan Username" required>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <label class="form-label mb-0 text-dark" for="password" style="font-size: 0.875rem; font-weight: 500;">Password</label>
                                    <a href="#" class="text-muted text-decoration-none" style="font-size: 0.875rem; font-weight: 500;">Lupa password?</a>
                                </div>
                                <div class="input-group input-group-merge border techauth-password-wrap">
                                    <input class="form-control techauth-input border-0 shadow-none" type="password" id="password" name="password" placeholder="Masukkan Password" required>
                                    <div class="input-group-text cursor-pointer bg-transparent border-0" data-password="false">
                                        <span class="password-eye"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mt-4 pt-2">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="techauth-btn">Masuk Ke Sistem</button>
                                    <a href="{{ route('public.home') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="border-radius: 0.625rem; padding: 0.72rem 1.2rem; font-weight: 700; border-color: #dce2f1; color: #6B7C8F; text-decoration: none;">
                                        <i class="ri-home-4-line me-1"></i> Kembali
                                    </a>
                                </div>
                                <p class="text-muted mb-0" style="font-size: 0.875rem;">Kendala Akses? <a href="#" class="text-decoration-underline techauth-link ms-1">Hubungi Admin</a></p>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- App Js -->
    @vite(['resources/js/app.js'])
</body>
</html>
