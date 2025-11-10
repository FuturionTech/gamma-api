<?php

namespace App\GraphQL\Mutations;

use App\Models\ContactRequest;
use App\Jobs\SendContactRequestNotification;

final class CreateContactRequest
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        // Create contact request
        $contactRequest = ContactRequest::create([
            'first_name' => $args['first_name'],
            'last_name' => $args['last_name'],
            'email' => $args['email'],
            'phone' => $args['phone'] ?? null,
            'subject' => $args['subject'] ?? null,
            'message' => $args['message'],
            'status' => 'new',
        ]);

        // Queue email notification
        SendContactRequestNotification::dispatch($contactRequest);

        return $contactRequest;
    }
}

