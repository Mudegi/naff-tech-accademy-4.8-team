<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Message - {{ $contactMessage->subject }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold mb-4">Contact Message</h1>
            
            <div class="mb-4">
                <h2 class="text-lg font-semibold">From:</h2>
                <p>{{ $contactMessage->name }} ({{ $contactMessage->email }})</p>
                @if($contactMessage->phone)
                    <p>Phone: {{ $contactMessage->phone }}</p>
                @endif
            </div>
            
            <div class="mb-4">
                <h2 class="text-lg font-semibold">Subject:</h2>
                <p>{{ $contactMessage->subject }}</p>
            </div>
            
            <div class="mb-4">
                <h2 class="text-lg font-semibold">Message:</h2>
                <p class="whitespace-pre-line">{{ $contactMessage->message }}</p>
            </div>
            
            <div class="mb-4">
                <h2 class="text-lg font-semibold">Received:</h2>
                <p>{{ $contactMessage->created_at->format('F j, Y, g:i a') }}</p>
            </div>
            
            <div class="mt-6">
                <a href="{{ route('contact') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    Back to Contact Page
                </a>
            </div>
        </div>
    </div>
</body>
</html> 