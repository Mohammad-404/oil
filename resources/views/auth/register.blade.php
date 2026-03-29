<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب — معصرة ربوع الثمرة الحديث</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --olive-dark: #1e3a0f;
            --olive-main: #3d6b1a;
            --olive-light:#6aab30;
            --olive-pale: #eaf5d5;
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
        .auth-logo .olive-icon { font-size: 3rem; display: block; margin-bottom: .4rem; }
        .auth-logo .press-name { font-size: 1.3rem; font-weight: 900; color: var(--gold-light); }
        .auth-card {
            background: #fff; border-radius: 18px; padding: 2rem 2.2rem;
            width: 100%; max-width: 440px;
            box-shadow: 0 20px 60px rgba(0,0,0,.4);
        }
        .auth-card h5 {
            font-weight: 800; color: var(--olive-dark); font-size: 1.2rem;
            text-align: center; margin-bottom: 1.5rem;
            padding-bottom: .75rem; border-bottom: 2px solid var(--olive-pale);
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
        .btn-register {
            background: linear-gradient(135deg, var(--olive-main), var(--olive-dark));
            color: #fff; border: none; border-radius: 10px;
            font-weight: 800; font-size: 1rem; padding: .75rem;
            width: 100%; transition: opacity .2s;
        }
        .btn-register:hover { opacity: .9; color: #fff; }
        .auth-footer { text-align: center; margin-top: 1rem; font-size: .88rem; }
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
    </div>

    <div class="auth-card">
        <h5><i class="fas fa-user-plus me-2" style="color:var(--olive-main)"></i>إنشاء حساب جديد</h5>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">الاسم الكامل</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" placeholder="اسمك الكامل" required autofocus>
                </div>
                @error('name')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">البريد الإلكتروني</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" placeholder="example@email.com" required>
                </div>
                @error('email')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">كلمة المرور</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                           placeholder="8 أحرف على الأقل" required>
                </div>
                @error('password')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label">تأكيد كلمة المرور</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password_confirmation" class="form-control"
                           placeholder="أعد كتابة كلمة المرور" required>
                </div>
            </div>

            <button type="submit" class="btn-register">
                <i class="fas fa-user-plus me-2"></i>إنشاء الحساب
            </button>
        </form>

        <div class="divider">أو</div>

        <div class="auth-footer">
            لديك حساب بالفعل؟
            <a href="{{ route('login') }}">تسجيل الدخول</a>
        </div>
    </div>

</body>
</html>
