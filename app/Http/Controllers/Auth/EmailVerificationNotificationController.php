<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect($this->redirectPath($request->user()));
        }
    
        $request->user()->sendEmailVerificationNotification();
    
        return back()->with('status', 'verification-link-sent');
    }
    
   protected function redirectPath($user): string
{
    // Admin y supervisor van a posts.index
    return ($user->is_admin || $user->isSupervisor) ? route('posts.index') : '/';
}
}
