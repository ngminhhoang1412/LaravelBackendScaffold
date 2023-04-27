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
        Schema::create(Group::INTERMEDIATE_TABLE[0], function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id')->nullable(false);
            $table->unsignedBigInteger('link_id')->nullable(false);
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
        Schema::dropIfExists(Group::INTERMEDIATE_TABLE[0]);
    }
}
