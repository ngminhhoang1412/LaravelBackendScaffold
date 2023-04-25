<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Link;
use App\Models\Group;
use App\Common\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Common\GlobalVariable;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;

class GroupController extends Controller
{
    public $model = Group::class;

    /**
     * @param Request $request
     * @return Response
     */
    public function handleStore(Request $request): Response
    {
        DB::beginTransaction();
        $addedGroupId = null;
        try {
            $group_id = $request->get('group_id');
            $link_id = $request->get('link_id');
            $description = $request->get('description');

            /** @var GlobalVariable $global */
            $global = app(GlobalVariable::class);
            $user_id = $global->currentUser->id;

            // Check if the user have that link
            $checkExist = DB::table(Link::retrieveTableName())
                ->where('user_id','=',$user_id)
                ->where('id','=',$link_id)
                ->get();

            if(count($checkExist) > 0)
            {
                $addedGroupId = DB::table(Group::retrieveTableName())->insertGetId([
                    'group_id' => $group_id,
                    'link_id' => $link_id,
                    'user_id' => $user_id,
                    'description' => $description
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            echo $e->getMessage();
            DB::rollBack();
        }
        return Helper::getResponse(Group::query()->find($addedGroupId));
    }

}
