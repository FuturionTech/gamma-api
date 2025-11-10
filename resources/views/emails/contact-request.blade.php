<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Contact Request</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4F46E5;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
            background-color: #f9f9f9;
        }
        .field {
            margin-bottom: 15px;
        }
        .field-label {
            font-weight: bold;
            color: #555;
        }
        .field-value {
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Contact Request</h1>
        </div>
        <div class="content">
            <div class="field">
                <div class="field-label">From:</div>
                <div class="field-value">{{ $contactRequest->first_name }} {{ $contactRequest->last_name }}</div>
            </div>

            <div class="field">
                <div class="field-label">Email:</div>
                <div class="field-value">{{ $contactRequest->email }}</div>
            </div>

            @if($contactRequest->phone)
            <div class="field">
                <div class="field-label">Phone:</div>
                <div class="field-value">{{ $contactRequest->phone }}</div>
            </div>
            @endif

            @if($contactRequest->subject)
            <div class="field">
                <div class="field-label">Subject:</div>
                <div class="field-value">{{ $contactRequest->subject }}</div>
            </div>
            @endif

            <div class="field">
                <div class="field-label">Message:</div>
                <div class="field-value">{{ $contactRequest->message }}</div>
            </div>

            <div class="field">
                <div class="field-label">Received:</div>
                <div class="field-value">{{ $contactRequest->created_at->format('F j, Y \a\t g:i A') }}</div>
            </div>
        </div>
    </div>
</body>
</html>

