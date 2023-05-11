<?php

namespace App\Common;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;

class CustomBlueprint extends Blueprint
{
    public function audit()
    {
        $this->timestampsDf();
        $this->unsignedBigInteger(Constant::CREATED_BY)->nullable()->default(null);
        $this->unsignedBigInteger(Constant::UPDATED_BY)->nullable()->default(null);
        $this->foreign(Constant::CREATED_BY)
            ->references('id')
            ->on(User::TABLE_NAME);
        $this->foreign(Constant::UPDATED_BY)
            ->references('id')
            ->on(User::TABLE_NAME);
        $this->boolean(Constant::IS_ACTIVE)->nullable(false)->default(true);
    }

    public function timestampsDf()
    {
        $this->timestamp(Constant::CREATED_AT)->useCurrent();
        $this->timestamp(Constant::UPDATED_AT)->useCurrent();
    }
}
