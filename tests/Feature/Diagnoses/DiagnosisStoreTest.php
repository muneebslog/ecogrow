<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

test('uploading a photo creates a diagnosis from the real Plant.id response shape', function () {
    Storage::fake('public');
    config(['services.plant_id.key' => 'test-key']);

    Http::fake([
        'plant.id/api/v3/identification*' => Http::response([
            'access_token' => 'abc123',
            'result' => [
                'is_plant' => ['binary' => true, 'probability' => 0.98],
                'classification' => [
                    'suggestions' => [
                        [
                            'id' => 'species-1',
                            'name' => 'Ocimum tenuiflorum',
                            'probability' => 0.91,
                            'details' => ['common_names' => ['Holy Basil']],
                        ],
                    ],
                ],
                'is_healthy' => ['binary' => false, 'probability' => 0.2],
                'disease' => [
                    'suggestions' => [
                        [
                            'id' => 'disease-1',
                            'name' => 'Aphid infestation',
                            'probability' => 0.76,
                            'details' => [
                                'description' => 'Small sap-sucking insects on new growth.',
                                'treatment' => [
                                    'biological' => 'Introduce ladybugs.',
                                    'prevention' => 'Apply neem oil every 3 days.',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ], 201),
    ]);

    $user = User::factory()->create();
    $photo = UploadedFile::fake()->image('plant.jpg');

    $response = $this->actingAs($user)->post(route('diagnoses.store'), [
        'photo' => $photo,
    ]);

    $diagnosis = $user->diagnoses()->first();

    $response->assertRedirect(route('diagnoses.show', $diagnosis));
    expect($diagnosis)->not->toBeNull();
    expect($diagnosis->confidence)->toBe(91);
    expect($diagnosis->health_status)->toBe('pest');
    expect($diagnosis->recommendation)->toBe('Apply neem oil every 3 days.');
    Storage::disk('public')->assertExists($diagnosis->photo_path);
});

test('diagnosis fails gracefully when the API key is not configured', function () {
    Storage::fake('public');
    config(['services.plant_id.key' => null]);

    $user = User::factory()->create();
    $photo = UploadedFile::fake()->image('plant.jpg');

    $response = $this->actingAs($user)->post(route('diagnoses.store'), [
        'photo' => $photo,
    ]);

    $response->assertSessionHasErrors('photo');
    expect($user->diagnoses()->count())->toBe(0);
});
