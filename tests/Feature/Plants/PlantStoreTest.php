<?php

use App\Models\Badge;
use App\Models\Species;
use App\Models\User;
use App\Models\UserProgress;

test('authenticated user can add a plant from a species', function () {
    $user = User::factory()->create();
    $species = Species::factory()->create();
    Badge::factory()->create([
        'code' => 'first_tree_planted',
        'criteria_type' => 'plant_count',
        'criteria_threshold' => 1,
    ]);

    $response = $this->actingAs($user)->post(route('plants.store'), [
        'species_id' => $species->id,
        'nickname' => 'Balcony Neem',
    ]);

    $response->assertRedirect(route('dashboard'));

    $this->assertDatabaseHas('plants', [
        'user_id' => $user->id,
        'species_id' => $species->id,
        'nickname' => 'Balcony Neem',
    ]);

    $progress = UserProgress::query()->where('user_id', $user->id)->first();

    expect($progress)->not->toBeNull();
    expect($progress->xp)->toBe(25);

    expect($user->badges()->where('code', 'first_tree_planted')->exists())->toBeTrue();
});

test('species_id is required to add a plant', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('plants.store'), []);

    $response->assertSessionHasErrors('species_id');
});
