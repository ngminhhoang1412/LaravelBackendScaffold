<?php

namespace App\Http\Controllers;

use App\Common\Constant;
use App\Mail\Mail;
use App\Models\User;
use App\Common\Helper;
use App\Models\Role;
use GuzzleHttp\Exception\GuzzleException;
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
     * @throws GuzzleException
     */
    public function sendMailRegister(Request $request)
    {
        $htmlFilePath = base_path().'\app\Mail\html\mail.html';
        $htmlContent = file_get_contents($htmlFilePath);
        Mail::sendMail($request->get('email'), 'test', $htmlContent);
    }

    public function confirmEmail(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'otp' => 'required',
                ]
            );

            if ($validateUser->fails()) {
                return Helper::getResponse(null, $validateUser->errors(), 401);
            }

            $user = User::updated();

            return Helper::getResponse([
                'token' => $this->getToken($user, 'guest')
            ]);
        } catch (\Throwable $th) {
            return Helper::getResponse(null, $th->getMessage());
        }
    }

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

            $newUserId = DB::table(User::TABLE_NAME)->where('email', '=', $request['email'])->get('id');
            $guestRoleId = DB::table(Role::retrieveTableName())->where('name', '=', 'guest')->get('id');

            DB::table($tableNames['model_has_roles'])
                ->insert([
                    'role_id' => $guestRoleId[0]->id,
                    'model_type' => User::class,
                    'model_id' => $newUserId[0]->id
                ]);

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
                'token' => $this->getToken($user, $user->role),
                'user' => $user
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
