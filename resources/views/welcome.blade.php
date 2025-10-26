<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Welcome</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1a1a1a;
        }

        .container {
            max-width: 480px;
            padding: 60px 40px;
            text-align: center;
        }

        /* Logo */
        .logo {
            width: 64px;
            height: 64px;
            margin: 0 auto 32px;
            background: #f97316;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s ease;
        }

        .logo:hover {
            transform: translateY(-2px);
        }

        .logo svg {
            width: 32px;
            height: 32px;
            fill: white;
        }

        /* Typography */
        h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
            color: #1a1a1a;
            letter-spacing: -0.02em;
        }

        .subtitle {
            font-size: 16px;
            font-weight: 400;
            color: #737373;
            margin-bottom: 48px;
        }

        /* Button */
        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 32px;
            background: #f97316;
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 500;
            font-size: 15px;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary:hover {
            background: #ea580c;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-primary svg {
            width: 18px;
            height: 18px;
        }

        /* Divider */
        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #e5e5e5, transparent);
            margin: 48px 0;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 32px;
        }

        .info-card {
            padding: 20px;
            background: #fafafa;
            border-radius: 12px;
            transition: all 0.2s ease;
        }

        .info-card:hover {
            background: #f5f5f5;
        }

        .info-icon {
            font-size: 24px;
            margin-bottom: 8px;
        }

        .info-label {
            font-size: 13px;
            color: #737373;
            font-weight: 500;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 18px;
            font-weight: 600;
            color: #1a1a1a;
        }

        /* API Info */
        .api-info {
            padding: 20px;
            background: #fafafa;
            border-radius: 12px;
            border: 1px solid #f0f0f0;
        }

        .api-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #a3a3a3;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .api-url {
            font-family: 'SF Mono', Monaco, 'Courier New', monospace;
            font-size: 13px;
            color: #f97316;
            font-weight: 500;
            word-break: break-all;
        }

        /* Footer */
        .footer {
            margin-top: 48px;
            font-size: 13px;
            color: #a3a3a3;
        }

        .footer a {
            color: #737373;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .footer a:hover {
            color: #f97316;
        }

        /* Status Badge */
        .status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: #dcfce7;
            color: #16a34a;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 24px;
        }

        .status-dot {
            width: 6px;
            height: 6px;
            background: #16a34a;
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Responsive */
        @media (max-width: 640px) {
            .container {
                padding: 40px 24px;
            }

            h1 {
                font-size: 28px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Smooth entrance */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .container > * {
            animation: fadeInUp 0.6s ease-out backwards;
        }

        .container > *:nth-child(1) { animation-delay: 0.1s; }
        .container > *:nth-child(2) { animation-delay: 0.2s; }
        .container > *:nth-child(3) { animation-delay: 0.3s; }
        .container > *:nth-child(4) { animation-delay: 0.4s; }
        .container > *:nth-child(5) { animation-delay: 0.5s; }
        .container > *:nth-child(6) { animation-delay: 0.6s; }
        .container > *:nth-child(7) { animation-delay: 0.7s; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Logo -->
        <div class="logo">
            <svg viewBox="0 0 24 24">
                <path d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z"/>
                <path d="M22 10v6"/>
                <path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5"/>
            </svg>
        </div>

        <!-- Status -->
        <div class="status">
            <span class="status-dot"></span>
            System Online
        </div>

        <!-- Title -->
        <h1>{{ config('app.name', 'BrieflyLearn') }}</h1>
        <p class="subtitle">Admin Dashboard</p>

        <!-- CTA Button -->
        <a href="/admin" class="btn-primary">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
            เข้าสู่ระบบ
        </a>

        <!-- Divider -->
        <div class="divider"></div>

        <!-- Info Grid -->
        <div class="info-grid">
            <div class="info-card">
                <div class="info-icon">📚</div>
                <div class="info-label">Courses</div>
                <div class="info-value">{{ \App\Models\Course::count() }}</div>
            </div>
            <div class="info-card">
                <div class="info-icon">👥</div>
                <div class="info-label">Users</div>
                <div class="info-value">{{ \App\Models\User::count() }}</div>
            </div>
        </div>

        <!-- API Info -->
        <div class="api-info">
            <div class="api-label">API Endpoint</div>
            <div class="api-url">{{ url('/api/v1') }}</div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>{{ config('app.name') }} © {{ date('Y') }}</p>
        </div>
    </div>
</body>
</html>
