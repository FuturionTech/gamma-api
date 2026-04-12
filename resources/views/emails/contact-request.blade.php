<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Request — Gamma Neutral</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #1f2937;
            background-color: #f3f4f6;
            padding: 20px;
        }

        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .email-header {
            background: linear-gradient(135deg, #0f0f0f 0%, #1a1a2e 50%, #16213e 100%);
            padding: 40px 30px;
            text-align: center;
        }

        .logo {
            font-size: 32px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.5px;
            margin-bottom: 10px;
        }

        .header-subtitle {
            color: #c4b5fd;
            font-size: 14px;
            font-weight: 400;
        }

        .email-content {
            padding: 40px 30px;
        }

        .priority-badge {
            display: inline-block;
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: #ffffff;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 6px 16px;
            border-radius: 20px;
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 20px;
        }

        .info-box {
            background: linear-gradient(135deg, #F9FAFB 0%, #F3F4F6 100%);
            border-radius: 12px;
            padding: 30px;
            margin: 20px 0;
        }

        .info-title {
            font-size: 16px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .info-icon {
            margin-right: 10px;
            font-size: 20px;
        }

        .info-field {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #E5E7EB;
        }

        .info-field:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .info-label {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 15px;
            color: #1f2937;
            font-weight: 500;
        }

        .message-box {
            background-color: #ffffff;
            border: 1px solid #E5E7EB;
            border-left: 4px solid #8b5cf6;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }

        .message-label {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }

        .message-text {
            font-size: 15px;
            color: #374151;
            line-height: 1.8;
            white-space: pre-wrap;
        }

        .action-section {
            text-align: center;
            margin: 30px 0;
        }

        .action-button {
            display: inline-block;
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: #ffffff;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 8px;
            box-shadow: 0 4px 12px -2px rgba(139, 92, 246, 0.4);
        }

        .timestamp-box {
            background-color: #F9FAFB;
            border-radius: 8px;
            padding: 15px 20px;
            margin-top: 20px;
            text-align: center;
        }

        .timestamp-label {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .timestamp-value {
            font-size: 14px;
            color: #374151;
            font-weight: 500;
            margin-top: 4px;
        }

        .email-footer {
            background-color: #F9FAFB;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }

        .footer-text {
            font-size: 13px;
            color: #9ca3af;
            line-height: 1.6;
        }

        .footer-brand {
            font-weight: 600;
            color: #8b5cf6;
            margin-top: 10px;
            display: block;
        }

        .footer-year {
            color: #9ca3af;
            font-size: 12px;
            margin-top: 15px;
        }

        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }

            .email-header {
                padding: 30px 20px;
            }

            .email-content {
                padding: 30px 20px;
            }

            .logo {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <!-- Header -->
        <div class="email-header">
            <div class="logo">GAMMA NEUTRAL</div>
            <div class="header-subtitle">Admin Notification</div>
        </div>

        <!-- Content -->
        <div class="email-content">
            <div style="text-align: center; margin-bottom: 10px;">
                <span class="priority-badge">{{ $contactRequest->subject ?? 'General Inquiry' }}</span>
            </div>

            <div class="section-title" style="text-align: center;">New Contact Request</div>

            <!-- Contact Details -->
            <div class="info-box">
                <div class="info-title">
                    <span class="info-icon">👤</span>
                    Contact Details
                </div>

                <div class="info-field">
                    <div class="info-label">Name</div>
                    <div class="info-value">{{ $contactRequest->first_name }} {{ $contactRequest->last_name }}</div>
                </div>

                <div class="info-field">
                    <div class="info-label">Email</div>
                    <div class="info-value">{{ $contactRequest->email }}</div>
                </div>

                @if($contactRequest->phone)
                <div class="info-field">
                    <div class="info-label">Phone</div>
                    <div class="info-value">{{ $contactRequest->phone }}</div>
                </div>
                @endif

                @if($contactRequest->subject)
                <div class="info-field">
                    <div class="info-label">Subject</div>
                    <div class="info-value">{{ $contactRequest->subject }}</div>
                </div>
                @endif

                @if($contactRequest->project_type)
                <div class="info-field">
                    <div class="info-label">Project Type</div>
                    <div class="info-value">{{ $contactRequest->project_type }}</div>
                </div>
                @endif
            </div>

            <!-- Message -->
            <div class="message-box">
                <div class="message-label">Message</div>
                <div class="message-text">{{ $contactRequest->message }}</div>
            </div>

            <!-- Quick Action -->
            <div class="action-section">
                <a href="mailto:{{ $contactRequest->email }}?subject=Re: {{ rawurlencode($contactRequest->subject ?? 'Your Contact Request') }} — Gamma Neutral" class="action-button">
                    Reply to {{ $contactRequest->first_name }}
                </a>
            </div>

            <!-- Timestamp -->
            <div class="timestamp-box">
                <div class="timestamp-label">Received</div>
                <div class="timestamp-value">{{ $contactRequest->created_at->format('F j, Y \a\t g:i A') }} (UTC)</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <div class="footer-text">
                This is an automated admin notification.<br>
                Manage contact requests in the admin dashboard.
            </div>
            <span class="footer-brand">Gamma Neutral Consulting</span>
            <div class="footer-year">&copy; {{ date('Y') }} Gamma Neutral Consulting Inc. All rights reserved.</div>
        </div>
    </div>
</body>
</html>
