<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Authenticatable;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(Gate $gate)
    {
        $this->registerPolicies();

        $gate->define('view-student', function ($user) {
            // Define your logic here to check if the user has the "view-student" privilege for the given student.
            // Return true if the user has permission, false otherwise.
            return $user->hasPermission('view_record');
        });

        $gate->define('add-student', function ($user) {
            // Define your logic here to check if the user has the "create-student" privilege.
            // Return true if the user has permission, false otherwise.
            return $user->hasPermission('add_record');
        });

        $gate->define('edit-student', function ($user) {
            // Define your logic here to check if the user has the "edit-student" privilege for the given student.
            // Return true if the user has permission, false otherwise.
            return $user->hasPermission('edit_record');
        });

        $gate->define('delete-student', function ($user) {
            // Define your logic here to check if the user has the "delete-student" privilege for the given student.
            // Return true if the user has permission, false otherwise.
            return $user->hasPermission('delete_record');
        });
    }
}
