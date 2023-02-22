<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class EmailVerificationController extends Controller
{
    public function __invoke(Request $request): JsonResponse | View
    {
        $userId = $request->route('id');
        $user = User::find($userId);

        if (!$user || !hash_equals((string)$request->route('hash'), sha1($user->getEmailForVerification()))) {
            return view('emailVerification.invalidVerification');
        }

        if ($user->hasVerifiedEmail()) {
            return view('emailVerification.alreadyVerified');
        }

        $user->markEmailAsVerified();
        return view('emailVerification.successVerification');
    }
}
