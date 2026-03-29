<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول — معصرة ربوع الثمرة الحديث</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --olive-dark: #1e3a0f;
            --olive-main: #3d6b1a;
            --olive-light:#6aab30;
            --olive-pale: #eaf5d5;
            --gold:       #c8952a;
            --gold-light: #f0c84a;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Cairo', sans-serif;
            min-height: 100vh;
            background: linear-gradient(160deg, #1a3009 0%, #2d5a14 50%, #1a3009 100%);
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            padding: 1rem;
        }

        .auth-logo {
            text-align: center; margin-bottom: 1.5rem; color: #fff;
        }
        .auth-logo .olive-icon { font-size: 3.5rem; display: block; margin-bottom: .4rem; }
        .auth-logo .press-name {
            font-size: 1.4rem; font-weight: 900; color: var(--gold-light);
            line-height: 1.2;
        }
        .auth-logo .press-phones {
            font-size: .8rem; color: rgba(255,255,255,.65); margin-top: 4px; direction: ltr;
        }

        .auth-card {
            background: #fff;
            border-radius: 18px;
            padding: 2rem 2.2rem;
            width: 100%; max-width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,.4);
        }
        .auth-card h5 {
            font-weight: 800; color: var(--olive-dark);
            font-size: 1.2rem; text-align: center;
            margin-bottom: 1.5rem;
            padding-bottom: .75rem;
            border-bottom: 2px solid var(--olive-pale);
        }
        .form-label { font-weight: 700; color: var(--olive-dark); font-size: .88rem; }
        .form-control {
            border-radius: 9px; border: 1.5px solid #dde3d5;
            padding: .65rem 1rem; font-family: 'Cairo', sans-serif;
        }
        .form-control:focus {
            border-color: var(--olive-light);
            box-shadow: 0 0 0 3px rgba(106,171,48,.18);
        }
        .btn-login {
            background: linear-gradient(135deg, var(--olive-main), var(--olive-dark));
            color: #fff; border: none; border-radius: 10px;
            font-weight: 800; font-size: 1rem; padding: .75rem;
            width: 100%; transition: opacity .2s;
        }
        .btn-login:hover { opacity: .9; color: #fff; }
        .auth-footer {
            text-align: center; margin-top: 1rem; font-size: .88rem;
        }
        .auth-footer a { color: var(--olive-main); font-weight: 700; text-decoration: none; }
        .auth-footer a:hover { text-decoration: underline; }
        .divider {
            text-align: center; position: relative; margin: 1.2rem 0;
            color: #aaa; font-size: .8rem;
        }
        .divider::before, .divider::after {
            content: ''; position: absolute; top: 50%;
            width: 40%; height: 1px; background: #e0e0e0;
        }
        .divider::before { right: 0; }
        .divider::after  { left: 0; }
    </style>
</head>
<body>

    <div class="auth-logo">
        <span class="olive-icon">🫒</span>
        <div class="press-name">معصرة ربوع الثمرة الحديث</div>
        <div class="press-phones">0785099961 / 0785365419 / 0785766009</div>
    </div>

    <div class="auth-card">
        <h5><i class="fas fa-sign-in-alt me-2" style="color:var(--olive-main)"></i>تسجيل الدخول</h5>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">البريد الإلكتروني</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" placeholder="example@email.com" required autofocus>
                </div>
                @error('email')
                    <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">كلمة المرور</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                           placeholder="••••••••" required>
                </div>
                @error('password')
                    <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3 d-flex align-items-center justify-content-between">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label small" for="remember">تذكرني</label>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt me-2"></i>دخول
            </button>
        </form>

        <div class="divider">أو</div>

        <div class="auth-footer">
            ليس لديك حساب؟
            <a href="{{ route('register') }}">إنشاء حساب جديد</a>
        </div>
    </div>

</body>
</html>
