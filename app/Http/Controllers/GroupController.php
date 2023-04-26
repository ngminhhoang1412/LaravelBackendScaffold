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

class GroupController extends Controller
{
    public $model = Group::class;

    protected $updatable = [
        'description'
    ];

    /**
     * @param Request $request
     * @return Response
     */
    public function handleStore(Request $request): Response
    {
        DB::beginTransaction();
        $addedGroup = [];
        try {
            $link_id = $request->get('link_id');
            $group_ids = (array) $request->get('group_id');
            $groups = [];

            /** @var GlobalVariable $global */
            $global = app(GlobalVariable::class);
            $user_id = $global->currentUser->id;

            foreach ($group_ids as $key => $value) {
                $addedGroup[] = ['group_id' => $value, 'link_id' => $link_id];
                $groups[] = ['id' => $value, 'user_id' => $user_id];
            }
            // Check if the user have that link
            $checkExist = DB::table(Link::retrieveTableName())
                ->where('user_id', '=', $user_id)
                ->where('id', '=', $link_id)
                ->get();

            if (count($checkExist) > 0) {
                DB::table('group_link')->insertOrIgnore($addedGroup);
                DB::table(Group::retrieveTableName())->insertOrIgnore($groups);
            } else {
                return Helper::getResponse([],"link not found");
            }

            DB::commit();
        } catch (\Exception $e) {
            echo $e->getMessage();
            DB::rollBack();
        }
        return Helper::getResponse($addedGroup);
    }

    public function handleUpdate(Request $request, $id): Response
    {
        $description = $request->get('description');
        DB::beginTransaction();
        try {
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
                ->first()
        );
    }
}
