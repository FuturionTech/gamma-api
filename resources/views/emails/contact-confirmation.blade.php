<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de votre demande de contact - Gamma</title>
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
            background: linear-gradient(135deg, #4F46E5 0%, #6366F1 100%);
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
            color: #E0E7FF;
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
            background-color: #EEF2FF;
            border-left: 4px solid #4F46E5;
            padding: 20px;
            margin: 25px 0;
            border-radius: 6px;
        }

        .next-steps-title {
            font-size: 15px;
            font-weight: 600;
            color: #4F46E5;
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
            color: #4338CA;
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
            background-color: #4F46E5;
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
            color: #4338CA;
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
            color: #4F46E5;
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
            color: #4F46E5;
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
            <div class="logo">GAMMA</div>
            <div class="header-subtitle">Confirmation de demande de contact</div>
        </div>

        <!-- Content -->
        <div class="email-content">
            <!-- Success Icon -->
            <div class="success-icon">
                <span class="success-checkmark">✓</span>
            </div>

            <div class="greeting">
                Demande bien reçue !
            </div>

            <div class="message">
                Bonjour <strong>{{ $contactRequest->first_name }} {{ $contactRequest->last_name }}</strong>,<br><br>
                Nous avons bien reçu votre demande de contact. Notre équipe va l'examiner et vous répondra dans les plus brefs délais.
            </div>

            <!-- Request Summary -->
            <div class="info-box">
                <div class="info-title">
                    <span class="info-icon">📋</span>
                    Récapitulatif de votre demande
                </div>

                @if($contactRequest->subject)
                <div class="info-field">
                    <div class="info-label">Sujet</div>
                    <div class="info-value">{{ $contactRequest->subject }}</div>
                </div>
                @endif

                <div class="info-field">
                    <div class="info-label">Message</div>
                    <div class="info-value">{{ $contactRequest->message }}</div>
                </div>

                <div class="info-field">
                    <div class="info-label">Email de contact</div>
                    <div class="info-value">{{ $contactRequest->email }}</div>
                </div>

                @if($contactRequest->phone)
                <div class="info-field">
                    <div class="info-label">Téléphone</div>
                    <div class="info-value">{{ $contactRequest->phone }}</div>
                </div>
                @endif

                <div class="info-field">
                    <div class="info-label">Date de soumission</div>
                    <div class="info-value">{{ $contactRequest->created_at->format('d/m/Y à H:i') }}</div>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="next-steps">
                <div class="next-steps-title">
                    <span class="next-steps-icon">🚀</span>
                    Prochaines étapes
                </div>
                <div class="timeline-item">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td style="width: 40px; vertical-align: top;">
                                <span class="timeline-number">1</span>
                            </td>
                            <td class="timeline-text" style="vertical-align: top;">
                                Notre équipe examine votre demande
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
                                Nous vous contacterons par email sous 24-48h
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
                                Un conseiller répondra à vos questions
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Support Section -->
            <div class="support-section">
                <div class="support-text">
                    Une question urgente ?
                </div>
                <a href="mailto:{{ env('ADMIN_EMAIL', 'support@gammaneutral.com') }}" class="support-email">
                    {{ env('ADMIN_EMAIL', 'support@gammaneutral.com') }}
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <div class="footer-text">
                Cet email a été envoyé automatiquement, merci de ne pas y répondre.<br>
                Pour toute question, contactez-nous à l'adresse ci-dessus.
            </div>
            <span class="footer-brand">Gamma API</span>
            <div class="footer-year">© {{ date('Y') }} Gamma. Tous droits réservés.</div>
        </div>
    </div>
</body>
</html>
