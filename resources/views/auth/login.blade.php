<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول — {{ $pressName }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --olive-dark:  #1a3309;
            --olive-main:  #2d5a14;
            --olive-mid:   #3d6b1a;
            --olive-light: #6aab30;
            --olive-pale:  #eaf5d5;
            --gold:        #c8952a;
            --gold-light:  #f0c84a;
            --cream:       #fdfcf5;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Cairo', sans-serif;
            min-height: 100vh;
            display: flex;
            background: var(--cream);
        }

        /* ===== الجانب الأيمن — هوية المعصرة ===== */
        .brand-side {
            width: 48%;
            background: linear-gradient(170deg, #0f2005 0%, #1e3d0a 40%, #2d5a14 75%, #1a3309 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 2rem;
            position: relative;
            overflow: hidden;
        }

        /* دوائر زخرفية خلفية */
        .brand-side::before {
            content: '';
            position: absolute;
            width: 420px; height: 420px;
            border-radius: 50%;
            border: 1px solid rgba(200,149,42,.15);
            top: -80px; right: -80px;
        }
        .brand-side::after {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            border-radius: 50%;
            border: 1px solid rgba(200,149,42,.1);
            bottom: -60px; left: -60px;
        }

        /* إطار الشعار الدائري */
        .logo-frame {
            position: relative;
            width: 220px; height: 220px;
            border-radius: 50%;
            padding: 6px;
            background: linear-gradient(135deg, var(--gold), #8b6318, var(--gold-light), #8b6318);
            box-shadow: 0 0 0 1px rgba(200,149,42,.3), 0 8px 40px rgba(0,0,0,.5);
            margin-bottom: 2rem;
            z-index: 1;
        }
        .logo-frame-inner {
            width: 100%; height: 100%;
            border-radius: 50%;
            overflow: hidden;
            background: #0f2005;
            display: flex; align-items: center; justify-content: center;
        }
        .logo-frame-inner img {
            width: 100%; height: 100%;
            object-fit: cover;
            border-radius: 50%;
            filter: contrast(1.25) brightness(1.15) saturate(1.1) sharpen(1);
            image-rendering: -webkit-optimize-contrast;
        }
        /* بديل لو الصورة ما حُمّلت */
        .logo-placeholder {
            font-size: 5rem; text-align: center; line-height: 1;
        }

        .brand-text { text-align: center; z-index: 1; }
        .brand-name {
            font-size: 1.5rem; font-weight: 900;
            color: var(--gold-light);
            line-height: 1.3;
            text-shadow: 0 2px 8px rgba(0,0,0,.5);
            margin-bottom: .5rem;
        }
        .brand-year {
            display: inline-block;
            font-size: .85rem; font-weight: 700;
            color: rgba(255,255,255,.6);
            border: 1px solid rgba(200,149,42,.4);
            border-radius: 20px;
            padding: 2px 16px;
            margin-bottom: 1rem;
            letter-spacing: 2px;
        }
        .brand-tagline {
            font-size: 1rem; font-weight: 700;
            color: rgba(255,255,255,.85);
            margin-bottom: .3rem;
        }
        .brand-location {
            font-size: .85rem; color: rgba(255,255,255,.5);
            margin-bottom: 1.5rem;
        }
        .brand-divider {
            width: 60px; height: 2px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
            margin: 0 auto 1.2rem;
        }
        .brand-phone {
            font-size: .82rem; color: rgba(255,255,255,.55);
            direction: ltr;
        }
        .brand-storage {
            margin-top: 2rem;
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(200,149,42,.2);
            border-radius: 12px;
            padding: .7rem 1.2rem;
            font-size: .75rem;
            color: rgba(255,255,255,.45);
            text-align: center;
            max-width: 280px;
            line-height: 1.6;
            z-index: 1;
        }

        /* ===== الجانب الأيسر — نموذج الدخول ===== */
        .form-side {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 2rem;
            background: var(--cream);
        }

        .form-box {
            width: 100%; max-width: 400px;
        }
        .form-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        .form-header .welcome {
            font-size: .85rem; font-weight: 600;
            color: var(--olive-main);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: .4rem;
        }
        .form-header h1 {
            font-size: 1.8rem; font-weight: 900;
            color: var(--olive-dark);
            margin-bottom: .3rem;
        }
        .form-header p {
            font-size: .85rem; color: #888;
        }

        .field-wrap {
            position: relative;
            margin-bottom: 1.3rem;
        }
        .field-label {
            display: block;
            font-size: .8rem; font-weight: 700;
            color: var(--olive-dark);
            margin-bottom: .4rem;
        }
        .field-input {
            width: 100%;
            background: #fff;
            border: 1.8px solid #e2e8d5;
            border-radius: 10px;
            padding: .75rem 1rem .75rem 3rem;
            font-family: 'Cairo', sans-serif;
            font-size: .92rem;
            color: #222;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
        }
        .field-input:focus {
            border-color: var(--olive-light);
            box-shadow: 0 0 0 3.5px rgba(106,171,48,.15);
        }
        .field-input.is-invalid { border-color: #dc2626; }
        .field-icon {
            position: absolute;
            left: 1rem; bottom: .78rem;
            color: #aab89a;
            font-size: .9rem;
            pointer-events: none;
        }
        .field-error {
            font-size: .78rem; color: #dc2626;
            margin-top: .3rem;
            display: flex; align-items: center; gap: 4px;
        }

        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, var(--olive-mid), var(--olive-dark));
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: .85rem;
            font-family: 'Cairo', sans-serif;
            font-size: 1rem; font-weight: 800;
            cursor: pointer;
            transition: transform .15s, box-shadow .15s;
            box-shadow: 0 4px 15px rgba(45,90,20,.3);
            margin-top: .5rem;
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(45,90,20,.4);
        }
        .btn-login:active { transform: translateY(0); }

        .form-bottom {
            display: flex; align-items: center;
            justify-content: space-between;
            margin-bottom: .5rem;
        }
        .remember-wrap { display: flex; align-items: center; gap: 6px; }
        .remember-wrap input[type=checkbox] { accent-color: var(--olive-main); }
        .remember-wrap label { font-size: .82rem; color: #666; cursor: pointer; }

        .form-divider {
            text-align: center; position: relative; margin: 1.5rem 0 1.2rem;
            color: #ccc; font-size: .78rem;
        }
        .form-divider::before, .form-divider::after {
            content: ''; position: absolute; top: 50%;
            width: 42%; height: 1px; background: #e8e8e8;
        }
        .form-divider::before { right: 0; }
        .form-divider::after  { left: 0; }

        .register-link {
            text-align: center; font-size: .85rem; color: #888;
        }
        .register-link a {
            color: var(--olive-main); font-weight: 700; text-decoration: none;
        }
        .register-link a:hover { text-decoration: underline; }

        .form-footer {
            margin-top: 3rem; text-align: center;
            font-size: .72rem; color: #bbb;
        }

        /* ===== Responsive ===== */
        @media (max-width: 767px) {
            body { flex-direction: column; }
            .brand-side {
                width: 100%; padding: 2.5rem 1.5rem;
            }
            .logo-frame { width: 150px; height: 150px; margin-bottom: 1.2rem; }
            .brand-name { font-size: 1.2rem; }
            .brand-storage { display: none; }
            .form-side { padding: 2rem 1.5rem; }
        }
    </style>
</head>
<body>

    {{-- ===== جانب الهوية ===== --}}
    <div class="brand-side">

        <div class="logo-frame">
            <div class="logo-frame-inner">
                @if(file_exists(public_path('images/logo-label.jpg')))
                    <img src="{{ asset('images/logo-label.jpg') }}" alt="شعار المعصرة">
                @else
                    <div class="logo-placeholder">🫒</div>
                @endif
            </div>
        </div>

        <div class="brand-text">
            <div class="brand-name">{{ $pressName }}</div>
            <div class="brand-year">١٩٧٨ — EST. 1978</div>
            <div class="brand-tagline">زيت زيتون بعل 100%</div>
            <div class="brand-location">
            سهل حوران — الرمثا
            </div>
            <div class="brand-divider"></div>
            @if($pressPhone)
            <div class="brand-phone"><i class="fas fa-phone-alt" style="color:var(--gold);font-size:.75rem;margin-left:5px"></i>{{ $pressPhone }}</div>
            @endif
        </div>

        <div class="brand-storage">
            يُحفظ في مكان بارد وجاف وبعيداً عن أشعة الشمس المباشرة<br>
            المعصرة غير مسؤولة عن الزيت بعد خروجه من المعصرة
        </div>

    </div>

    {{-- ===== جانب النموذج ===== --}}
    <div class="form-side">
        <div class="form-box">

            <div class="form-header">
                <div class="welcome">أهلاً بك</div>
                <h1>تسجيل الدخول</h1>
                <p>أدخل بياناتك للوصول إلى النظام</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- البريد الإلكتروني --}}
                <div class="field-wrap">
                    <label class="field-label" for="email">البريد الإلكتروني</label>
                    <input id="email" type="email" name="email"
                           class="field-input @error('email') is-invalid @enderror"
                           value="{{ old('email') }}"
                           placeholder="example@email.com"
                           required autofocus>
                    <i class="fas fa-envelope field-icon"></i>
                    @error('email')
                    <div class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</div>
                    @enderror
                </div>

                {{-- كلمة المرور --}}
                <div class="field-wrap">
                    <label class="field-label" for="password">كلمة المرور</label>
                    <input id="password" type="password" name="password"
                           class="field-input @error('password') is-invalid @enderror"
                           placeholder="••••••••"
                           required>
                    <i class="fas fa-lock field-icon"></i>
                    @error('password')
                    <div class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-bottom">
                    <div class="remember-wrap">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember">تذكرني</label>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>دخول
                </button>
            </form>

            <div class="form-divider">أو</div>

            <div class="register-link">
                ليس لديك حساب؟ <a href="{{ route('register') }}">إنشاء حساب جديد</a>
            </div>

            <div class="form-footer">
                نظام إدارة المعصرة &copy; {{ date('Y') }}
            </div>

        </div>
    </div>

</body>
</html>
