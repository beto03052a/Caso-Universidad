<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Campus Connect — Acceso Administrativo">
    <title>Iniciar Sesión — Campus Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0F172A 0%, #1E3A5F 50%, #0F172A 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            width: 100%;
            max-width: 440px;
            padding: 1rem;
        }
        .login-card {
            background: rgba(255,255,255,.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 25px 60px rgba(0,0,0,.4);
        }
        .brand-logo {
            width: 56px; height: 56px;
            background: linear-gradient(135deg, #2563EB, #38BDF8);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin: 0 auto 1rem;
            box-shadow: 0 8px 24px rgba(37,99,235,.4);
        }
        h1.brand-title {
            color: #fff;
            font-size: 1.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: .25rem;
        }
        .brand-sub {
            color: #94A3B8;
            font-size: .82rem;
            text-align: center;
            margin-bottom: 2rem;
        }
        .form-label { color: #CBD5E1; font-size: .82rem; font-weight: 500; }
        .form-control {
            background: rgba(255,255,255,.07);
            border: 1px solid rgba(255,255,255,.12);
            color: #fff;
            border-radius: 10px;
            padding: .65rem 1rem;
            font-size: .9rem;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus {
            background: rgba(255,255,255,.1);
            border-color: #38BDF8;
            box-shadow: 0 0 0 3px rgba(56,189,248,.2);
            color: #fff;
        }
        .form-control::placeholder { color: #64748B; }
        .input-group-text {
            background: rgba(255,255,255,.07);
            border: 1px solid rgba(255,255,255,.12);
            color: #94A3B8;
            border-radius: 10px 0 0 10px;
        }
        .input-group .form-control { border-left: none; border-radius: 0 10px 10px 0; }
        .btn-login {
            background: linear-gradient(135deg, #2563EB, #1D4ED8);
            border: none;
            border-radius: 10px;
            padding: .75rem;
            font-weight: 600;
            font-size: .9rem;
            color: #fff;
            width: 100%;
            transition: all .2s;
            box-shadow: 0 4px 15px rgba(37,99,235,.4);
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #1D4ED8, #1E40AF);
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(37,99,235,.5);
        }
        .invalid-feedback { color: #F87171; }
        .form-check-label { color: #94A3B8; font-size: .82rem; }
        .demo-badge {
            background: rgba(56,189,248,.1);
            border: 1px solid rgba(56,189,248,.2);
            border-radius: 8px;
            padding: .75rem 1rem;
            margin-top: 1.5rem;
        }
        .demo-badge p { color: #94A3B8; font-size: .78rem; margin: 0; }
        .demo-badge code { color: #38BDF8; font-size: .78rem; }
    </style>
</head>
<body>
<div class="login-container">
    <div class="login-card">
        <div class="brand-logo">
            <i class="bi bi-building-fill"></i>
        </div>
        <h1 class="brand-title">Campus Connect</h1>
        <p class="brand-sub">Sistema de Gestión Universitaria · Panel Administrativo</p>

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Correo Institucional</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}"
                        placeholder="usuario@campusconnect.edu"
                        autocomplete="email"
                        required
                        autofocus
                    >
                </div>
                @error('email')
                    <div class="invalid-feedback d-block mt-1">
                        <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        required
                    >
                </div>
                @error('password')
                    <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">
                    <label class="form-check-label" for="remember">Recordar sesión</label>
                </div>
            </div>

            <button type="submit" class="btn-login" id="btn-login">
                <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
            </button>
        </form>

        <div class="demo-badge">
            <p><i class="bi bi-info-circle me-1" style="color:#38BDF8;"></i>Acceso de demostración:</p>
            <p class="mt-1"><strong style="color:#94A3B8;">Administrador:</strong> <code>admin@campusconnect.edu</code> / <code>password</code></p>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
