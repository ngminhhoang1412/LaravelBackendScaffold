<?php

namespace App\Http\Controllers;

use App\Common\Constant;
use App\Models\User;
use App\Common\Helper;
use App\Models\AbsenceType;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    const TOKEN_NAME = "API TOKEN";

    /**
     * Create User
     * @param Request $request
     * @return Response
     */
    public function createUser(Request $request): Response
    {
        $tableNames = config('permission.table_names');
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'email' => 'required|email|unique:'. User::TABLE_NAME .',email',
                    'password' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return Helper::getResponse(null, $validateUser->errors(), 401);
            }

            $user = User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => Hash::make($request['password'])
            ]);


            // Assign 'guest' role for new user

            $newUserId = DB::table(User::TABLE_NAME)->where('email', '=', $request['email'])->get('id');
            $guestRoleId = DB::table(Role::retrieveTableName())->where('name', '=', 'guest')->get('id');

            DB::table($tableNames['model_has_roles'])
                ->insert([
                    'role_id' => $guestRoleId[0]->id,
                    'model_type' => User::class,
                    'model_id' => $newUserId[0]->id
                ]);

            // Assign the default absence amount for the new user
            DB::table(AbsenceType::retrieveTableName())
                ->where('id', '>', 3)
                ->get('id')->map(function ($value) use ($newUserId) {
                    DB::table(AbsenceType::INTERMEDIATE_TABLES[0])
                        ->insert([
                            'user_id' => $newUserId[0]->id,
                            'absence_type_id' => $value->id,
                            'amount' => AbsenceType::DEFAULT_AMOUNT
                        ]);
                });


            return Helper::getResponse([
                'token' => $this->getToken($user, 'guest')
            ]);
        } catch (\Throwable $th) {
            return Helper::getResponse(null, $th->getMessage());
        }
    }

    /**
     * Login The User
     * @param Request $request
     * @return Response
     */
    public function loginUser(Request $request): Response
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return Helper::getResponse(null, $validateUser->errors(), 401);
            }

            if (!Auth::attempt($request->only(['email', 'password']))) {
                return Helper::getResponse(null, 'Credential not correct', 403);
            }

            $user = User::where('email', $request['email'])->first();

            return Helper::getResponse([
                'token' => $this->getToken($user, $user->role)
            ]);
        } catch (\Throwable $th) {
            return Helper::getResponse(null, $th->getMessage());
        }
    }

    /**
     * @param User $user
     * @param string $role
     * @return mixed|string
     */
    private function getToken(User $user, string $role)
    {
        return explode(
            '|',
            $user->createToken(self::TOKEN_NAME, User::ROLES[$role])->plainTextToken
        )[1];
    }
}
