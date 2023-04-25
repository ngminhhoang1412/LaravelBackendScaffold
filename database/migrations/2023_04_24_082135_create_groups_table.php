<?php

use App\Models\Group;
use App\Models\Link;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Group::retrieveTableName(), function (Blueprint $table) {
            $table->id();
            $table->integer('group_id');
            $table->unsignedInteger('link_id');
            $table->unsignedInteger('user_id');
            $table->foreign('link_id')->references('id')->on(Link::retrieveTableName());
            $table->longText('description')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists(Group::retrieveTableName());
    }
}
