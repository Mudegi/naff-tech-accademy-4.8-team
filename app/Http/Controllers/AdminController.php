<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class AdminController extends BaseController
{
    public function dashboard()
    {
        $contactMessages = ContactMessage::latest()->get();
        return view('admin.dashboard', compact('contactMessages'));
    }
} 