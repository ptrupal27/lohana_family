<!DOCTYPE html>
<html lang="gu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>લોગિન - ટ્રસ્ટ મેનેજમેન્ટ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Gujarati:wght@400;700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-maroon: #800000;
            --dark-maroon: #4d0000;
            --gold-accent: #ffd700;
        }
        body {
            background: linear-gradient(135deg, #fcfaf2 0%, #f5e6d3 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Noto Sans Gujarati', 'Poppins', sans-serif;
            margin: 0;
            overflow: hidden;
        }
        .login-container {
            width: 100%;
            max-width: 450px;
            perspective: 1000px;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(128, 0, 0, 0.15);
            border: 1px solid rgba(128, 0, 0, 0.05);
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }
        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-maroon), var(--gold-accent), var(--primary-maroon));
        }
        .login-title {
            text-align: center;
            margin-bottom: 35px;
            font-weight: 700;
            color: var(--primary-maroon);
            letter-spacing: 0.5px;
        }
        .login-title small {
            display: block;
            font-size: 0.9rem;
            color: #666;
            margin-top: 5px;
            font-weight: 400;
        }
        .form-label {
            font-weight: 600;
            color: #444;
            font-size: 0.9rem;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1.5px solid #eee;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: var(--primary-maroon);
            box-shadow: 0 0 0 0.25rem rgba(128, 0, 0, 0.1);
        }
        .btn-primary {
            background: var(--primary-maroon);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 700;
            transition: all 0.3s;
            margin-top: 10px;
        }
        .btn-primary:hover {
            background: var(--dark-maroon);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(128, 0, 0, 0.3);
        }
        .decorative-icon {
            text-align: center;
            color: var(--gold-accent);
            font-size: 3rem;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-card">
        <div class="decorative-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" fill="currentColor" class="bi bi-shield-check" viewBox="0 0 16 16">
                <path d="M8 0c-.69 0-1.843.265-2.928.56-1.11.3-2.229.655-2.887.87a1.54 1.54 0 0 0-1.044 1.262c-.596 4.477.787 7.795 2.465 9.99a11.775 11.775 0 0 0 2.517 2.453c.386.273.744.482 1.048.625.28.132.581.24.829.24s.548-.108.829-.24a7.158 7.158 0 0 0 1.048-.625 11.777 11.777 0 0 0 2.517-2.453c1.678-2.195 3.061-5.513 2.465-9.99a1.541 1.541 0 0 0-1.044-1.263 62.467 62.467 0 0 0-2.887-.87C9.843.266 8.69 0 8 0zm0 1.415c.665 0 1.737.272 2.807.567.879.243 1.83.541 2.37.72.184.06.33.17.432.307.1.137.157.287.168.455.485 3.644-.51 6.394-1.921 8.24a10.776 10.776 0 0 1-2.28 2.235c-.322.228-.609.393-.82.492a1.239 1.239 0 0 1-.368.122V1.415z"/>
                <path d="M10.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0z"/>
            </svg>
        </div>
        <h2 class="login-title">
            ટ્રસ્ટ મેનેજમેન્ટ
            <small>એડમિન પોર્ટલ લોગિન</small>
        </h2>
        
        <form action="/login" method="POST">
            @csrf
            
            <div class="mb-4">
                <label for="email" class="form-label">ઈમેઈલ એડ્રેસ</label>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="admin@example.com" required autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="password" class="form-label">પાસવર્ડ</label>
                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label small text-muted" for="remember">મને યાદ રાખો</label>
            </div>
            
        <button type="submit" class="btn btn-primary w-100 py-3">લોગિન કરો</button>
        </form>
    </div>
    <div class="mt-4 text-center text-muted small animate__animated animate__fadeInUp" style="animation-delay: 0.5s;">
        <p class="mb-0">&copy; {{ date('Y') }} <span class="fw-bold">શ્રી ઘોઘારી લોહાણા મહાજન - સુરત</span></p>
        <p class="mb-0 opacity-75">સર્વ હક સ્વાધીન. | Developed by <a href="https://ziuinfotech.in/" target="_blank" class="text-maroon fw-bold text-decoration-none">ZIU INFOTECH</a></p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
