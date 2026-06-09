<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - Zinou TV</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-primary: #070b14;
            --bg-card: rgba(13, 19, 35, 0.75);
            --accent-primary: #00f0ff;
            --accent-secondary: #10b981;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border-glass: rgba(255, 255, 255, 0.05);
            --shadow-premium: 0 30px 70px -15px rgba(0, 0, 0, 0.95), 0 0 1px 1px rgba(255, 255, 255, 0.02) inset;
            --radius-lg: 20px;
            --transition-smooth: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }

        * {
            margin: 0; padding: 0; box-sizing: border-box;
            font-family: 'Cairo', sans-serif;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
            background-image:
                radial-gradient(ellipse at 20% 30%, rgba(0, 212, 170, 0.07) 0%, transparent 55%),
                radial-gradient(ellipse at 80% 70%, rgba(79, 126, 249, 0.07) 0%, transparent 55%);
        }

        /* Animated background particles */
        .bg-particle {
            position: absolute;
            border-radius: 50%;
            opacity: 0.03;
            animation: float-particle 8s ease-in-out infinite;
        }

        .bg-particle:nth-child(1) { width:500px; height:500px; background:var(--accent-primary); top:-150px; left:-150px; animation-delay:0s; }
        .bg-particle:nth-child(2) { width:400px; height:400px; background:var(--accent-secondary); bottom:-100px; right:-100px; animation-delay:-3s; }
        .bg-particle:nth-child(3) { width:200px; height:200px; background:var(--accent-primary); top:50%; left:70%; animation-delay:-6s; }

        @keyframes float-particle {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.05); }
            66% { transform: translate(-20px, 20px) scale(0.95); }
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 20px;
            z-index: 10;
        }

        .login-card {
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-lg);
            padding: 44px 40px;
            backdrop-filter: blur(30px);
            box-shadow: var(--shadow-premium), 0 0 0 1px rgba(0,212,170,0.05);
        }

        .logo-area {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 14px;
            margin-bottom: 36px;
            text-align: center;
        }

        .logo-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 30px;
            color: #06080f;
            box-shadow: 0 0 30px rgba(0, 212, 170, 0.4), 0 8px 25px rgba(0,0,0,0.3);
            animation: logo-glow 3s ease-in-out infinite alternate;
        }

        @keyframes logo-glow {
            from { box-shadow: 0 0 20px rgba(0, 212, 170, 0.3), 0 8px 25px rgba(0,0,0,0.3); }
            to { box-shadow: 0 0 40px rgba(0, 212, 170, 0.6), 0 8px 25px rgba(0,0,0,0.3); }
        }

        .logo-name {
            font-size: 28px;
            font-weight: 900;
            background: linear-gradient(90deg, var(--accent-primary), var(--accent-secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .logo-subtitle {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .form-group {
            margin-bottom: 22px;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper svg {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            fill: var(--text-muted);
            pointer-events: none;
        }

        .form-control {
            width: 100%;
            background: rgba(6, 8, 15, 0.6);
            border: 1px solid var(--border-glass);
            border-radius: 10px;
            padding: 14px 48px 14px 16px;
            color: var(--text-main);
            font-size: 15px;
            font-family: 'Cairo', sans-serif;
            transition: var(--transition-smooth);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(0, 212, 170, 0.12);
            background: rgba(6, 8, 15, 0.85);
        }

        .form-control::placeholder { color: var(--text-muted); opacity: 0.5; }

        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, var(--accent-primary), #00b894);
            color: #06080f;
            border: none;
            border-radius: 10px;
            padding: 15px;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            transition: var(--transition-smooth);
            margin-top: 8px;
            font-family: 'Cairo', sans-serif;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 20px rgba(0, 212, 170, 0.3);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0, 212, 170, 0.45);
        }

        .btn-submit:active { transform: translateY(0); }

        .error-message {
            background: rgba(255, 90, 126, 0.1);
            border: 1px solid rgba(255, 90, 126, 0.25);
            color: #ff5a7e;
            padding: 14px;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 22px;
            text-align: center;
            font-weight: 600;
        }

        .login-footer {
            text-align: center;
            margin-top: 28px;
            font-size: 11px;
            color: var(--text-muted);
            border-top: 1px solid var(--border-glass);
            padding-top: 20px;
        }

        .version-badge {
            display: inline-block;
            background: rgba(0,212,170,0.1);
            color: var(--accent-primary);
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
            border: 1px solid rgba(0,212,170,0.2);
        }
    </style>
</head>
<body>

    <div class="bg-particle"></div>
    <div class="bg-particle"></div>
    <div class="bg-particle"></div>

    <div class="login-wrapper">
        <div class="login-card">
            <div class="logo-area">
                <div class="logo-icon">Z</div>
                <h1 class="logo-name">Zinou TV</h1>
                <span class="logo-subtitle">لوحة الإدارة</span>
            </div>

            @if($errors->any())
                <div class="error-message">
                    ⚠️ {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ url('admin/login') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="email">البريد الإلكتروني</label>
                    <div class="input-wrapper">
                        <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                        <input type="email" id="email" name="email" class="form-control"
                               placeholder="admin@zinoutv.com" required autofocus value="{{ old('email') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">كلمة المرور</label>
                    <div class="input-wrapper">
                        <svg viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
                        <input type="password" id="password" name="password" class="form-control"
                               placeholder="••••••••••" required>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    🔓 تسجيل الدخول
                </button>
            </form>

            <div class="login-footer">
                <span class="version-badge">Zinou TV v2.0</span>
                <div style="margin-top:10px;">نظام إدارة IPTV الاحترافي</div>
            </div>
        </div>
    </div>

</body>
</html>
