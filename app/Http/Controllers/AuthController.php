<?php

namespace App\Http\Controllers;

use App\Common\Constant;
use App\Mail\Mail;
use App\Models\User;
use App\Common\Helper;
use App\Models\Role;
use DateTime;
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
        $htmlFilePath = base_path() . '\resources/html/mail.php';
        $htmlContent = file_get_contents($htmlFilePath);
        $email = $request->get('email');
        $user = User::where('email', $request->get('email'))->first();
        $link = env('FE_LINK'). '?email='. $email . '&otp='. $user->otp;
        $htmlContent = str_replace('{{link}}', $link, $htmlContent);
        Mail::sendMail($request->get('email'), 'test', $htmlContent);
    }

    public function expiredTime(Request $request)
    {
        try {
            $currentTime = new \DateTime();
            $user = User::where('email', $request->get('email'))->first();
            $startTimeObj = DateTime::createFromFormat('Y-m-d H:i:s', $user->last_sent);
            $endTimeObj = DateTime::createFromFormat('Y-m-d H:i:s', $currentTime->format('Y-m-d H:i:s'));
            $duration = $endTimeObj->getTimestamp() - $startTimeObj->getTimestamp();
            $expired = Constant::Expired_Mail_Time - $duration;
            if ($expired > 0){
                return Helper::getResponse($expired);
            }
            else
                return Helper::getResponse(null,'experienced');
        } catch (\Throwable $th) {
            return Helper::getResponse(null, $th->getMessage());
        }
    }

    public function confirmEmail(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'email' => 'required',
                    'otp' => 'required',
                ]
            );

            if ($validateUser->fails()) {
                return Helper::getResponse(null, $validateUser->errors(), 401);
            }
            $currentTime = new \DateTime();
            $user = User::where('email', $request->get('email'))->first();
            $startTimeObj = DateTime::createFromFormat('Y-m-d H:i:s', $user->last_sent);
            $endTimeObj = DateTime::createFromFormat('Y-m-d H:i:s', $currentTime->format('Y-m-d H:i:s'));
            $duration = $endTimeObj->getTimestamp() - $startTimeObj->getTimestamp();
            if ($duration > Constant::Expired_Mail_Time) {
                return Helper::getResponse(null, 'expired time, please check mail again');
            }
            if ($request->get('otp') == $user->otp) {
                User::where('email', $request->get('email'))
                    ->update([
                        'confirm_email' => true
                    ]);
                return Helper::getResponse('confirm success');
            } else {
                User::where('email', $request->get('email'))
                    ->update(['otp' => base64_encode(random_bytes(10))]);
                return Helper::getResponse(null, 'otp change');
            }
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
                    'email' => 'required|email|unique:' . User::TABLE_NAME . ',email',
                    'password' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return Helper::getResponse(null, $validateUser->errors(), 401);
            }
            User::create([
                'otp' => base64_encode(random_bytes(10)),
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
                'last_sent' => new \DateTime()
            ]);

            $newUserId = DB::table(User::TABLE_NAME)->where('email', '=', $request['email'])->get('id');
            $guestRoleId = DB::table(Role::retrieveTableName())->where('name', '=', 'guest')->get('id');

            DB::table($tableNames['model_has_roles'])
                ->insert([
                    'role_id' => $guestRoleId[0]->id,
                    'model_type' => User::class,
                    'model_id' => $newUserId[0]->id
                ]);

            $this->sendMailRegister($request);

            return Helper::getResponse([
                'register success'
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

            if (!$user->confirm_email) {
                return Helper::getResponse(null, 'Please create account', 401);
            }

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
