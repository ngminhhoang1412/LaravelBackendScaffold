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
use Throwable;

class AuthController extends Controller
{
    const TOKEN_NAME = "API TOKEN";

    /**
     * @throws GuzzleException
     */
    public function sendRegisterMail(Request $request)
    {
        $htmlFilePath = base_path() . '\resources/html/mail.php';
        $htmlContent = file_get_contents($htmlFilePath);
        $email = $request->get('email');
        $user = User::where('email',$email)->first();
        $link = env('FE_LINK'). '?email='. $email . '&otp='. $user->otp;
        $htmlContent = str_replace('{{link}}', $link, $htmlContent);
        Mail::sendMail($email, 'Socapp - Activate your account', $htmlContent);
    }

    public function checkExpiredTime(Request $request)
    {
        try {
            $currentTime = new DateTime();
            $user = User::where('email', $request->get('email'))->first();
            $startTimeObj = DateTime::createFromFormat('Y-m-d H:i:s', $user->last_sent);
            $endTimeObj = DateTime::createFromFormat('Y-m-d H:i:s', $currentTime->format('Y-m-d H:i:s'));
            $duration = $endTimeObj->getTimestamp() - $startTimeObj->getTimestamp();
            $expired = Constant::MAIL_EXPIRED_TIME - $duration;
            if ($expired > 0){
                return Helper::getResponse($expired);
            }
            else
                return Helper::getResponse(null,'Time out', 408);
        } catch (Throwable $th) {
            return Helper::handleApiError($th);
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
                return Helper::getResponse(null, $validateUser->errors(), 400);
            }
            $currentTime = new DateTime();
            $email = $request->get('email');
            $user = User::where('email', $email)->first();
            $startTimeObj = DateTime::createFromFormat('Y-m-d H:i:s', $user->last_sent);
            $endTimeObj = DateTime::createFromFormat('Y-m-d H:i:s', $currentTime->format('Y-m-d H:i:s'));
            $duration = $endTimeObj->getTimestamp() - $startTimeObj->getTimestamp();
            if ($duration > Constant::MAIL_EXPIRED_TIME) {
                return Helper::getResponse(null, 'Request Timeout',408);
            }
            if ($request->get('otp') == $user->otp) {
                User::where('email', $email)
                    ->update([
                        'confirm_email' => true
                    ]);
                return Helper::getResponse('Verify Success');
            } else {
                User::where('email',$email)
                    ->update(['otp' => base64_encode(random_bytes(Constant::OTP_LENGTH))]);
                return Helper::getResponse(null, 'OTP changed', 409);
            }
        } catch (Throwable $th) {
            return Helper::handleApiError($th);
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
                'otp' => base64_encode(random_bytes(Constant::OTP_LENGTH)),
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
                'last_sent' => new DateTime()
            ]);

            $newUserId = DB::table(User::TABLE_NAME)->where('email', '=', $request['email'])->get('id');
            $guestRoleId = DB::table(Role::retrieveTableName())->where('name', '=', 'guest')->get('id');

            DB::table($tableNames['model_has_roles'])
                ->insert([
                    'role_id' => $guestRoleId[0]->id,
                    'model_type' => User::class,
                    'model_id' => $newUserId[0]->id
                ]);

            $this->sendRegisterMail($request);

            return Helper::getResponse([
                'register success'
            ]);
        } catch (Throwable $th) {
            return Helper::handleApiError($th);
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
                return Helper::getResponse(null, 'Please confirm your email', 400);
            }

            return Helper::getResponse([
                'token' => $this->getToken($user, $user->role),
                'user' => $user
            ]);
        } catch (Throwable $th) {
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
