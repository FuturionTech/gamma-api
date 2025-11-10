<?php

namespace App\Jobs;

use App\Models\ContactRequest;
use App\Mail\ContactRequestReceived;
use App\Mail\ContactRequestConfirmation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendContactRequestNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public ContactRequest $contactRequest
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $adminEmail = env('ADMIN_EMAIL', 'acompaore@futurion.tech');

        // Send notification to admin
        Mail::to($adminEmail)->send(
            new ContactRequestReceived($this->contactRequest)
        );

        // Send confirmation to user
        Mail::to($this->contactRequest->email)->send(
            new ContactRequestConfirmation($this->contactRequest)
        );
    }
}

