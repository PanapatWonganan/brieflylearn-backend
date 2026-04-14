<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ยกเลิกการรับอีเมล — BrieflyLearn</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #fdfcfa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .card {
            background: #ffffff;
            border-radius: 12px;
            padding: 40px;
            max-width: 480px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .icon {
            font-size: 48px;
            margin-bottom: 16px;
        }
        h1 {
            font-size: 22px;
            color: #1a1a1a;
            margin: 0 0 12px 0;
        }
        p {
            color: #4a4a4a;
            font-size: 16px;
            line-height: 1.5;
            margin: 0 0 24px 0;
        }
        a.button {
            display: inline-block;
            padding: 12px 28px;
            background-color: #4a7a5a;
            color: #ffffff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">{{ $success ? '✓' : '✕' }}</div>
        <h1>{{ $success ? 'เรียบร้อย' : 'เกิดข้อผิดพลาด' }}</h1>
        <p>{{ $message }}</p>
        <a href="{{ $frontendUrl }}" class="button">กลับสู่ BrieflyLearn</a>
    </div>
</body>
</html>
