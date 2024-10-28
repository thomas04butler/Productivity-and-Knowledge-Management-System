<?php

namespace Database\Seeders;

use App\Models\Chat;
use App\Models\Message;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // call RolesSeeder before DatabaseSeeder
        $this->call(RolesSeeder::class);

        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@make-it-all.co.uk',
        ])->assignRole('manager');

        // create a manager and assign them the manager role
        $manager = User::factory()->create([
            'name' => 'manager',
            'email' => 'manager@make-it-all.co.uk',
        ]);

        $manager->assignRole('manager');

        // create two team leaders and assign them the team leader roles
        $leader1 = User::factory()->create([
            'name' => 'leader1',
            'email' => 'leader1@make-it-all.co.uk',
        ]);

        $leader1->assignRole('leader');

        $leader2 = User::factory()->create([
            'name' => 'leader2',
            'email' => 'leader2@make-it-all.co.uk',
        ]);

        $leader2->assignRole('leader');

        // Create 10 members and assign them the member role
        $members = User::factory(10)->create();

        foreach ($members as $member) {
            $member->assignRole('member');
        }

        Chat::factory(20)->create();

        Chat::first()->users()->syncWithPivotValues(User::all(), ['is_admin' => true]);
        // for documentation
        Message::factory(1)->create(
            [
                'user_id' => User::first()->id,
                'chat_id' => Chat::first()->id,
            ]
        );

        // Each user is assigned a random chat
        foreach (range(1, 3) as $index) {
            User::all()->each(function ($user) {
                $chat_id = Chat::all()->random()->id;
                $user->chats()->syncWithoutDetaching($chat_id);
                // set admin randomly
                $user->chats()->updateExistingPivot($chat_id, ['is_admin' => rand(0, 1)]);
            });
        }

        Message::factory(100)
            ->recycle(User::all())
            ->recycle(Chat::all())
            ->create();

        Project::factory(10)
            ->recycle(User::role('leader')->get())
            ->create();

        Task::factory(1000)
            ->recycle(User::all())
            ->recycle(Project::all())
            ->create();

    }
}
