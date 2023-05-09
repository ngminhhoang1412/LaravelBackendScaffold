<?php

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $roles = array_keys(User::ROLES);
            $table->increments('id')->unique();
            $table->string('name')->nullable(false);
            $table->string('email')->nullable(false)->unique();
            $table->string('avatar')->nullable();
            $table->string('password')->nullable(false);
            $table->rememberToken()->default(null);
            $table->enum('role', $roles)->nullable(false)->default($roles[5]);
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
