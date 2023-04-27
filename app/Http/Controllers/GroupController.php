<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Link;
use App\Models\User;
use App\Models\Group;
use App\Common\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Common\GlobalVariable;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    public $model = Group::class;

    /**
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function handleUpdate(Request $request, $id): Response
    {
        $description = $request->get('description');
        DB::beginTransaction();
        try {
            /** @var GlobalVariable $global */
            $global = app(GlobalVariable::class);
            $user_id = $global->currentUser->id;
            DB::table(Group::retrieveTableName())
                ->where('id', '=', $id)
                ->update([
                    'description' => $description
                ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
        return Helper::getResponse(
            Group::query()
                ->where('id', '=', $id)
                ->get()
        );
    }
}
