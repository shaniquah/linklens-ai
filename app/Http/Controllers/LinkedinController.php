<?php

namespace App\Http\Controllers;

use App\Models\LinkedinProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class LinkedinController extends Controller
{
    public function redirect()
    {
        $params = http_build_query([
            'response_type' => 'code',
            'client_id' => config('services.linkedin.client_id'),
            'redirect_uri' => config('services.linkedin.redirect'),
            'scope' => 'openid profile email w_member_social',
        ]);

        return redirect("https://www.linkedin.com/oauth/v2/authorization?{$params}");
    }

    public function callback(Request $request)
    {
        $response = Http::asForm()->post('https://www.linkedin.com/oauth/v2/accessToken', [
            'grant_type' => 'authorization_code',
            'code' => $request->code,
            'redirect_uri' => config('services.linkedin.redirect'),
            'client_id' => config('services.linkedin.client_id'),
            'client_secret' => config('services.linkedin.client_secret'),
        ]);

        $token = $response->json();
        
        if (!isset($token['access_token'])) {
            return redirect('/dashboard')->with('error', 'LinkedIn connection failed: ' . ($token['error_description'] ?? 'Unknown error'));
        }
        
        $profile = Http::withToken($token['access_token'])
            ->get('https://api.linkedin.com/v2/userinfo')
            ->json();

        if (!isset($profile['sub'])) {
            return redirect('/dashboard')->with('error', 'Failed to get LinkedIn profile data');
        }

        LinkedinProfile::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'linkedin_id' => $profile['sub'],
                'access_token' => $token['access_token'],
                'token_expires_at' => now()->addSeconds($token['expires_in']),
                'profile_data' => $profile,
            ]
        );

        return redirect('/dashboard')->with('success', 'LinkedIn connected successfully!');
    }
}