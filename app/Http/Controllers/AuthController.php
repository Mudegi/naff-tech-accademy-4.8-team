<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationEmail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * Get user type from session
     *
     * @param Request $request
     * @return string|null
     */
    public static function getUserTypeFromSession(Request $request)
    {
        return $request->session()->get('user_type');
    }
    public function showLoginForm()
    {
        return view('frontend.auth.login');
    }

    public function login(Request $request)
    {
        \Log::info('Login attempt', [
            'login' => $request->input('login'),
            'has_password' => $request->filled('password'),
        ]);

        $validator = Validator::make($request->all(), [
            'login' => 'required|string', // This can be email or phone
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            \Log::warning('Validation failed', ['errors' => $validator->errors()->toArray()]);
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Determine if login is email or phone
        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_number';
        
        // For phone-based login, also check for parent phone format
        $user = null;
        if ($loginType === 'phone_number') {
            // Try exact phone match first
            $user = User::where('phone_number', $request->login)
                ->where('is_active', true)
                ->first();
            
            // If not found, try parent phone format (+parent_xxxxx)
            if (!$user) {
                $user = User::where('phone_number', '+parent_' . $request->login)
                    ->where('is_active', true)
                    ->where('account_type', 'parent')
                    ->first();
            }
        } else {
            // For email-based login
            $user = User::where('email', $request->login)
                ->where('is_active', true)
                ->first();
        }
        
        \Log::info('Attempting authentication', [
            'login_type' => $loginType,
            'login_value' => $request->login,
            'user_found' => $user ? true : false,
        ]);
        
        if ($user) {
            \Log::info('User found', [
                'user_id' => $user->id,
                'account_type' => $user->account_type,
                'is_active' => $user->is_active,
                'password_check' => \Hash::check($request->password, $user->password),
            ]);
        } else {
            \Log::warning('User not found', ['login_type' => $loginType, 'value' => $request->login]);
        }
        
        // Manual authentication if user found and password matches
        if ($user && \Hash::check($request->password, $user->password)) {
            Auth::login($user, $request->filled('remember'));
            \Log::info('Authentication successful');
            $user = Auth::user();

            // Check if user belongs to a school and if that school is active
            if ($user->school_id) {
                $school = School::find($user->school_id);
                if (!$school) {
                    \Log::warning('School not found', ['school_id' => $user->school_id]);
                    Auth::logout();
                    return back()->withErrors([
                        'login' => 'Your school account is no longer active. Please contact support.',
                    ])->withInput();
                }
            }
            
            // Regenerate session first (creates new session ID)
            $request->session()->regenerate();
            
            // Now terminate all OTHER sessions for this user (keeping the current one)
            $currentSessionId = $request->session()->getId();
            $user->terminateOtherSessions($currentSessionId);
            
            // Store user type in session for future use
            $request->session()->put('user_type', $user->account_type);
            
            \Log::info('Login successful', [
                'user_id' => $user->id,
                'account_type' => $user->account_type,
                'school_id' => $user->school_id,
            ]);
            
            // Redirect based on account type
            if ($user->account_type === 'admin' && !$user->school_id) {
                // Super admin (no school)
                return redirect()->intended('admin/dashboard');
            } elseif ($user->account_type === 'director_of_studies') {
                // Director of Studies - redirect to their dashboard
                return redirect()->intended('admin/school/director-of-studies/dashboard');
            } elseif (in_array($user->account_type, ['school_admin', 'head_of_department'])) {
                // School admin and head of department - redirect to school admin dashboard
                return redirect()->intended('admin/dashboard');
            } elseif (in_array($user->account_type, ['teacher', 'subject_teacher'])) {
                // Teachers (both 'teacher' and 'subject_teacher') - redirect to teacher dashboard
                return redirect()->intended('teacher/dashboard');
            } elseif ($user->account_type === 'student') {
                return redirect()->intended('student/dashboard');
            } elseif ($user->account_type === 'parent') {
                // Parents - redirect to parent portal dashboard
                return redirect()->intended('parent/dashboard');
            } else {
                return redirect()->intended('dashboard');
            }
        }

        \Log::warning('Authentication failed', ['login' => $request->login]);
        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ])->withInput();
    }

    public function showRegistrationForm()
    {
        return view('frontend.auth.register');
    }

    public function register(Request $request)
    {
        // Base validation rules
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['required', 'string', 'max:20', 'unique:users'],
            'account_type' => ['required', 'string', 'in:student,parent,school_admin'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];

        // Add school validation rules if account type is school_admin
        if ($request->account_type === 'school_admin') {
            $rules['school_name'] = ['required', 'string', 'max:255', 'unique:schools,name'];
            $rules['school_email'] = ['required', 'email', 'max:255', 'unique:schools,email'];
            $rules['school_phone'] = ['nullable', 'string', 'max:20'];
            $rules['school_address'] = ['nullable', 'string'];
        }

        $validator = Validator::make($request->all(), $rules);

        // Add custom validation rule for email/phone (only for non-school-admin)
        if ($request->account_type !== 'school_admin') {
            $validator->after(function ($validator) use ($request) {
                if (empty($request->email) && empty($request->phone_number)) {
                    $validator->errors()->add('email', 'Either email or phone number is required.');
                    $validator->errors()->add('phone_number', 'Either email or phone number is required.');
                }
            });
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create school if account type is school_admin
        $school = null;
        if ($request->account_type === 'school_admin') {
            $school = School::create([
                'name' => $request->school_name,
                'slug' => Str::slug($request->school_name),
                'email' => $request->school_email,
                'phone_number' => $request->school_phone,
                'address' => $request->school_address,
                'status' => 'inactive', // Schools start inactive until subscription is approved
            ]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'account_type' => $request->account_type,
            'password' => Hash::make($request->password),
            'school_id' => $school ? $school->id : null,
            'email_verified_at' => $request->email ? now() : null, // Auto-verify email if provided
            'phone_verified' => true, // Auto-verify phone
            'is_active' => true,
        ]);

        Auth::login($user);
        
        // Store user type in session for future use
        $request->session()->put('user_type', $user->account_type);

        // Redirect based on account type
        if ($user->account_type === 'student') {
            return redirect()->route('student.dashboard');
        } elseif ($user->account_type === 'school_admin') {
            return redirect()->intended('admin/dashboard')
                ->with('success', 'School account created successfully! Welcome to your admin dashboard.');
        }
        
        return redirect()->intended('dashboard');
    }

    public function verifyEmail($token)
    {
        $user = User::where('email_verification_token', $token)->first();

        if (!$user) {
            return redirect()->route('verification.notice')
                ->with('error', 'Invalid verification token.');
        }

        $user->email_verified_at = now();
        $user->email_verification_token = null;
        $user->save();

        return redirect()->route('dashboard')
            ->with('success', 'Email verified successfully.');
    }

    public function verifyPhone(Request $request)
    {
        $request->validate([
            'token' => 'required|string|size:6',
        ]);

        $user = Auth::user();

        if ($user->phone_verification_token !== $request->token) {
            return back()->withErrors(['token' => 'Invalid verification code.']);
        }

        $user->phone_verified = true;
        $user->phone_verification_token = null;
        $user->save();

        return redirect()->route('dashboard')
            ->with('success', 'Phone number verified successfully.');
    }

    public function resendVerificationEmail(Request $request)
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        $user->email_verification_token = Str::random(60);
        $user->save();

        event(new Registered($user));

        return back()->with('success', 'Verification email sent.');
    }

    public function resendVerificationSMS(Request $request)
    {
        $user = Auth::user();

        if ($user->phone_verified) {
            return redirect()->route('dashboard');
        }

        $user->phone_verification_token = Str::random(6);
        $user->save();

        // TODO: Implement SMS sending logic here

        return back()->with('success', 'Verification SMS sent.');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        
        // Terminate all sessions for this user
        if ($user) {
            $user->terminateAllSessions();
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Clear user type from session
        $request->session()->forget('user_type');
        
        return redirect()->route('home');
    }

    public function showForgotPasswordForm()
    {
        return view('frontend.auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => __($status)])
                    : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPasswordForm(Request $request, $token)
    {
        return view('frontend.auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ]);

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }
} 