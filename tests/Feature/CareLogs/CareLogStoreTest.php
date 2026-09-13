<?php

use App\Models\Plant;
use App\Models\User;
use App\Models\UserProgress;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;

test('logging care on consecutive days increases the streak and resets after a gap', function () {
    Date::use(CarbonImmutable::class);

    $user = User::factory()->create();
    $plant = Plant::factory()->for($user)->create();

    CarbonImmutable::setTestNow('2026-01-01 09:00:00');
    $this->actingAs($user)->post(route('care-logs.store', $plant), ['action' => 'watered']);

    CarbonImmutable::setTestNow('2026-01-02 09:00:00');
    $this->actingAs($user)->post(route('care-logs.store', $plant), ['action' => 'watered']);

    $progress = UserProgress::query()->where('user_id', $user->id)->first();
    expect($progress->current_streak_days)->toBe(2);

    // Skip a day — streak should reset to 1.
    CarbonImmutable::setTestNow('2026-01-04 09:00:00');
    $this->actingAs($user)->post(route('care-logs.store', $plant), ['action' => 'watered']);

    $progress->refresh();
    expect($progress->current_streak_days)->toBe(1);
    expect($progress->longest_streak_days)->toBe(2);

    CarbonImmutable::setTestNow();
});

test('watering a plant updates its last_watered_at timestamp', function () {
    $user = User::factory()->create();
    $plant = Plant::factory()->for($user)->create(['last_watered_at' => now()->subDays(5)]);

    $this->actingAs($user)->post(route('care-logs.store', $plant), ['action' => 'watered']);

    expect($plant->refresh()->last_watered_at->isToday())->toBeTrue();
});

test('a user cannot log care for another user\'s plant', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $plant = Plant::factory()->for($owner)->create();

    $response = $this->actingAs($stranger)->post(route('care-logs.store', $plant), ['action' => 'watered']);

    $response->assertForbidden();
});
