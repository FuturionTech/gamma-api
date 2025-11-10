<!DOCTYPE html>
<html lang="{{ $language === 'FR' ? 'fr' : 'en' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $language === 'FR' ? 'Code OTP Gamma' : 'Gamma OTP Code' }}</title>
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

        .greeting {
            font-size: 20px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 20px;
        }

        .message {
            font-size: 15px;
            color: #4b5563;
            line-height: 1.7;
            margin-bottom: 30px;
        }

        .otp-container {
            background: linear-gradient(135deg, #F9FAFB 0%, #F3F4F6 100%);
            border: 2px dashed #4F46E5;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }

        .otp-label {
            font-size: 13px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }

        .otp-code {
            font-size: 42px;
            font-weight: 700;
            color: #4F46E5;
            letter-spacing: 8px;
            font-family: 'Courier New', Courier, monospace;
            text-align: center;
            padding: 10px;
            background-color: #ffffff;
            border-radius: 8px;
            display: inline-block;
            min-width: 280px;
        }

        .expiry-notice {
            background-color: #FEF3C7;
            border-left: 4px solid #F59E0B;
            padding: 15px 20px;
            margin: 25px 0;
            border-radius: 6px;
        }

        .expiry-notice-icon {
            display: inline-block;
            margin-right: 8px;
            font-size: 18px;
        }

        .expiry-notice-text {
            font-size: 14px;
            color: #92400E;
            font-weight: 500;
        }

        .security-warning {
            background-color: #FEE2E2;
            border-left: 4px solid #EF4444;
            padding: 15px 20px;
            margin: 25px 0;
            border-radius: 6px;
        }

        .security-warning-icon {
            display: inline-block;
            margin-right: 8px;
            font-size: 18px;
        }

        .security-warning-text {
            font-size: 13px;
            color: #7F1D1D;
            line-height: 1.6;
        }

        .help-section {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #e5e7eb;
        }

        .help-text {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.6;
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

            .otp-code {
                font-size: 36px;
                letter-spacing: 6px;
                min-width: auto;
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
            <div class="logo">GAMMA</div>
            <div class="header-subtitle">
                {{ $language === 'FR' ? 'Authentification sécurisée' : 'Secure Authentication' }}
            </div>
        </div>

        <!-- Content -->
        <div class="email-content">
            <div class="greeting">
                {{ $language === 'FR' ? 'Bonjour !' : 'Hello!' }}
            </div>

            <div class="message">
                @if($language === 'FR')
                    Vous avez demandé un code de vérification pour accéder à votre compte Gamma.
                    Utilisez le code ci-dessous pour continuer votre connexion.
                @else
                    You requested a verification code to access your Gamma account.
                    Use the code below to continue your login.
                @endif
            </div>

            <!-- OTP Code Box -->
            <div class="otp-container">
                <div class="otp-label">
                    {{ $language === 'FR' ? 'Votre code de vérification' : 'Your verification code' }}
                </div>
                <div class="otp-code">{{ $otpCode }}</div>
            </div>

            <!-- Expiry Notice -->
            <div class="expiry-notice">
                <span class="expiry-notice-icon">⏱️</span>
                <span class="expiry-notice-text">
                    @if($language === 'FR')
                        Ce code expire dans <strong>10 minutes</strong>. Ne le partagez avec personne.
                    @else
                        This code expires in <strong>10 minutes</strong>. Do not share it with anyone.
                    @endif
                </span>
            </div>

            <!-- Security Warning -->
            <div class="security-warning">
                <span class="security-warning-icon">🔒</span>
                <span class="security-warning-text">
                    @if($language === 'FR')
                        <strong>Important :</strong> L'équipe Gamma ne vous demandera jamais votre code OTP par email, téléphone ou tout autre moyen.
                        Si vous n'avez pas demandé ce code, veuillez ignorer cet email et sécuriser votre compte immédiatement.
                    @else
                        <strong>Important:</strong> The Gamma team will never ask for your OTP code via email, phone, or any other means.
                        If you did not request this code, please ignore this email and secure your account immediately.
                    @endif
                </span>
            </div>

            <!-- Help Section -->
            <div class="help-section">
                <div class="help-text">
                    @if($language === 'FR')
                        Besoin d'aide ? Si vous rencontrez des difficultés pour vous connecter,
                        contactez notre équipe de support.
                    @else
                        Need help? If you're having trouble logging in,
                        contact our support team.
                    @endif
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <div class="footer-text">
                @if($language === 'FR')
                    Cet email a été envoyé automatiquement, merci de ne pas y répondre.
                @else
                    This email was sent automatically, please do not reply.
                @endif
            </div>
            <span class="footer-brand">Gamma API</span>
            <div class="footer-year">© {{ date('Y') }} Gamma. {{ $language === 'FR' ? 'Tous droits réservés.' : 'All rights reserved.' }}</div>
        </div>
    </div>
</body>
</html>
