<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login</title>
    <style>
        html, body { height: 100%; }
        body { margin: 0; background: #eef2f7; font-family: -apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Inter,Arial,sans-serif; }
        .wrap { min-height: 100%; display: grid; grid-template-rows: 64px 1fr; }
        .topbar { background: #1f2a44; color: #fff; display: flex; align-items: center; justify-content: flex-end; padding: 0 16px; }
        .topbar .chip { background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15); padding: 6px 10px; border-radius: 999px; font-size: 12px; }
        .grid { display: grid; grid-template-columns: 1fr 480px 1fr; }
        .panel { background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); }
        .center { display: flex; align-items: center; justify-content: center; padding: 40px 16px; }
        .title { font-size: 18px; font-weight: 600; margin-bottom: 16px; text-align: center; }
        .field { margin-bottom: 12px; }
        .field label { display: block; font-size: 12px; color: #6b7280; margin-bottom: 6px; }
        .input { width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 12px; font-size: 14px; }
        .btn { width: 100%; background: #355d92; color: #fff; border: none; padding: 12px; border-radius: 8px; cursor: pointer; font-weight: 600; }
        .btn:hover { background: #2d4f7c; }
        .error { color: #b91c1c; font-size: 12px; margin-top: 6px; }
        @media (max-width: 800px) { .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="topbar">
            <span class="chip">DEPS Admin</span>
        </div>
        <div class="grid">
            <div></div>
            <div class="center">
                <div class="panel" style="width:100%; max-width:480px;">
                    <div class="title">Вхід адміністратора</div>
                    <form method="POST" action="{{ route('admin.login.submit') }}">
                        @csrf
                        <div class="field">
                            <label>Логін</label>
                            <input class="input" type="text" name="username" value="{{ old('username') }}" required />
                            @error('username') <div class="error">{{ $message }}</div> @enderror
                        </div>
                        <div class="field">
                            <label>Пароль</label>
                            <input class="input" type="password" name="password" required />
                            @error('password') <div class="error">{{ $message }}</div> @enderror
                        </div>
                        <button class="btn" type="submit">Увійти</button>
                    </form>
                </div>
            </div>
            <div></div>
        </div>
    </div>
</body>
</html>


