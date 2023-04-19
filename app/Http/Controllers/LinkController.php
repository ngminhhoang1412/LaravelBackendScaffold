<?php

namespace App\Http\Controllers;

use App\Common\Helper;
use App\Models\Link;
use Exception;
use App\Common\GlobalVariable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
            $model->amount++;
            $link = $model->link;
            DB::commit();
            return redirect($link);
        } catch (Exception $e) {
            DB::rollBack();
            return Helper::getResponse(null);
        }
    }
}
