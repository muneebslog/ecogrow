<?php

use App\Models\Plant;
use App\Models\User;

test('plant owner can view its care plan', function () {
    $owner = User::factory()->create();
    $plant = Plant::factory()->for($owner)->create();

    $response = $this->actingAs($owner)->get(route('plants.show', $plant));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('plants/show')
        ->where('plant.id', $plant->id)
    );
});

test('a user cannot view another user\'s plant', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $plant = Plant::factory()->for($owner)->create();

    $response = $this->actingAs($stranger)->get(route('plants.show', $plant));

    $response->assertForbidden();
});
