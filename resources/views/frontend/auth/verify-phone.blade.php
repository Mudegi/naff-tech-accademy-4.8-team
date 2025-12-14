@extends('frontend.layouts.app')

@section('title', 'Verify Phone Number')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Verify Your Phone Number
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Please enter the verification code sent to your phone.
            </p>
        </div>

        @if (session('status') == 'verification-sms-sent')
            <div class="rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            A new verification code has been sent to your phone.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <form class="mt-8 space-y-6" action="{{ route('verification.phone.verify') }}" method="POST">
            @csrf
            <div>
                <label for="token" class="sr-only">Verification Code</label>
                <input id="token" 
                       name="token" 
                       type="text" 
                       required 
                       class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                       placeholder="Enter verification code">
                @error('token')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Verify Phone Number
                </button>
            </div>

            <div class="text-center">
                <form method="POST" action="{{ route('verification.phone.send') }}">
                    @csrf
                    <button type="submit" class="text-sm text-blue-600 hover:text-blue-500">
                        Resend Verification Code
                    </button>
                </form>
            </div>
        </form>
    </div>
</div>
@endsection 