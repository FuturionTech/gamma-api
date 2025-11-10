<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OtpCodeNotification extends Notification
{

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $otpCode,
        public string $language = 'FR'
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $content = $this->getContent();

        return (new MailMessage)
            ->subject($content['subject'])
            ->greeting($content['greeting'])
            ->line($content['line1'])
            ->line($content['code_display'])
            ->line($content['line2'])
            ->line($content['line3'])
            ->salutation($content['salutation']);
    }

    /**
     * Get localized content based on language.
     */
    protected function getContent(): array
    {
        if ($this->language === 'FR') {
            return [
                'subject' => 'Code OTP Gamma',
                'greeting' => 'Bonjour !',
                'line1' => 'Vous avez demandé un code OTP pour vous connecter à Gamma.',
                'code_display' => "**Votre code OTP est : {$this->otpCode}**",
                'line2' => 'Ce code est valable pendant 10 minutes.',
                'line3' => "Si vous n'avez pas demandé ce code, veuillez ignorer cet email.",
                'salutation' => "Cordialement,\nL'équipe Gamma",
            ];
        }

        // Default to English
        return [
            'subject' => 'Gamma OTP Code',
            'greeting' => 'Hello!',
            'line1' => 'You requested an OTP code to log in to Gamma.',
            'code_display' => "**Your OTP code is: {$this->otpCode}**",
            'line2' => 'This code is valid for 10 minutes.',
            'line3' => 'If you did not request this code, please ignore this email.',
            'salutation' => "Best regards,\nThe Gamma Team",
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'otp_code' => $this->otpCode,
            'language' => $this->language,
            'expires_at' => now()->addMinutes(10),
        ];
    }
}
