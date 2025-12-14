<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SchoolContextController extends Controller
{
    /**
     * Switch to a school context (for super admin)
     */
    public function switch(Request $request, $schoolId = null)
    {
        $user = Auth::user();
        
        // Only allow super admin to switch school context
        if ($user->account_type !== 'admin' || $user->school_id) {
            abort(403, 'Only super administrators can switch school context.');
        }
        
        if ($schoolId) {
            $school = School::findOrFail($schoolId);
            Session::put('admin_school_context', $school->id);
            Session::put('admin_school_name', $school->name);
            
            return redirect()->back()
                ->with('success', "Switched to {$school->name} context. All operations will now be for this school.");
        } else {
            // Clear school context (switch back to global)
            Session::forget('admin_school_context');
            Session::forget('admin_school_name');
            
            return redirect()->back()
                ->with('success', 'Switched back to global context. All operations will now be global.');
        }
    }
    
    /**
     * Get current school context
     */
    public function getCurrentContext()
    {
        $user = Auth::user();
        
        if ($user->account_type === 'admin' && !$user->school_id) {
            $schoolId = Session::get('admin_school_context');
            if ($schoolId) {
                $school = School::find($schoolId);
                return response()->json([
                    'has_context' => true,
                    'school_id' => $schoolId,
                    'school_name' => $school ? $school->name : 'Unknown School'
                ]);
            }
        }
        
        return response()->json([
            'has_context' => false,
            'school_id' => null,
            'school_name' => null
        ]);
    }
}

