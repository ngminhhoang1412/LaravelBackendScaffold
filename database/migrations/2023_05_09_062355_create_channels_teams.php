<?php

use App\Models\Category;
use App\Models\Channel;
use App\Models\Group;
use App\Models\Growth;
use App\Models\Platform;
use App\Models\Team;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChannelsTeams extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Category::retrieveTableName(), function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->boolean('is_active')->default(true);
            $table->date('created_by')->default(null);
            $table->date('updated_by')->default(null);
            $table->string('name');
            $table->string('description');
        });

        Schema::create(Group::retrieveTableName(), function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->boolean('is_active')->default(true);
            $table->date('created_by')->default(null);
            $table->date('updated_by')->default(null);
            $table->string('name');
            $table->string('description');
        });

        Schema::create(Platform::retrieveTableName(), function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->boolean('is_active')->default(true);
            $table->date('created_by')->default(null);
            $table->date('updated_by')->default(null);
            $table->string('name');
            $table->string('description');
            $table->string('logo');
        });

        Schema::create(Channel::retrieveTableName(), function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->boolean('is_active')->default(true);
            $table->date('created_by')->default(null);
            $table->date('updated_by')->default(null);
            $table->string('channel_id');
            $table->string('name');
            $table->string('logo');
            $table->unsignedInteger('platform_id');
            $table->foreign('platform_id')->references('id')->on(Platform::retrieveTableName());
            $table->unique(['channel_id', 'platform_id']);
        });

        Schema::create(Team::retrieveTableName(), function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->boolean('is_active')->default(true);
            $table->date('created_by')->default(null);
            $table->date('updated_by')->default(null);
            $table->string('name');
            $table->string('description');
        });

        Schema::create(Growth::retrieveTableName(), function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->boolean('is_active')->default(true);
            $table->date('created_by')->default(null);
            $table->date('updated_by')->default(null);
            $table->string('detail');
            $table->date('date');
        });

        Schema::create('category_channel', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->date('created_by')->default(null);
            $table->date('updated_by')->default(null);
            $table->integer('category_id')->unsigned();
            $table->foreign('category_id')->references('id')->on(Category::retrieveTableName())->onDelete('cascade');
            $table->integer('channel_id')->unsigned();
            $table->foreign('channel_id')->references('id')->on(Channel::retrieveTableName())->onDelete('cascade');
        });

        Schema::create('channel_group', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->date('created_by')->default(null);
            $table->date('updated_by')->default(null);
            $table->integer('group_id')->unsigned();
            $table->foreign('group_id')->references('id')->on(Group::retrieveTableName())->onDelete('cascade');
            $table->integer('channel_id')->unsigned();
            $table->foreign('channel_id')->references('id')->on(Channel::retrieveTableName())->onDelete('cascade');
        });

        Schema::create('channel_user', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->date('created_by')->default(null);
            $table->date('updated_by')->default(null);
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('channel_id')->unsigned();
            $table->foreign('channel_id')->references('id')->on(Channel::retrieveTableName())->onDelete('cascade');
            $table->boolean('is_supporter')->default(false);
            $table->boolean('is_responsible')->default(false);
            $table->unique(['channel_id', 'user_id']);
        });

        Schema::create('team_user', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->date('created_by')->default(null);
            $table->date('updated_by')->default(null);
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('team_id')->unsigned();
            $table->foreign('team_id')->references('id')->on(Team::retrieveTableName())->onDelete('cascade');
            $table->boolean('is_leader')->default(false);
            $table->unique(['team_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(Category::retrieveTableName());
        Schema::dropIfExists(Group::retrieveTableName());
        Schema::dropIfExists(Platform::retrieveTableName());
        Schema::dropIfExists(Team::retrieveTableName());
        Schema::dropIfExists(Channel::retrieveTableName());
        Schema::dropIfExists(Growth::retrieveTableName());
        Schema::dropIfExists('category_channel');
        Schema::dropIfExists('channel_group');
        Schema::dropIfExists('channel_user');
        Schema::dropIfExists('team_user');
    }
}
