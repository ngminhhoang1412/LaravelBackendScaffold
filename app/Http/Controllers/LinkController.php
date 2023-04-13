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
            return Helper::getResponse($link);
        DB::beginTransaction();
        try {
            $this->model -> link = $request->get('link');
            $this->model -> short_link = Str::random(7);

            /** @var GlobalVariable $modelObj */
            $global = app(GlobalVariable::class);
            $this->model -> user_id = $global->currentUser->id;
            $this->model->save();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
        return Helper::getResponse(Link::query()->find($this->model));
    }

    public function handleUpdate(Request $request, $id): Response
    {
        $status = $request->get('status');
        $amount = $request->get('amount');
        DB::beginTransaction();
        try {
            if ($status) {
                // Cancel order
                $this->handleCancelOrder($id, $status);
            } elseif ($amount) {
                // Update remaining
                $this->handleProgressUpdate($id, $amount);
            } else {
                throw new Exception("Not recognizable");
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
        return Helper::getResponse(
            Order::query()
                ->where('id', '=', $id)
                ->orWhere('order_no', '=', $id)
                ->first()
        );
    }


    public function handleStore(Request $request): Response
    {
        $modelObj = $this->modelObj;
        $orderValidator = $modelObj->getOrderValidation($request->input('type'));
        $callback = function ($request) use ($modelObj) {
            return $modelObj->createOrder($request);
        };
        return $this->validateCustom($request, $orderValidator, $callback);
    }
}