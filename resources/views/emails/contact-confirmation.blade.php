@php
$locale = $contactRequest->locale ?? 'en';
$strings = [
    'en' => [
        'title' => 'Contact Request Confirmation — Gamma Neutral',
        'header_subtitle' => 'Contact Request Confirmation',
        'greeting' => 'Request Received!',
        'message' => "Hello <strong>{$contactRequest->first_name} {$contactRequest->last_name}</strong>,<br><br>We have received your contact request. Our team will review it and get back to you as soon as possible.",
        'summary_title' => 'Your Request Summary',
        'subject_label' => 'Subject',
        'message_label' => 'Message',
        'email_label' => 'Contact Email',
        'phone_label' => 'Phone',
        'date_label' => 'Submitted',
        'date_format' => 'F j, Y \a\t g:i A',
        'next_steps_title' => 'Next Steps',
        'step_1' => 'Our team reviews your request',
        'step_2' => 'We will contact you by email within 24-48h',
        'step_3' => 'An advisor will answer your questions',
        'urgent_question' => 'Urgent question?',
        'footer_auto' => 'This email was sent automatically, please do not reply.',
        'footer_contact' => 'For any questions, contact us at the address above.',
        'footer_rights' => 'All rights reserved.',
    ],
    'fr' => [
        'title' => 'Confirmation de demande de contact — Gamma Neutral',
        'header_subtitle' => 'Confirmation de demande de contact',
        'greeting' => 'Demande bien reçue !',
        'message' => "Bonjour <strong>{$contactRequest->first_name} {$contactRequest->last_name}</strong>,<br><br>Nous avons bien reçu votre demande de contact. Notre équipe va l'examiner et vous répondra dans les plus brefs délais.",
        'summary_title' => 'Récapitulatif de votre demande',
        'subject_label' => 'Sujet',
        'message_label' => 'Message',
        'email_label' => 'Email de contact',
        'phone_label' => 'Téléphone',
        'date_label' => 'Date de soumission',
        'date_format' => 'd/m/Y à H:i',
        'next_steps_title' => 'Prochaines étapes',
        'step_1' => 'Notre équipe examine votre demande',
        'step_2' => 'Nous vous contacterons par email sous 24-48h',
        'step_3' => 'Un conseiller répondra à vos questions',
        'urgent_question' => 'Une question urgente ?',
        'footer_auto' => 'Cet email a été envoyé automatiquement, merci de ne pas y répondre.',
        'footer_contact' => "Pour toute question, contactez-nous à l'adresse ci-dessus.",
        'footer_rights' => 'Tous droits réservés.',
    ],
];
$t = $strings[$locale] ?? $strings['en'];
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $t['title'] }}</title>
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

        .success-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #10B981 0%, #34D399 100%);
            border-radius: 50%;
            margin: 0 auto 30px;
            box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.3);
            text-align: center;
            line-height: 80px;
        }

        .success-checkmark {
            color: #ffffff;
            font-size: 48px;
            font-weight: bold;
            line-height: 80px;
            vertical-align: middle;
            display: inline-block;
        }

        .greeting {
            font-size: 24px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 20px;
            text-align: center;
        }

        .message {
            font-size: 15px;
            color: #4b5563;
            line-height: 1.8;
            margin-bottom: 30px;
            text-align: center;
        }

        .info-box {
            background: linear-gradient(135deg, #F9FAFB 0%, #F3F4F6 100%);
            border-radius: 12px;
            padding: 30px;
            margin: 30px 0;
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

        .next-steps {
            background-color: #f5f3ff;
            border-left: 4px solid #8b5cf6;
            padding: 20px;
            margin: 25px 0;
            border-radius: 6px;
        }

        .next-steps-title {
            font-size: 15px;
            font-weight: 600;
            color: #7c3aed;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
        }

        .next-steps-icon {
            margin-right: 8px;
            font-size: 18px;
        }

        .next-steps-text {
            font-size: 14px;
            color: #6d28d9;
            line-height: 1.7;
        }

        .timeline-item {
            margin-bottom: 12px;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        .timeline-item table {
            width: 100%;
            border-collapse: collapse;
        }

        .timeline-number {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            font-size: 13px;
            font-weight: 700;
            text-align: center;
            line-height: 28px;
            display: inline-block;
        }

        .timeline-text {
            font-size: 14px;
            color: #6d28d9;
            line-height: 1.6;
            padding-left: 12px;
            vertical-align: middle;
        }

        .support-section {
            background-color: #F9FAFB;
            border-radius: 8px;
            padding: 20px;
            margin-top: 30px;
            text-align: center;
        }

        .support-text {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 15px;
        }

        .support-email {
            font-size: 15px;
            font-weight: 600;
            color: #8b5cf6;
            text-decoration: none;
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

            .greeting {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <!-- Header -->
        <div class="email-header">
            <div class="logo">GAMMA NEUTRAL</div>
            <div class="header-subtitle">{{ $t['header_subtitle'] }}</div>
        </div>

        <!-- Content -->
        <div class="email-content">
            <!-- Success Icon -->
            <div class="success-icon">
                <span class="success-checkmark">&#10003;</span>
            </div>

            <div class="greeting">
                {{ $t['greeting'] }}
            </div>

            <div class="message">
                {!! $t['message'] !!}
            </div>

            <!-- Request Summary -->
            <div class="info-box">
                <div class="info-title">
                    <span class="info-icon">&#128203;</span>
                    {{ $t['summary_title'] }}
                </div>

                @if($contactRequest->subject)
                <div class="info-field">
                    <div class="info-label">{{ $t['subject_label'] }}</div>
                    <div class="info-value">{{ $contactRequest->subject }}</div>
                </div>
                @endif

                <div class="info-field">
                    <div class="info-label">{{ $t['message_label'] }}</div>
                    <div class="info-value">{{ $contactRequest->message }}</div>
                </div>

                <div class="info-field">
                    <div class="info-label">{{ $t['email_label'] }}</div>
                    <div class="info-value">{{ $contactRequest->email }}</div>
                </div>

                @if($contactRequest->phone)
                <div class="info-field">
                    <div class="info-label">{{ $t['phone_label'] }}</div>
                    <div class="info-value">{{ $contactRequest->phone }}</div>
                </div>
                @endif

                <div class="info-field">
                    <div class="info-label">{{ $t['date_label'] }}</div>
                    <div class="info-value">{{ $contactRequest->created_at->format($t['date_format']) }}</div>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="next-steps">
                <div class="next-steps-title">
                    <span class="next-steps-icon">&#128640;</span>
                    {{ $t['next_steps_title'] }}
                </div>
                <div class="timeline-item">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td style="width: 40px; vertical-align: top;">
                                <span class="timeline-number">1</span>
                            </td>
                            <td class="timeline-text" style="vertical-align: top;">
                                {{ $t['step_1'] }}
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="timeline-item">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td style="width: 40px; vertical-align: top;">
                                <span class="timeline-number">2</span>
                            </td>
                            <td class="timeline-text" style="vertical-align: top;">
                                {{ $t['step_2'] }}
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="timeline-item">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td style="width: 40px; vertical-align: top;">
                                <span class="timeline-number">3</span>
                            </td>
                            <td class="timeline-text" style="vertical-align: top;">
                                {{ $t['step_3'] }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Support Section -->
            <div class="support-section">
                <div class="support-text">
                    {{ $t['urgent_question'] }}
                </div>
                <a href="mailto:{{ config('mail.admin_email', 'support@gammaneutral.com') }}" class="support-email">
                    {{ config('mail.admin_email', 'support@gammaneutral.com') }}
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <div class="footer-text">
                {{ $t['footer_auto'] }}<br>
                {{ $t['footer_contact'] }}
            </div>
            <span class="footer-brand">Gamma Neutral Consulting</span>
            <div class="footer-year">&copy; {{ date('Y') }} Gamma Neutral Consulting Inc. {{ $t['footer_rights'] }}</div>
        </div>
    </div>
</body>
</html>
