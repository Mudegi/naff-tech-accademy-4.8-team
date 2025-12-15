<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\SubscriptionPackage;
use App\Models\Statistic;
use App\Models\FooterContent;
use App\Models\ContactMessage;
use App\Models\WelcomeLink;
use App\Models\Team;
use App\Models\ContactPage;
use App\Notifications\NewContactMessage;
use App\Notifications\ContactMessageReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

class HomeController extends Controller
{
    public function index()
    {
        $subjects = Subject::where('is_active', true)->take(6)->get();
        $packages = SubscriptionPackage::where('is_active', true)->get();
        $statistics = Statistic::where('is_active', true)->orderBy('display_order')->get();
        $teams = Team::active()->ordered()->get();
        $welcomePage = WelcomeLink::first();
        
        return view('frontend.pages.home', compact('subjects', 'packages', 'statistics', 'teams', 'welcomePage'));
    }

    public function about()
    {
        $teamMembers = Team::active()->ordered()->get();
        return view('frontend.pages.about', compact('teamMembers'));
    }

    public function contact()
    {
        $contactPage = ContactPage::first();
        return view('frontend.pages.contact', compact('contactPage'));
    }

    public function submitContact(Request $request)
    {
        $validated = $request->validate(ContactMessage::rules());

        $contactMessage = ContactMessage::create($validated);

        // Log the contact message for debugging
        Log::info('Contact message received', [
            'name' => $contactMessage->name,
            'email' => $contactMessage->email,
            'subject' => $contactMessage->subject
        ]);

        try {
            // Send notification to admin synchronously
            $adminEmail = config('mail.admin_email', 'emmanuelwandera8@gmail.com');
            Log::info('Sending email to admin', ['email' => $adminEmail]);
            
            // Send a direct email to admin for testing
            $phone = $contactMessage->phone ?? 'Not provided';
            Mail::raw(
                "New contact message from {$contactMessage->name}\n\n" .
                "Subject: {$contactMessage->subject}\n" .
                "Email: {$contactMessage->email}\n" .
                "Phone: {$phone}\n\n" .
                "Message:\n{$contactMessage->message}",
                function (Message $message) use ($adminEmail, $contactMessage) {
                    $message->to($adminEmail)
                        ->subject('New Contact Message from ' . $contactMessage->name);
                }
            );
            
            // Also send the notification
            Notification::route('mail', $adminEmail)
                ->notify(new NewContactMessage($contactMessage));

            // Send confirmation to the sender
            Notification::route('mail', $contactMessage->email)
                ->notify(new ContactMessageReceived($contactMessage));
                
            Log::info('Emails sent successfully');
        } catch (\Exception $e) {
            Log::error('Failed to send email', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return redirect()
            ->route('contact')
            ->with('success', 'Thank you for your message. We will get back to you soon!');
    }

    public function pricing()
    {
        $packages = SubscriptionPackage::where('is_active', true)->get();
        return view('frontend.pages.pricing', compact('packages'));
    }

    public function teamMembers()
    {
        $teams = Team::active()->ordered()->get();
        return view('frontend.pages.team-members', compact('teams'));
    }
}
