<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect($this->redirectPath($request->user()));
        }
    
        return view('auth.verify-email');
    }
    
   protected function redirectPath($user): string
{
    // Admin y supervisor van a posts.index
    return ($user->is_admin || $user->isSupervisor) ? route('posts.index') : '/';
}
}
