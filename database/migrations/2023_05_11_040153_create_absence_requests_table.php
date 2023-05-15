<?php

use App\Models\AbsenceRequest;
use App\Models\AbsenceType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbsenceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(AbsenceRequest::retrieveTableName(), function (Blueprint $table) {
            $status = AbsenceRequest::REQUEST_STATUS;
            $table->id();
            $table->timestamp('date')->useCurrent();
            $table->unsignedInteger('absence_type_id')->nullable(false);
            $table->string('reason');
            $table->unsignedInteger('user_id')->nullable(false);
            $table->enum('status', $status)->default($status[0]);
            $table->foreign('absence_type_id')->references('id')->on(AbsenceType::retrieveTableName());
            $table->foreign('user_id')->references('id')->on('users');

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
        Schema::dropIfExists(AbsenceRequest::retrieveTableName());
    }
}
