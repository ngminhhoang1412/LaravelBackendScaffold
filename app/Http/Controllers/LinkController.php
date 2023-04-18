<?php

namespace App\Http\Controllers;

use App\Common\Helper;
use App\Models\Link;
use Exception;
use App\Common\GlobalVariable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LinkController extends Controller
{
    public $model = Link::class;

    public function createLink(Request $request): Response
    {
        $link = Link::query()
            ->where('link', '=', $request->get('link'))
            ->first();
        if ($link)
            return response([
                'error' => 'Link is available'
            ], $status ?? 400);
        $data_row = new Link();
        DB::beginTransaction();
        try {
            $data_row['link'] = $request->get('link');
            do {
                $short_link = Str::random(7);
                $data = Link::query()
                    ->where('short_link', '=', $short_link)
                    ->first();
            } while ($data);
            $data_row['short_link'] = $short_link;
            /** @var GlobalVariable $global */
            $global = app(GlobalVariable::class);
            $data_row['user_id'] = $global->currentUser->id;
            $data_row->save();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
        return Helper::getResponse($data_row);
    }

    public function handleUpdate(Request $request, $id): Response
    {
        $model = Link::find($id);
        $link = $request->get('link');
        DB::beginTransaction();
        try {
            if ($link) {
                $model['link'] = $link;
                $model['short_link'] = Str::random(7);
                $model->save();
            } else {
                throw new Exception("Not recognizable");
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
        return Helper::getResponse(
            Link::query()
                ->where('id', '=', $id)
                ->first()
        );
    }

    public function redirect(Request $request)
    {
        $short_link = $request->shortlink;
        $model = Link::query()
            ->where('short_link', '=', $short_link)
            ->first();
        $model -> amount++;
        $link = $model -> link;
        $model->save();
        return redirect($link);
    }

    public function getByUser(Request $request): Response
    {
        $limit = $request->has('limit') ? $request->get('limit') : 20;
        /** @var GlobalVariable $global */
        $global = app(GlobalVariable::class);
        $model = Link::query()
            ->where('user_id', '=', $global->currentUser->id)->orderBy( 'created_at', 'desc')->paginate($limit);
        return Helper::getResponse($model);
    }

}