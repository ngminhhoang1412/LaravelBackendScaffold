<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Log;
use App\Models\Link;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(Log::retrieveTableName(), function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedInteger('link_id');
            $table->foreign('link_id')->references('id')->on(Link::retrieveTableName());
            $table->integer('amount')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(Log::retrieveTableName());
    }
}
