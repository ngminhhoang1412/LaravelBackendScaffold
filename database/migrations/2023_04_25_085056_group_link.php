<?php

use App\Models\Group;
use App\Models\Link;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class GroupLink extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_link', function (Blueprint $table) {
            $table->id();
            $table->string('group_id')->nullable(false);
            $table->unsignedInteger('link_id');
            $table->foreign('group_id')->references('id')->on(Group::retrieveTableName());
            $table->foreign('link_id')->references('id')->on(Link::retrieveTableName());
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
        Schema::dropIfExists('group_link');
    }
}
