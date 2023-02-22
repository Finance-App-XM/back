<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RegistrationControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function testValidUserRegistration(): void
    {
        $name = 'John Doe';
        $email = 'john.doe@example.com';
        $password = 'password';

        $response = $this->postJson(route('register'), [
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);


        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'message',
            'user' => [
                'id',
                'name',
                'email',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function testDatabaseHasUser(): void
    {
        $name = 'John Doe';
        $email = 'john.doe@example.com';
        $password = 'password';

        $this->postJson(route('register'), [
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        $this->assertDatabaseHas('users', [
            'name' => $name,
            'email' => $email,
        ]);
    }

    public function testInvalidUserRegistration()
    {
        $response = $this->postJson(route('register'), []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors([
            'name',
            'email',
            'password',
        ]);
    }
}
