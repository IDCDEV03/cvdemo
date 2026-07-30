<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\Role;
use Illuminate\Support\Facades\Log; 
class RedirectController extends Controller
{

    public function handleRoot()
    {  

        if (!Auth::check()) {
            return redirect()->route('login')->withHeaders([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma'        => 'no-cache',
                'Expires'       => 'Sat, 01 Jan 2000 00:00:00 GMT',
            ]);
        }

        $user = Auth::user();
     
        if (!$user || empty($user->role)) {
            return redirect()->route('login')->withHeaders([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma'        => 'no-cache',
                'Expires'       => 'Sat, 01 Jan 1990 00:00:00 GMT',
            ]);
        }


        Log::info('Redirect to role: ' . $user->role->value);
        // redirect ตาม role
        switch ($user?->role) {
            case Role::Admin:
                return redirect()->route('admin.dashboard');

            case Role::Manager:
                return redirect()->route('manager.index');

            case Role::Agency:
                return redirect()->route('agency.index');

            case Role::User:
                return redirect()->route('local.home');

            case Role::Company:
                return redirect()->route('company.index');

            case Role::Staff:
                return redirect()->route('staff.index');

            default:
                return redirect()->route('login')->withHeaders([
                    'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                    'Pragma'        => 'no-cache',
                    'Expires'       => 'Sat, 01 Jan 1990 00:00:00 GMT',
                ]);
        }
    }
}
