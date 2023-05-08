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
     * @param $id
     * @return Response
     */
    public function updateLinkGroup(Request $request, $id): Response
    {
        $callback = function ($request) use ($id) {
            $groups = $request->get('groups');
            DB::beginTransaction();
            try {
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

        };
        $validator = [
            'groups' => [
                'required',
                'array'
            ]
        ];
        return $this->validateCustom($request, $validator, $callback);

    }

    /**
     * @param $shortId
     * @return Application|RedirectResponse|Redirector
     */
        public function redirect($shortId)
    {
        DB::beginTransaction();
        try {
            $model = Link::query()
                ->where('short_link', '=', $shortId)
                ->first();
            $amount = $model->amount;
            Link::where('short_link', $shortId)
                ->update([
                    'amount' => $amount + 1
                ]);
            $link = $model->link;
            DB::commit();
            return redirect($link);
        } catch (Exception $e) {
            DB::rollBack();
            return response('',404);
        }
    }
}
