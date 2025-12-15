<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ImpersonationController extends Controller
{
    public function stop(Request $request)
    {
        if (!session()->has('impersonator_id')) {
            return redirect()->route('dashboard')->with('error', 'No impersonation session found.');
        }

        $impersonatorId = session()->get('impersonator_id');
        $impersonator = User::find($impersonatorId);

        if (!$impersonator) {
            return redirect()->route('dashboard')->with('error', 'Original account not found.');
        }

        // Regenerate session to prevent fixation
        session()->regenerate();

        // Log back in as the impersonator
        Auth::login($impersonator);

        // Clean up impersonation session data
        session()->forget('impersonator_id');
        session()->forget('user_type');

        // Redirect to appropriate dashboard
        switch ($impersonator->account_type) {
            case 'parent':
                return redirect()->route('parent.dashboard')->with('success', 'Returned to your parent account.');
            case 'admin':
            case 'director_of_studies':
            case 'school_admin':
            case 'head_of_department':
                return redirect()->route('admin.dashboard')->with('success', 'Returned to your admin account.');
            case 'teacher':
            case 'subject_teacher':
                return redirect()->route('teacher.dashboard')->with('success', 'Returned to your teacher account.');
            default:
                return redirect()->route('dashboard')->with('success', 'Returned to your account.');
        }
    }
}
