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
            'scope' => 'r_liteprofile r_emailaddress w_member_social',
        ]);

        return redirect("https://www.linkedin.com/oauth/v2/authorization?{$params}");
    }

    public function callback(Request $request)
    {
        $response = Http::post('https://www.linkedin.com/oauth/v2/accessToken', [
            'grant_type' => 'authorization_code',
            'code' => $request->code,
            'redirect_uri' => config('services.linkedin.redirect'),
            'client_id' => config('services.linkedin.client_id'),
            'client_secret' => config('services.linkedin.client_secret'),
        ]);

        $token = $response->json();
        
        $profile = Http::withToken($token['access_token'])
            ->get('https://api.linkedin.com/v2/people/~')
            ->json();

        LinkedinProfile::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'linkedin_id' => $profile['id'],
                'access_token' => $token['access_token'],
                'token_expires_at' => now()->addSeconds($token['expires_in']),
                'profile_data' => $profile,
            ]
        );

        return redirect('/dashboard')->with('success', 'LinkedIn connected successfully!');
    }
}