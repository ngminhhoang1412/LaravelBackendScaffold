<?php

use App\Models\Log;
use App\Models\Link;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateTimestamp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(Link::retrieveTableName(), function (Blueprint $table) {
            $table->timestamp('created_at')->default('CURRENT_TIMESTAMP')->change();
            $table->timestamp('updated_at')->default('CURRENT_TIMESTAMP')->change();
        });

        Schema::table(Log::retrieveTableName(), function (Blueprint $table) {
            $table->timestamp('created_at')->default('CURRENT_TIMESTAMP')->change();
            $table->timestamp('updated_at')->default('CURRENT_TIMESTAMP')->change();
        });

        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->timestamp('created_at')->default('CURRENT_TIMESTAMP')->change();
            $table->timestamp('updated_at')->default('CURRENT_TIMESTAMP')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(Link::retrieveTableName(), function (Blueprint $table) {
            $table->timestamp('created_at')->default(null)->change();
            $table->timestamp('updated_at')->default(null)->change();
        });

        Schema::table(Log::retrieveTableName(), function (Blueprint $table) {
            $table->timestamp('created_at')->default(null)->change();
            $table->timestamp('updated_at')->default(null)->change();
        });

        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->timestamp('created_at')->default(null)->change();
            $table->timestamp('updated_at')->default(null)->change();
        });
    }
}
