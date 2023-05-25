<?php

use App\Models\AbsenceType;
use App\Common\CustomSchema;
use App\Common\CustomBlueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbsenceTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        CustomSchema::create(AbsenceType::retrieveTableName(), function (CustomBlueprint $table) {
            $table->string('code');
            $table->string('description')->nullable();

            $table->audit();
        });

        CustomSchema::create(AbsenceType::INTERMEDIATE_TABLES[0], function (CustomBlueprint $table) {
            $table->unsignedInteger('user_id')->nullable(false);
            $table->unsignedInteger('absence_type_id')->nullable(false);
            $table->unique(['user_id', 'absence_type_id']);
            $table->unsignedInteger('amount');

            $table->audit();
        });

        CustomSchema::create(AbsenceType::INTERMEDIATE_TABLES[1], function (CustomBlueprint $table) {
            $table->unsignedInteger('user_id')->nullable(false);
            $table->date('date');
            $table->unsignedInteger('absence_type_id')->nullable(false);
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('absence_type_id')->references('id')->on(AbsenceType::retrieveTableName());

            $table->audit();
        });

        CustomSchema::create(AbsenceType::INTERMEDIATE_TABLES[2], function (CustomBlueprint $table) {
            $table->date('date');
            $table->string('description');
            $table->boolean('is_rostered')->default(false);
            
            $table->audit();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        CustomSchema::dropIfExists(AbsenceType::retrieveTableName());
        CustomSchema::dropIfExists(AbsenceType::INTERMEDIATE_TABLES[0]);
        CustomSchema::dropIfExists(AbsenceType::INTERMEDIATE_TABLES[1]);
        CustomSchema::dropIfExists(AbsenceType::INTERMEDIATE_TABLES[2]);
    }
}
