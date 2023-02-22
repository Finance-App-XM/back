<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testRedirectionToSuccessEmailVerification(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);


        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->get($url);

        $response->assertViewIs('emailVerification.successVerification');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email_verified_at' => now(),
        ]);

        $response->assertSee('Seu email foi verificado com
        sucesso!');
    }

    public function testRedirectionToAlreadyVerifiedEmailVerification(): void
    {
        $now = now();

        $user = User::factory()->create([
            'email_verified_at' => $now,
        ]);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->get($url);

        $response->assertViewIs('emailVerification.alreadyVerified');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email_verified_at' => $now,
        ]);

        $response->assertSee('Seu email já está verificado!');
    }

    public function testRedirectionToInvalidEmailVerification(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => 'invalid-hash']
        );

        $response = $this->get($url);

        $response->assertViewIs('emailVerification.invalidVerification');

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
            'email_verified_at' => now(),
        ]);

        $response->assertSee('Link inválido!');
    }
}
