<?php

use App\Models\Species;
use App\Models\User;

test('quiz submission returns ranked species recommendations', function () {
    $user = User::factory()->create();

    $sunnyTree = Species::factory()->create([
        'sunlight' => 'full_sun',
        'category' => 'tree',
    ]);
    $shadyHouseplant = Species::factory()->create([
        'sunlight' => 'low_light',
        'category' => 'plant',
    ]);

    $response = $this->actingAs($user)->post(route('onboarding.quiz.submit'), [
        'sunlight' => 'full_sun',
        'placement' => 'yard',
    ]);

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('onboarding/recommendations')
        ->where('matches.0.species_id', $sunnyTree->id)
        ->where('matches.1.species_id', $shadyHouseplant->id)
    );
});

test('quiz submission requires valid answers', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('onboarding.quiz.submit'), []);

    $response->assertSessionHasErrors(['sunlight', 'placement']);
});
