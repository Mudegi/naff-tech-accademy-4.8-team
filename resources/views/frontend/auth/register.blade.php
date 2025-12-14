@extends('frontend.layouts.app')

@section('title', 'Register')

@section('content')
<div class="min-h-screen flex">
    <!-- Left side - Image -->
    <div class="hidden lg:block lg:w-1/2 relative">
        <img src="{{ $welcomeLinks->getImageUrl('register_image') ?? 'https://images.unsplash.com/photo-1577896851231-70ef18881754?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80' }}" 
             alt="Students learning" 
             class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-900/70 to-blue-900/50"></div>
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="text-center text-white px-12">
                <h1 class="text-4xl font-bold mb-4">Join Naf Academy</h1>
                <p class="text-lg">Start your learning journey today</p>
            </div>
        </div>
    </div>

    <!-- Right side - Registration Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 text-center">
                    Create your account
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                        Sign in here
                    </a>
                </p>
            </div>

            @if ($errors->any())
                <div class="rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                There were errors with your submission
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <form class="mt-8 space-y-6" action="{{ route('register') }}" method="POST" id="registerForm">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input id="name" 
                               name="name" 
                               type="text" 
                               value="{{ old('name') }}"
                               class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="Enter your full name">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address (Optional if phone number is provided)</label>
                        <input id="email" 
                               name="email" 
                               type="email" 
                               value="{{ old('email') }}"
                               class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="Enter your email address">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input id="phone_number" 
                               name="phone_number" 
                               type="tel" 
                               value="{{ old('phone_number') }}"
                               class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="Enter your phone number">
                        @error('phone_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="account_type" class="block text-sm font-medium text-gray-700">Account Type</label>
                        <select id="account_type" 
                                name="account_type" 
                                onchange="toggleSchoolFields()"
                                class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Select account type</option>
                            <option value="student" {{ old('account_type') == 'student' ? 'selected' : '' }}>Student</option>
                            <option value="parent" {{ old('account_type') == 'parent' ? 'selected' : '' }}>Parent</option>
                            <option value="school_admin" {{ old('account_type') == 'school_admin' ? 'selected' : '' }}>School Admin</option>
                        </select>
                        @error('account_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- School Information Fields (shown when school_admin is selected) -->
                    <div id="school-fields" style="display: {{ old('account_type') == 'school_admin' || $errors->has('school_name') || $errors->has('school_email') ? 'block' : 'none' }};" class="space-y-4 p-4 bg-blue-50 border border-blue-200 rounded-md">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">School Information</h3>
                        <p class="text-sm text-gray-600 mb-4">Please provide your school details to create your school account.</p>
                        
                        <div>
                            <label for="school_name" class="block text-sm font-medium text-gray-700">School Name <span class="text-red-500">*</span></label>
                            <input id="school_name" 
                                   name="school_name" 
                                   type="text" 
                                   value="{{ old('school_name') }}"
                                   class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Enter your school name">
                            @error('school_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="school_email" class="block text-sm font-medium text-gray-700">School Email <span class="text-red-500">*</span></label>
                            <input id="school_email" 
                                   name="school_email" 
                                   type="email" 
                                   value="{{ old('school_email') }}"
                                   class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Enter school email address">
                            @error('school_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="school_phone" class="block text-sm font-medium text-gray-700">School Phone Number</label>
                            <input id="school_phone" 
                                   name="school_phone" 
                                   type="tel" 
                                   value="{{ old('school_phone') }}"
                                   class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Enter school phone number">
                            @error('school_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="school_address" class="block text-sm font-medium text-gray-700">School Address</label>
                            <textarea id="school_address" 
                                      name="school_address" 
                                      rows="2"
                                      class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                      placeholder="Enter school address">{{ old('school_address') }}</textarea>
                            @error('school_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input id="password" 
                               name="password" 
                               type="password" 
                               class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="Enter your password">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <input id="password_confirmation" 
                               name="password_confirmation" 
                               type="password" 
                               class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="Confirm your password">
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Create Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Make function available immediately - define it before DOM is ready
// This function will be called by the inline onchange attribute
window.toggleSchoolFields = function() {
    const schoolFields = document.getElementById('school-fields');
    const accountTypeSelect = document.getElementById('account_type');
    
    if (!schoolFields || !accountTypeSelect) {
        // If elements don't exist yet, try again after a short delay
        setTimeout(window.toggleSchoolFields, 50);
        return;
    }
    
    const accountType = accountTypeSelect.value;
    const schoolNameField = document.getElementById('school_name');
    const schoolEmailField = document.getElementById('school_email');
    
    if (accountType === 'school_admin') {
        schoolFields.style.display = 'block';
        schoolFields.style.visibility = 'visible';
        if (schoolNameField) {
            schoolNameField.setAttribute('required', 'required');
        }
        if (schoolEmailField) {
            schoolEmailField.setAttribute('required', 'required');
        }
    } else {
        schoolFields.style.display = 'none';
        schoolFields.style.visibility = 'hidden';
        if (schoolNameField) {
            schoolNameField.removeAttribute('required');
        }
        if (schoolEmailField) {
            schoolEmailField.removeAttribute('required');
        }
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone_number');
    const accountTypeSelect = document.getElementById('account_type');

    // Initial check on page load - show fields if school_admin is selected or if there are errors
    if (accountTypeSelect) {
        const accountType = accountTypeSelect.value;
        const schoolFields = document.getElementById('school-fields');
        const hasSchoolErrors = schoolFields && (schoolFields.querySelector('.text-red-600') !== null || 
                                                 document.querySelector('[name="school_name"]') !== null);
        
        // Show fields if school_admin is selected or if there are validation errors
        if (accountType === 'school_admin' || hasSchoolErrors) {
            window.toggleSchoolFields();
        }
        
        // Add event listener for account type change (in addition to inline onchange)
        accountTypeSelect.addEventListener('change', function() {
            window.toggleSchoolFields();
        });
    }

    // Add input event listeners to both fields
    if (emailInput && phoneInput) {
        [emailInput, phoneInput].forEach(input => {
            input.addEventListener('input', function() {
                // If phone number is empty, make email required (unless school_admin)
                if (accountTypeSelect && accountTypeSelect.value !== 'school_admin') {
                    if (!phoneInput.value.trim()) {
                        emailInput.setAttribute('required', 'required');
                    } else {
                        emailInput.removeAttribute('required');
                    }
                }
            });
        });

        // Initial check
        if (!phoneInput.value.trim() && accountTypeSelect && accountTypeSelect.value !== 'school_admin') {
            emailInput.setAttribute('required', 'required');
        }
    }
});

// Also run immediately if DOM is already loaded (for faster response)
if (document.readyState !== 'loading') {
    const accountTypeSelect = document.getElementById('account_type');
    if (accountTypeSelect && accountTypeSelect.value === 'school_admin') {
        window.toggleSchoolFields();
    }
}
</script>
@endsection 