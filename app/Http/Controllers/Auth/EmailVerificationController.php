<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmailVerificationController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $userId = $request->route('id');
        $user = User::find($userId);

        if (!$user || !hash_equals((string)$request->route('hash'), sha1($user->getEmailForVerification()))) {
            return response()->json([
                'message' => 'Invalid verification link',
            ], Response::HTTP_NOT_FOUND);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified',
            ], Response::HTTP_CONFLICT);
        }

        $user->markEmailAsVerified();
        return response()->json([
            'message' => 'Email verified successfully',
            'user' => $user,
        ]);
    }
}
