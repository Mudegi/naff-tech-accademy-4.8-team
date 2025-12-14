<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::where('is_active', true)->paginate(9);
        return View::make('frontend.pages.subjects', compact('subjects'));
    }

    public function show($hashId)
    {
        $subject = Subject::findByHashId($hashId);
        
        if (!$subject) {
            abort(404);
        }

        $subject->load('topics');
        return View::make('frontend.pages.subject-details', compact('subject'));
    }
} 