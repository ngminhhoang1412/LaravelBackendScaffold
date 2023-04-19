<?php
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $roles = array_keys(User::ROLES);
            $table->increments('id')->unique();
            $table->string('email', 320)->nullable(false)->unique();
            $table->string('name', 256)->nullable(false);
            $table->string('password', 256)->nullable(false);
            $table->string('remember_token', 100)->nullable()->default(null);
            $table->enum('role', $roles)->nullable(false)
                ->default($roles[1]);
            $table->timestamps();
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
}
