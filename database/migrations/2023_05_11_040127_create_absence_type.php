<?php

use App\Models\AbsenceType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbsenceType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(AbsenceType::retrieveTableName(), function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('description')->nullable();

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });

        Schema::create(AbsenceType::INTERMEDIATE_TABLES[0], function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->nullable(false);
            $table->unsignedInteger('absence_type_id')->nullable(false);
            $table->unique(['user_id', 'absence_type_id']);
            $table->unsignedInteger('amount');

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });

        Schema::create(AbsenceType::INTERMEDIATE_TABLES[1], function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->nullable(false);
            $table->date('date');
            $table->unsignedInteger('absence_type_id')->nullable(false);
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('absence_type_id')->references('id')->on(AbsenceType::retrieveTableName());

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });

        Schema::create(AbsenceType::INTERMEDIATE_TABLES[2], function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('description');
            $table->boolean('is_rostered')->default(false);
            
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
        Schema::dropIfExists(AbsenceType::retrieveTableName());
        Schema::dropIfExists(AbsenceType::INTERMEDIATE_TABLES[0]);
        Schema::dropIfExists(AbsenceType::INTERMEDIATE_TABLES[1]);
        Schema::dropIfExists(AbsenceType::INTERMEDIATE_TABLES[2]);
    }
}
