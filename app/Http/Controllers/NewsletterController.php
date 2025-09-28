<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        // Validate email
        $request->validate([
            'email' => 'required|email|unique:subscribers,email',
        ]);

        $sendToBrevo = $this->addUserToBrevo($request->email);

        // Save to database (create subscribers table first if not existing)
        // Subscriber::create([
        //     'email' => $request->email,
        // ]);

        // Redirect back with flash message
        return back()->with('success', '🎉 Thanks for subscribing! Check your inbox.');
    }

    public function addUserToBrevo($email)
    {
        $email = $email;

        // $apiKey = BREVO; // Replace with your actual Brevo API key
        $apiKey = env('BREVO_API_KEY');

        $response = Http::withHeaders([
            'api-key' => $apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post('https://api.brevo.com/v3/contacts', [
            'email' => $email,
            'attributes' => [
                'FIRSTNAME' => "ViralGenius"
            ],
            'listIds' => [12], // Replace with the actual List ID from your Brevo account
            'updateEnabled' => true // Will update contact if it already exists
        ]);

        if ($response->failed()) {
            \Log::error('Brevo contact sync failed', [
                'response' => $response->body(),
            ]);
        }
    }
}
