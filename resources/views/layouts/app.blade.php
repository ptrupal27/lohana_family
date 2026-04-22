<!DOCTYPE html>
<html lang="gu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SGLM MEMBER MANAGMENT</title>
    <link rel="icon" href="{{ asset('images/logo.jpeg') }}" type="image/jpeg">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Gujarati:wght@400;700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 sidebar offcanvas-md offcanvas-start p-0" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
            <div class="offcanvas-header d-md-none bg-dark-maroon text-white p-3 border-bottom border-gold">
                <h5 class="offcanvas-title fw-bold" id="sidebarMenuLabel">મેનૂ</h5>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu" aria-label="Close"></button>
            </div>

            <div class="offcanvas-body d-flex flex-column h-100">
                <div class="px-3 mb-4 mt-3">
                    <div class="text-white fw-bold">નમસ્તે, {{ Auth::user()->name }}</div>
                    <hr class="text-white-50 opacity-25">
                </div>
                <div class="flex-grow-1 overflow-y-auto">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2 me-2"></i> ડેશબોર્ડ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('members.*') ? 'active' : '' }}" href="{{ route('members.index') }}">
                                <i class="bi bi-people me-2"></i> સભ્યો
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="mt-auto px-3 pb-4 pt-3 border-top border-white-10">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start text-white-50 p-0">
                            <i class="bi bi-box-arrow-right me-2"></i> લોગ આઉટ
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 p-0 d-flex flex-column min-vh-100">
            <header class="navbar sticky-top bg-white flex-md-nowrap p-2 shadow-sm border-bottom">

                <div class="d-flex align-items-center justify-content-between w-100 px-3">
                    <div class="d-flex align-items-center gap-2 gap-md-3">
                        <img src="{{ asset('images/logo.jpeg') }}" alt="Lohana Logo" style="height: 40px; width: auto;" class="rounded shadow-sm">
                        <h6 class="mb-0 text-maroon fw-bold d-md-none" style="font-size: 0.9rem;">SGLM MAHJAN - SURAT</h6>
                        <h5 class="mb-0 text-maroon fw-bold d-none d-md-block">શ્રી ઘોઘારી લોહાણા મહાજન - સુરત</h5>
                    </div>
                   
                    <button class="navbar-toggler d-md-none border-0 p-1" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-label="Toggle navigation">
                        <i class="bi bi-list fs-3 text-maroon"></i>
                    </button>

                </div>
            </header>


            <div class="main-content flex-grow-1">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show animate__animated animate__fadeInDown" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show animate__animated animate__shakeX" role="alert">
                        <h5 class="fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i> કૃપા કરીને નીચેની ભૂલો સુધારો:</h5>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>

            <!-- Copyright Footer -->
            <footer class="footer py-3 bg-white border-top mt-auto">
                <div class="container-fluid px-4">
                    <div class="row align-items-center">
                        <div class="col-md-6 text-center text-md-start">
                            <p class="mb-0 text-muted footer-text">
                                &copy; {{ date('Y') }} <span class="text-maroon fw-bold">શ્રી ઘોઘારી લોહાણા મહાજન - સુરત</span>. સર્વ હક સ્વાધીન.
                            </p>
                        </div>
                        <div class="col-md-6 text-center text-md-end mt-2 mt-md-0">
                            <span class="text-muted small footer-tagline">
                                Developed by <a href="https://ZiUinfotech.in/" target="_blank" class="text-maroon fw-bold text-decoration-none">ZiU INFOTECH</a>
                            </span>
                        </div>
                    </div>
                </div>
            </footer>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')
</body>
</html>
