<?php

namespace App\Common;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CustomBlueprint extends Blueprint
{
    /**
     * Default audit fields for auditing purposes
     * @param bool $reference
     * @param bool $defaultIndex
     * @return void
     */
    public function audit(bool $reference = true, bool $defaultIndex = true)
    {
        if ($defaultIndex) {
            $this->increments('id')->unique();
        }
        $this->timestampsDf();
        $this->unsignedInteger(Constant::CREATED_BY)->nullable()->default(null);
        $this->unsignedInteger(Constant::UPDATED_BY)->nullable()->default(null);
        if ($reference) {
            $this->foreign(Constant::CREATED_BY)
                ->references('id')
                ->on(User::TABLE_NAME);
            $this->foreign(Constant::UPDATED_BY)
                ->references('id')
                ->on(User::TABLE_NAME);
        }
        $this->boolean(Constant::IS_ACTIVE)->nullable(false)->default(true);
    }

    /**
     * 2 default timestamp fields
     * @return void
     */
    public function timestampsDf()
    {
        $this->timestamp(Constant::CREATED_AT)->useCurrent();
        $this->timestamp(Constant::UPDATED_AT)
            ->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
    }
}
