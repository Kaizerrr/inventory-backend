<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolePrivilegeTable extends Migration
{
    public function up()
    {
        Schema::create('role_privilege', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('privilege_id')->constrained('privileges')->onDelete('cascade');
            $table->primary(['role_id', 'privilege_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('role_privilege');
    }
}
