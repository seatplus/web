<?php


namespace Seatplus\Web\Console\Commands;


use Illuminate\Console\Command;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;

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
        if ($this->hasAlreadyRun()){
            $this->warn('Superuser has already been assigned, ask any of the following users to help you out:');

            $users = User::with('characters')
                ->permission('superuser')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'characters' => $user->characters->implode('name', ', ')
                    ];
                });

            $this->table(['user_id', 'characters'],$users);

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
                    'characters' => $user->characters->implode('name', ', ')
                ];
            })
            ->toArray();

        $this->table(['user_id', 'characters'],$users);

        $this->info('please select the approriate user_id which should receive the superuser permission');

        $user_id = $this->ask('Who should be superadmin?');

        $this->user = User::find($user_id);

        if (!$this->user)
            return $this->alert('illegal user_id selected');

        $this->info('Please note after setting a superuser via console, you are only able to set another via web ui.');


        if(! $this->confirm('Do you wish to a continue?')) {
            return $this->error('aborted');
        }

        $role = $this->createRole();
        $this->assignPermuissionToRole($role);

        $this->user->assignRole($role);

    }

    private function createRole(): Role
    {
        return Role::findOrCreate('Superuser');
    }

    private function assignPermuissionToRole(Role $role)
    {
        $permission = Permission::findOrCreate('superuser');

        $role->givePermissionTo($permission);
        return;
    }

    private function hasAlreadyRun()
    {
        return User::permission('superuser')->get()->isNotEmpty();
    }


}
