<?php

namespace App\Http\Controllers;

use App\Common\Helper;
use App\Models\BaseModel;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $model;
    /** @var BaseModel $modelObj */
    public $modelObj;

    public function __construct()
    {
        $this->modelObj = new $this->model;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $modelValidator = call_user_func($this->model . '::getQueryValidator', $request);
        $callback = function ($request) {
            return $this->handleIndex($request);
        };
        return $this->validateCustom($request, $modelValidator, $callback);
    }

    /**
     * Display the specified resource.
     *
     * @param string $id
     * @return Response
     */
    public function show(string $id): Response
    {
        return $this->handleShow($id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return Response
     */
    public function destroy($id): Response
    {
        return $this->handleDestroy($id);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(): Response
    {
        $request = new Request();
        $modelValidator = call_user_func($this->model . '::getStoreValidator', $request);
        return Helper::getResponse($modelValidator);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        $modelValidator = call_user_func($this->model . '::getStoreValidator', $request);
        $callback = function ($request) {
            return $this->handleStore($request);
        };
        return $this->validateCustom($request, $modelValidator, $callback);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $request = new Request();
        $modelValidator = call_user_func($this->model . '::getUpdateValidator', $request, $id);
        return Helper::getResponse($modelValidator);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function update(Request $request, $id): Response
    {
        $modelValidator = call_user_func($this->model . '::getUpdateValidator', $request, $id);
        $callback = function ($request) use ($id) {
            return $this->handleUpdate($request, $id);
        };
        return $this->validateCustom($request, $modelValidator, $callback);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function handleIndex(Request $request): Response
    {
        try {
            $result = $this->modelObj->queryWithCustomFormat($request);
            return Helper::getResponse($result);
        } catch (Exception $e) {
            return Helper::handleApiError($e);
        }
    }

    /**
     * @param $id
     * @return Response
     */
    public function handleShow($id): Response
    {
        try {
            $result = $this->modelObj->showWithCustomFormat($id);
            return Helper::getResponse($result);
        } catch (Exception $e) {
            return Helper::handleApiError($e);
        }
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function handleStore(Request $request): Response
    {
        try {
            $result = $this->modelObj->storeWithCustomFormat($request);
            return Helper::getResponse($result);
        } catch (Exception $e) {
            return Helper::handleApiError($e);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function handleUpdate(Request $request, $id): Response
    {
        try {
            $result = $this->modelObj->updateWithCustomFormat($request, $id);
            return Helper::getResponse($result);
        } catch (Exception $e) {
            return Helper::handleApiError($e);
        }
    }

    /**
     * @param $id
     * @return Response
     */
    public function handleDestroy($id): Response
    {
        try {
            $result = $this->modelObj->destroyWithCustomFormat($id);
            return Helper::getResponse($result);
        } catch (Exception $e) {
            return Helper::handleApiError($e);
        }
    }

    /**
     * @param $input
     * @param $rule
     * @param $callback
     * @return Response
     */
    public function validateCustom($input, $rule, $callback): Response
    {
        $validator = Validator::make($input->all(), $rule);
        try {
            $validator->validate();
            return $callback($input);
        } catch (ValidationException $e) {
            return Helper::getResponse(
                null,
                $validator->errors()->first()
            );
        }
    }
}
