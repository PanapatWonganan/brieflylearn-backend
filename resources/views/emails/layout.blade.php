<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Antiparallel')</title>
    <style>
        /* Reset styles */
        body, table, td, a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        /* Body styles */
        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            background-color: #0E0E0E;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji';
            color: #F2F2F0;
        }

        /* Container styles */
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #141414;
        }

        /* Header styles */
        .email-header {
            background: #0E0E0E;
            padding: 40px 30px;
            text-align: center;
            border-bottom: 1px solid rgba(0, 255, 186, 0.12);
        }

        .logo {
            font-size: 28px;
            font-weight: 700;
            color: #ffffff;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .tagline {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.5);
            margin: 8px 0 0 0;
        }

        /* Content styles */
        .email-content {
            padding: 40px 30px;
            color: #F2F2F0;
            line-height: 1.6;
        }

        h1 {
            font-size: 24px;
            font-weight: 600;
            color: #F2F2F0;
            margin: 0 0 20px 0;
        }

        h2 {
            font-size: 20px;
            font-weight: 600;
            color: #F2F2F0;
            margin: 30px 0 15px 0;
        }

        p {
            margin: 0 0 16px 0;
            color: rgba(255, 255, 255, 0.7);
            font-size: 16px;
        }

        /* Button styles */
        .button {
            display: inline-block;
            padding: 14px 32px;
            background-color: #00FFBA;
            color: #0E0E0E !important;
            text-decoration: none;
            border-radius: 2px;
            font-weight: 600;
            font-size: 16px;
            margin: 10px 0 20px 0;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #00C98F;
        }

        .button-container {
            text-align: center;
            margin: 30px 0;
        }

        /* Info box styles */
        .info-box {
            background-color: #1A1A1A;
            border-left: 4px solid #00FFBA;
            padding: 16px 20px;
            margin: 20px 0;
            border-radius: 2px;
        }

        .info-box p {
            margin: 0;
            color: rgba(255, 255, 255, 0.7);
        }

        /* Achievement badge styles */
        .achievement-badge {
            text-align: center;
            padding: 20px;
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-radius: 12px;
            margin: 20px 0;
        }

        .achievement-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }

        .achievement-name {
            font-size: 20px;
            font-weight: 600;
            color: #92400e;
            margin: 10px 0 5px 0;
        }

        .achievement-xp {
            font-size: 16px;
            color: #78350f;
            font-weight: 500;
        }

        /* Footer styles */
        .email-footer {
            background-color: #0E0E0E;
            padding: 30px;
            text-align: center;
            border-top: 1px solid rgba(0, 255, 186, 0.08);
        }

        .email-footer p {
            margin: 8px 0;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.4);
        }

        .email-footer a {
            color: #00FFBA;
            text-decoration: none;
        }

        .email-footer a:hover {
            text-decoration: underline;
        }

        .social-links {
            margin: 20px 0;
        }

        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #6b7280;
            text-decoration: none;
        }

        /* Divider */
        .divider {
            height: 1px;
            background-color: rgba(0, 255, 186, 0.08);
            margin: 30px 0;
        }

        /* List styles */
        ul {
            padding-left: 20px;
            margin: 16px 0;
        }

        li {
            margin: 8px 0;
            color: rgba(255, 255, 255, 0.7);
        }

        /* Mobile responsive */
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
            }

            .email-header {
                padding: 30px 20px;
            }

            .email-content {
                padding: 30px 20px;
            }

            .email-footer {
                padding: 20px;
            }

            h1 {
                font-size: 22px;
            }

            .button {
                display: block;
                width: 100%;
                box-sizing: border-box;
            }
        }
    </style>
</head>
<body>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #0E0E0E;">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <table class="email-container" role="presentation" cellspacing="0" cellpadding="0" border="0">
                    <!-- Header -->
                    <tr>
                        <td class="email-header">
                            <h1 class="logo">Antiparallel</h1>
                            <p class="tagline">Learn smarter. Apply faster.</p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td class="email-content">
                            @yield('content')
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td class="email-footer">
                            <p style="font-weight: 600; color: #ffffff;">Antiparallel</p>
                            <p>แพลตฟอร์มเรียนรู้ออนไลน์ที่ช่วยให้คุณพัฒนาทักษะได้อย่างมีประสิทธิภาพ</p>

                            <div class="divider"></div>

                            <p style="font-size: 13px;">
                                หากคุณมีคำถามหรือต้องการความช่วยเหลือ<br>
                                ติดต่อเราได้ที่ <a href="mailto:panapat.w@apppresso.com">panapat.w@apppresso.com</a>
                            </p>

                            <p style="font-size: 12px; color: rgba(255, 255, 255, 0.3); margin-top: 20px;">
                                อีเมลนี้ถูกส่งถึง {{ $user->email ?? 'คุณ' }}<br>
                                หากคุณไม่ต้องการรับอีเมลจากเรา <a href="#">ยกเลิกการรับอีเมล</a>
                            </p>

                            <p style="font-size: 12px; color: rgba(255, 255, 255, 0.3);">
                                &copy; {{ date('Y') }} Antiparallel. สงวนลิขสิทธิ์
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
