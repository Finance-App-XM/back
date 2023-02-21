<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class RegistrationController extends Controller
{
    public function __invoke(RegisterRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
        } catch (ValidationException $e) {
            return $e->validator->errors();
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password'])
        ]);

        event(new Registered($user));

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user,
        ]);
    }
}
