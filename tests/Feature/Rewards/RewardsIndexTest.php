<?php

use App\Models\Badge;
use App\Models\User;
use App\Models\UserProgress;

test('rewards page lists progress and earned badges', function () {
    $user = User::factory()->create();

    UserProgress::query()->create([
        'user_id' => $user->id,
        'xp' => 120,
        'level' => 2,
    ]);

    $earned = Badge::factory()->create(['code' => 'first_tree_planted']);
    $locked = Badge::factory()->create(['code' => 'master_planter']);
    $user->badges()->attach($earned->id, ['earned_at' => now()]);

    $response = $this->actingAs($user)->get(route('rewards.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('rewards/index')
        ->where('progress.level', 2)
        ->where('progress.xp', 120)
        ->where('badges', fn ($badges) => collect($badges)
            ->firstWhere('code', 'first_tree_planted')['earned'] === true
            && collect($badges)->firstWhere('code', 'master_planter')['earned'] === false
        )
    );

    expect($locked)->not->toBeNull();
});

test('rewards page lazily provisions progress for a brand-new user', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('rewards.index'));

    $response->assertOk();
    $this->assertDatabaseHas('user_progress', ['user_id' => $user->id, 'xp' => 0, 'level' => 1]);
});
