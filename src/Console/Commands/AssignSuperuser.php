<?php

/*
 * MIT License
 *
 * Copyright (c) 2019, 2020 Felix Huber
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Seatplus\Web\Console\Commands;

use Illuminate\Console\Command;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

class AssignSuperuser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seatplus:assign:superuser
                            {characterName : the name of a character belonging to the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign superuser permission to a user, search by character name';

    /** @var null|User */
    private $user;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        if ($this->hasAlreadyRun()) {
            $this->warn('Superuser has already been assigned, ask any of the following users to help you out:');

            $users = User::with('characters')
                ->permission('superuser')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'characters' => $user->characters->implode('name', ', '),
                    ];
                });

            $this->table(['user_id', 'characters'], $users);

            $this->error('aborting');

            return;
        }

        $character_name = $this->argument('characterName');

        $users = User::with('characters')
            ->search($character_name)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'characters' => $user->characters->implode('name', ', '),
                ];
            })
            ->toArray();

        $this->table(['user_id', 'characters'], $users);

        $this->info('please select the approriate user_id which should receive the superuser permission');

        $user_id = $this->ask('Who should be superadmin?');

        $this->user = User::find($user_id);

        if (! $this->user) {
            return $this->alert('illegal user_id selected');
        }

        $this->info('Please note after setting a superuser via console, you are only able to set another via web ui.');

        if (! $this->confirm('Do you wish to a continue?')) {
            return $this->error('aborted');
        }

        $role = $this->createRole();
        $this->assignPermissionToRole($role);

        $role->activateMember($this->user);
    }

    private function createRole(): Role
    {
        return Role::findOrCreate('Superuser');
    }

    private function assignPermissionToRole(Role $role)
    {
        $permission = Permission::findOrCreate('superuser');

        $role->givePermissionTo($permission);
    }

    private function hasAlreadyRun()
    {
        try {
            return User::permission('superuser')->get()->isNotEmpty();
        } catch (PermissionDoesNotExist $exception) {
            return false;
        }
    }
}
