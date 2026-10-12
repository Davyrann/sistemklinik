<?php

use App\Models\User;

test('email verification is disabled', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/verify-email');

    $response->assertStatus(404);
});
