<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Link;
use App\Models\Group;
use App\Common\Helper;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Common\GlobalVariable;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Foundation\Application;

class LinkController extends Controller
{
    public $model = Link::class;

    /**
     * @param Request $request
     * @return Response
     */
    public function handleStore(Request $request): Response
    {
        DB::beginTransaction();
        $newLinkId = null;
        try {
            $link = $request->get('link');
            /** @var GlobalVariable $global */
            $global = app(GlobalVariable::class);
            $user_id = $global->currentUser->id;
            $newLinkId = DB::table(Link::retrieveTableName())->insertGetId([
                'link' => $link,
                'short_link' => Str::random(7),
                'user_id' => $user_id
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
        return Helper::getResponse(Link::query()->find($newLinkId));
    }

    public function handleUpdate(Request $request, $id): Response
    {
        DB::beginTransaction();
        try {
            $groups = $request->get('groups');

            DB::table('group_link')->where('link_id', '=', $id)->delete();
            foreach ($groups as $key => $value) {
                DB::table('group_link')->insert([
                    'group_id' => $value,
                    'link_id' => $id
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
        return Helper::getResponse(Link::with('groups')->get());
    }

    /**
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function redirect(Request $request)
    {
        DB::beginTransaction();
        try {
            $model = Link::query()
                ->where('short_link', '=', $request->shortlink)
                ->first();
            $amount = $model->amount;
            Link::where('short_link', $request->shortlink)
                ->update([
                    'amount' => $amount + 1
                ]);
            $link = $model->link;
            DB::commit();
            return redirect($link);
        } catch (Exception $e) {
            DB::rollBack();
            return Helper::getResponse(null);
        }
    }
}
