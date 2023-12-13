<?php

namespace App\Http\Controllers\API;

use AmazingTraits\Controllers\API\BaseController as BaseController;
use AmazingTraits\Mail\SendOtpCode;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use AmazingTraits\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Hash;
use Mockery\Exception;
use GuzzleHttp\Client;

class AuthController extends BaseController
{
    public function isActive(string $field, string $identity)
    {
        $user = Auth::user();
        $user = $user->where([
            [$field, '=', $identity]
        ])->first();
        return $user->active_flag;
    }

    public function sendWaNotification($otp_code)
    {
        $url = 'https://corelabs-wa.sytes.net/api/v1/whatsapp/send';
        $data = [
            'group' => 'CalegPlus Dev',
            'phone' => '085736907093',
            "message" => "Kode OTP CalegPlus developer mode anda adalah $otp_code",
            'token' => '2N4mE7rP3xH8L5cQ1vZ6A9wB0GK1oJfDyV7tI8q'
        ];

        $client = new Client();

        try {
            $response = $client->post($url, [
                'json' => $data,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();
        } catch (GuzzleHttp\Exception\RequestException $e) {
            // $e berisi informasi kesalahan
        }
    }
    public function getPermissions($userId)
    {
        $user = DB::table(env('TABLE_PREFIX') . 'm_users')->find($userId);
        // cek active role nya apa, karena use memungkinkan untuk multiple role
        $role = DB::table(env('TABLE_PREFIX') . 'm_roles')->where('name', $user->role)->first();

        $roles_d_users = DB::table(env('TABLE_PREFIX') . 'm_roles_d_users as users')
            ->selectRaw('mpm.name as "permission_name"')
            ->join(env('TABLE_PREFIX') . 'm_roles as roles', 'roles.id', '=', "users." . env('TABLE_PREFIX') . "m_roles_id")
            ->join(env('TABLE_PREFIX') . 'm_roles_d_permissions as pm', "pm." . env('TABLE_PREFIX') . "m_roles_id", '=', 'roles.id')
            ->join(env('TABLE_PREFIX') . 'm_permissions as mpm', 'mpm.id', '=', 'pm.permission_id')
            ->where('users.user_id', $userId)
            ->where('users.' . env('TABLE_PREFIX') . 'm_roles_id', $role->id)
            ->where('pm.deleted_at', '=', null)
            ->where('users.deleted_at', '=', null)
            ->where('roles.deleted_at', '=', null)
            ->get();

        $permissions = [];
        foreach ($roles_d_users as $pm) {
            $permissions[] = $pm->permission_name;
        }
        return $permissions;
    }

    public function getPermissionsV2($userId)
    {
        $tbl_prefix = env('TABLE_PREFIX');
        $version_role = '';
        $user = DB::table($tbl_prefix . 'm_users')->find($userId);
        // cek active role nya apa, karena use memungkinkan untuk multiple role
        $role = DB::table($tbl_prefix . 'm_roles' . $version_role)
            ->where('name', $user->role)->first();

        $roles_d_users = DB::table("{$tbl_prefix}m_roles_d_users{$version_role} as users")
            ->selectRaw('pm.*, menu.uid as "menu.uid", menu.name as "menu.name", menu.module as "menu.module", menu.submodule as "menu.submodule"')
            ->join("{$tbl_prefix}m_roles{$version_role} as roles", 'roles.id', '=', "users.{$tbl_prefix}m_roles{$version_role}_id")
            ->join("{$tbl_prefix}m_roles_d_ui_pc_permissions{$version_role} as pm", "pm.{$tbl_prefix}m_roles{$version_role}_id", '=', 'roles.id')
            ->join("{$tbl_prefix}m_menu as menu", 'menu.id', '=', 'pm.menu_id')
            ->where('users.user_id', $userId)
            ->where("users.{$tbl_prefix}m_roles{$version_role}_id", $role->id)
            ->where('pm.deleted_at', '=', null)
            ->where('users.deleted_at', '=', null)
            ->where('roles.deleted_at', '=', null)
            ->get();

        $permissions = [];
        $default_permissions = ['view', 'preview', 'create', 'edit', 'delete'];
        foreach ($roles_d_users as $pm) {
            $customs = json_decode($pm->customs);
            $module = $pm->{"menu.module"} ? str_replace(' ', '_', strtolower($pm->{"menu.module"})) : '';
            $submodule = $pm->{"menu.submodule"} ? str_replace(' ', '_', strtolower($pm->{"menu.submodule"})) : '';
            $name = $pm->{"menu.name"} ? str_replace(' ', '_', strtolower($pm->{"menu.name"})) : '';
            $uid = $pm->{"menu.uid"};

            foreach ($default_permissions as $dm) {
                if ($pm->{$dm} === 1) {
                    // if ($name && $module && $submodule) {
                    //     $permissions[] = "{$module}-{$submodule}-{$name}-{$dm}";
                    // } else if ($name && $module && !$submodule) {
                    //     $permissions[] = "{$module}-{$name}-{$dm}";
                    // } else if ($name && !$module && !$submodule) {
                    //     $permissions[] = "{$name}-{$dm}";
                    // }

                    $permissions[] = "{$uid}-{$dm}";
                }
            }

            foreach ($customs as $custom) {
                if ($custom->allow) {
                    $customName = str_replace(' ', '_', strtolower($custom->name));
                    if ($name && $module && $submodule) {
                        $permissions[] = "{$module}-{$submodule}-{$name}-{$customName}";
                    } else if ($name && $module && !$submodule) {
                        $permissions[] = "{$module}-{$name}-{$customName}";
                    } else if ($name && !$module && !$submodule) {
                        $permissions[] = "{$name}-{$customName}";
                    }
                }
            }
        }
        return $permissions;
    }

    public function getPermissionsV2_mb($userId)
    {
        $tbl_prefix = env('TABLE_PREFIX');
        $version_role = '';
        $user = DB::table($tbl_prefix . 'm_users')->find($userId);
        // cek active role nya apa, karena use memungkinkan untuk multiple role
        $role = DB::table($tbl_prefix . 'm_roles' . $version_role)
            ->where('name', $user->role)->first();

        $roles_d_users = DB::table("{$tbl_prefix}m_roles_d_users{$version_role} as users")
            ->selectRaw('pm.*')
            ->join("{$tbl_prefix}m_roles{$version_role} as roles", 'roles.id', '=', "users.{$tbl_prefix}m_roles{$version_role}_id")
            ->join("{$tbl_prefix}m_roles_d_ui_mb_permissions{$version_role} as pm", "pm.{$tbl_prefix}m_roles{$version_role}_id", '=', 'roles.id')
            ->where('users.user_id', $userId)
            ->where("users.{$tbl_prefix}m_roles{$version_role}_id", $role->id)
            ->where('pm.deleted_at', '=', null)
            ->where('users.deleted_at', '=', null)
            ->where('roles.deleted_at', '=', null)
            ->get();

        $permissions = [];
        $default_permissions = ['view', 'preview', 'create', 'edit', 'delete'];
        foreach ($roles_d_users as $pm) {
            $customs = json_decode($pm->customs);
            $module = $pm->module ? str_replace(' ', '_', strtolower($pm->module)) : '';
            $submodule = $pm->submodule ? str_replace(' ', '_', strtolower($pm->submodule)) : '';
            $name = $pm->name ? str_replace(' ', '_', strtolower($pm->name)) : '';
            foreach ($default_permissions as $dm) {
                if ($pm->{$dm} === 1) {
                    if ($name && $module && $submodule) {
                        $permissions[] = "{$module}-{$submodule}-{$name}-{$dm}";
                    } else if ($name && $module && !$submodule) {
                        $permissions[] = "{$module}-{$name}-{$dm}";
                    } else if ($name && !$module && !$submodule) {
                        $permissions[] = "{$name}-{$dm}";
                    }
                }
            }

            foreach ($customs as $custom) {
                if ($custom->allow) {
                    $customName = str_replace(' ', '_', strtolower($custom->name));
                    if ($name && $module && $submodule) {
                        $permissions[] = "{$module}-{$submodule}-{$name}-{$customName}";
                    } else if ($name && $module && !$submodule) {
                        $permissions[] = "{$module}-{$name}-{$customName}";
                    } else if ($name && !$module && !$submodule) {
                        $permissions[] = "{$name}-{$customName}";
                    }
                }
            }
        }
        return $permissions;
    }

    public function getRoles($userId): array
    {
        $data = DB::table(env('TABLE_PREFIX') . 'm_roles_d_users as users')
            ->selectRaw('roles.name')
            ->join(env('TABLE_PREFIX') . 'm_roles as roles', 'roles.id', '=', "users." . env('TABLE_PREFIX') . "m_roles_id")
            ->groupBy('roles.id')
            ->where('users.user_id', $userId)
            ->get();

        $roles = [];
        foreach ($data as $dt) {
            $roles[] = $dt->name;
        }
        return $roles;
    }

    public function syncronize_current_device($userId = null, $device)
    {
        DB::beginTransaction();
        try {
            $table_prefix = env('TABLE_PREFIX');
            $user = new \App\Models\m_users();
            $history = DB::table($table_prefix . 'm_users_d_device_histories');
            $search = $history;
            $search = $search
                ->where('cp_m_users_id', $userId)
                ->where('uuid', $device['uuid']);

            if (!$search->exists()) {
                // berarti belum ada history device yang mirip dgn apa yang dikirimkan
                $beforeCreate = $device;
                $beforeCreate['cp_m_users_id'] = $userId;
                $beforeCreate['current'] = true;
                $beforeCreate['created_id'] = $userId;
                $beforeCreate['created_at'] = now();

                $history->insert($beforeCreate);
            } else {
                // jika sudah ada
                $history_prevs = DB::table($table_prefix . 'm_users_d_device_histories');
                $history_prevs = $history_prevs
                    ->where('cp_m_users_id', $userId)
                    ->get();

                // current di false kan semua dulu
                foreach ($history_prevs as $prev) {
                    DB::table($table_prefix . 'm_users_d_device_histories')
                        ->where('id', $prev->id)
                        ->update([
                            'current' => false,
                        ]);
                }

                // need check when version not match with current device
                $version_check = DB::table($table_prefix . 'm_users_d_device_histories')
                    ->where('cp_m_users_id', $userId)
                    ->where('uuid', $device['uuid'])
                    ->where('version', $device['version']);

                $app_version_check = DB::table($table_prefix . 'm_users_d_device_histories')
                    ->where('cp_m_users_id', $userId)
                    ->where('uuid', $device['uuid'])
                    ->where('app_version', $device['app_version']);

                if (!$version_check->first() || !$app_version_check->first()) {
                    // when os version or app version has updated
                    $beforeCreate = $device;
                    $beforeCreate['cp_m_users_id'] = $userId;
                    $beforeCreate['current'] = true;
                    $beforeCreate['created_id'] = $userId;
                    $beforeCreate['created_at'] = now();
                    $beforeCreate['updated_id'] = $userId;
                    $beforeCreate['updated_at'] = now();

                    $history->insert($beforeCreate);
                } else {
                    // update current with uuid
                    DB::table($table_prefix . 'm_users_d_device_histories')
                        ->where('uuid', $device['uuid'])
                        ->update([
                            'current' => true
                        ]);
                }
            }

            $user->where('id', $userId)
                ->update([
                    'device' => json_encode($device)
                ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendError('Login failed.', [], 500);
        }
    }

    public function save_current_position($userId, $point)
    {
        $explode = explode(',', $point);
        $val = DB::raw("POINT($explode[1], $explode[0])");
        $table_prefix = env('TABLE_PREFIX');
        DB::table($table_prefix . 'm_users_d_login_histories')
            ->insert([
                "{$table_prefix}m_users_id" => $userId,
                'longlat_point' => $val,
                'created_id' => $userId,
                'created_at' => now()
            ]);
    }

    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->identity, 'password' => $request->password, 'deleted_at' => NULL])) {
            $is_active = $this->isActive('email', $request->identity);
            if (!$is_active) {
                return $this->sendError('Account inactive.', ['error' => 'Account inactive'], 403);
            }
            $user = Auth::user();
            $success['name'] = $user->name;
            $success['username'] = $user->username;
            $success['email'] = $user->email;
            $success['phone'] = $user->phone;
            $success['token'] =  $user->createToken('MyApp')->accessToken;
            $success['id'] = $user->id;
            $success['token_type'] = 'Bearer';
            $success['permissions'] = $this->getPermissionsV2($user->id);
            $success['permissions_mb'] = $this->getPermissionsV2_mb($user->id);

            return $this->sendResponse($success, 'User login successfully email.');
        } else if (Auth::attempt(['username' => $request->identity, 'password' => $request->password, 'deleted_at' => NULL])) {
            $is_active = $this->isActive('username', $request->identity);
            if (!$is_active) {
                return $this->sendError('Account inactive.', ['error' => 'Account inactive'], 403);
            }
            $user = Auth::user();
            $success['name'] = $user->name;
            $success['username'] = $user->username;
            $success['email'] = $user->email;
            $success['phone'] = $user->phone;
            $success['token'] =  $user->createToken('MyApp')->accessToken;
            $success['id'] = $user->id;
            $success['token_type'] = 'Bearer';
            $success['permissions'] = $this->getPermissionsV2($user->id);
            $success['permissions_mb'] = $this->getPermissionsV2_mb($user->id);

            return $this->sendResponse($success, 'User login successfully with username.');
        } else if (Auth::attempt(['phone' => $request->identity, 'password' => $request->password, 'deleted_at' => NULL])) {
            $is_active = $this->isActive('phone', $request->identity);
            if (!$is_active) {
                return $this->sendError('Account inactive.', ['error' => 'Account inactive'], 403);
            }
            $user = Auth::user();
            $success['name'] = $user->name;
            $success['username'] = $user->username;
            $success['email'] = $user->email;
            $success['phone'] = $user->phone;
            $success['token'] =  $user->createToken('MyApp')->accessToken;
            $success['id'] = $user->id;
            $success['token_type'] = 'Bearer';
            $success['permissions'] = $this->getPermissionsV2($user->id);
            $success['permissions_mb'] = $this->getPermissionsV2_mb($user->id);

            if (isset($request->device)) {
                $validator = Validator::make($request->device, [
                    'uuid' => 'required|string|max:150',
                    'brand' => 'required|string|string|max:20',
                    'os' => 'required|string|max:20',
                    'version' => 'required|string|max:20',
                    'app_version' => 'nullable|string|max:10'
                ]);

                if ($validator->fails()) {
                    return $this->sendError('Validation Error.', $validator->errors()->all(), 400);
                }

                $this->syncronize_current_device($user->id, $request->device);
            }

            if (isset($request->longlat_point)) {
                $this->save_current_position($user->id, $request->longlat_point);
            }

            return $this->sendResponse($success, 'User login successfully with username.');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 403);
        }
    }

    public function recordAction($userId)
    {
        $request = app()->request;
        if (isset($request->device)) {
            $validator = Validator::make($request->device, [
                'uuid' => 'required|string|max:150',
                'brand' => 'required|string|string|max:20',
                'os' => 'required|string|max:20',
                'version' => 'required|string|max:20',
                'app_version' => 'nullable|string|max:10'
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors()->all(), 400);
            }

            $this->syncronize_current_device($userId, $request->device);
        }
    }

    public function login_mobile(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->email, 'deleted_at' => NULL])) {
            $user = Auth::user();
            $is_active = $this->isActive('email', $request->email);
            if (!$is_active) {
                return $this->sendError('Account inactive.', ['error' => 'Account inactive'], 403);
            }
            $otp_code = random_int(100000, 999999);
            $input = [
                'otp_code' => $otp_code,
                'updated_id' => 1,
                'updated_at' => now(),
            ];

            DB::table(env('TABLE_PREFIX') . 'm_users')->where('email', $request->email)->update($input);

            $this->recordAction($user->id);

            Cache::put("otp_code_$otp_code", true, 86400);
            Cache::put("otp_code_{$otp_code}_data", $user->id, 86400);
            Cache::put("otp_code_{$otp_code}_for", 'signin', 86400);


            if (env('APP_ENV') == 'production'){
                $this->sendWaNotification($otp_code);
                $data = [
                    'otp_code' => $otp_code,
                    'mode' => 'register',
                    'click_by_link' => false
                ];
                Mail::to($request->email)->send(new SendOtpCode($data));

                return $this->sendResponse(
                    null,
                    'Silahkan cek email anda untuk melihat kode otp yang kami kirimkan'
                );
            } else {
                return $this->sendResponse(
                    null,
                    "Kode otp anda adalah $otp_code"
                );
            }
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 403);
        }
    }

    public function otp_verification(Request $request, $otp_code)
    {
        DB::beginTransaction();
        try {
            if (Cache::has("otp_code_$otp_code")) {
                $for = Cache::get("otp_code_{$otp_code}_for");
                $userId = Cache::get("otp_code_{$otp_code}_data");
                if ($for === 'signup') {
                    $userId = Cache::get("otp_code_{$otp_code}_data");
                    DB::table(env('TABLE_PREFIX') . 'm_users')
                        ->where('id', $userId)
                        ->update([
                            "status" => "VERIFIED",
                            'email_verified_at' => now(),
                            'updated_at' => now()
                        ]);

                    $user = DB::table(env('TABLE_PREFIX') . 'm_users')
                        ->where('id', $userId)
                        ->first();

                    $role = DB::table('cp_m_roles')->where('name', $user->role)->first();
                    DB::table('cp_m_roles_d_users')->insert([
                        'cp_m_roles_id' => $role->id,
                        'user_id' => $userId,
                        'created_id' => 1,
                        'created_at' => Carbon::now()
                    ]);

                    Cache::put("otp_code_$otp_code", null);
                    Cache::put("otp_code_{$otp_code}_data", null);
                    Auth::loginUsingId($userId);
                    $user = Auth::user();
                    $success['name'] = $user['name'];
                    $success['gender'] = $user['gender'];
                    $success['no_kta'] = $user['no_kta'];
                    $success['birth_date'] = $user['birth_date'];
                    $success['avatar'] = $user['avatar'];
                    $success['email'] = $user['email'];
                    $success['phone'] = $user['phone'];
                    $success['token'] =  $user->createToken('MyApp')->accessToken;
                    $success['id'] = $user['id'];
                    $success['token_type'] = 'Bearer';
                    $success['permissions'] = $this->getPermissionsV2_mb($user['id']);
                    DB::commit();
                    return $this->sendResponse($success, "Email has been sucessfully to verified");
                } else if ($for === 'signin') {
                    Cache::put("otp_code_$otp_code", null);
                    Cache::put("otp_code_{$otp_code}_data", null);
                    $user = DB::table(env('TABLE_PREFIX') . 'm_users')->where('otp_code', $otp_code);
                    $data = $user->first();

                    if (!$data) {
                        DB::rollBack();
                        return $this->sendError('OTP Code is invalid', [], 400);
                    }

                    if (Auth::attempt(['email' => $data->email, 'password' => $data->email]) || Auth::loginUsingId($userId)) {
                        $user = Auth::user();
                        $success['name'] = $user['name'];
                        $success['gender'] = $user['gender'];
                        $success['no_kta'] = $user['no_kta'];
                        $success['birth_date'] = $user['birth_date'];
                        $success['avatar'] = $user['avatar'];
                        $success['email'] = $user['email'];
                        $success['phone'] = $user['phone'];
                        $success['token'] =  $user->createToken('MyApp')->accessToken;
                        $success['id'] = $user['id'];
                        $success['token_type'] = 'Bearer';
                        $success['permissions'] = $this->getPermissionsV2_mb($user['id']);
                        DB::commit();
                        return $this->sendResponse($success, "The email is verified");
                    }
                } else {
                    DB::rollBack();
                    return $this->sendError('OTP Code is invalid', [], 400);
                }
            } else {
                DB::rollBack();
                return $this->sendError('OTP Code is invalid', [], 400);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendError('Signup failed.', [], 500);
        }
    }

    public function verification_account(Request $request)
    {
        $check_email = DB::table(env('TABLE_PREFIX') . 'm_users')->where('email', $request->identity);
        $check_phone = DB::table(env('TABLE_PREFIX') . 'm_users')->where('phone', $request->identity);

        if ($check_email->exists()) {
            // $success['token'] =  $user->createToken('MyApp')->accessToken;
            // $success['token_type'] = 'Bearer';

            $user = $check_email->first();

            $otp_code = random_int(100000, 999999);

            DB::table(env('TABLE_PREFIX') . 'm_users')->where('id', $user->id)->update([
                'otp_code' => $otp_code
            ]);

            Cache::put("otp_code_$otp_code", true, 86400);
            Cache::put("otp_code_{$otp_code}_data", $user->id, 86400);
            Cache::put("otp_code_{$otp_code}_for", 'verification', 86400);
            $data = [
                'otp_code' => $otp_code,
                'mode' => 'verification'
            ];

            if (env('APP_ENV') !== 'local') {
                Mail::to($request->identity)->send(new SendOtpCode($data));
            }

            DB::commit();
            return $this->sendResponse(null, 'Verification code has been sent to your email.');
        } else if ($check_phone->exists()) {
            // $success['token'] =  $user->createToken('MyApp')->accessToken;
            // $success['token_type'] = 'Bearer';

            $user = $check_phone->first();

            $otp_code = random_int(100000, 999999);
            $hasError = false;

            DB::table(env('TABLE_PREFIX') . 'm_users')->where('phone', $request->identity)->update([
                'otp_code' => $otp_code
            ]);

            Cache::put("otp_code_$otp_code", true, 86400);
            Cache::put("otp_code_{$otp_code}_data", $user->id, 86400);
            Cache::put("otp_code_{$otp_code}_for", 'verification', 86400);
            $send = $this->sendWhatsappText($user->phone, 'JANGAN MEMBERITAHU KODE RAHASIA INI KE SIAPAPUN termasuk pihak Tokopedia. WASPADA TERHADAP KASUS PENIPUAN! KODE VERIFIKASI untuk masuk: ' . $otp_code);


            // dd($send);

            // if (!$send->status) {
            //     $hasError = true;
            // }

            // if (!$hasError) {
            //     DB::commit();
            //     return $this->sendResponse(
            //         null,
            //         'Please check your whatsapp to get OTP Code.'
            //     );
            // } else {
            //     DB::rollBack();
            //     return $this->sendError('Verification failed, please try again.', [], 500);
            // }
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 403);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'gender' => 'required|in:Pria,Wanita',
            'birth_date' => 'required|date',
            'nik' => 'required|string|max:16',
            'phone' => 'required|string|unique:' . env('TABLE_PREFIX') . 'm_users,phone',
            'email' => 'required|email|unique:' . env('TABLE_PREFIX') . 'm_users,email',
            'agama' => 'required|string|max:25',
            'partai_id' => 'required|integer',
            'no_kta' => 'nullable|string|max:25',
            'tipe_caleg_id' => 'required|integer',
            'dapil_id' => 'required|integer',
            'nomor_urut' => 'required|integer',
            'prov_id' => 'required|integer',
            'kab_id' => 'required|integer',
            'kec_id' => 'required|integer',
            'kel_id' => 'required|integer',
            'koordinat_point' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->all(), 400);
        }

        DB::beginTransaction();
        try {
            $input = Arr::except($request->all(), ['partai_id', 'tipe_caleg_id', 'dapil_id', 'nomor_urut', 'prov_id', 'kab_id', 'kec_id', 'kel_id', 'koordinat_point']);
            $input['role'] = 'CALEG';
            $input['roles'] = json_encode(['CALEG']);
            $input['password'] = Hash::make($input['email']);
            $input['status'] = 'PENDING';
            $input['created_at'] = now();
            $input = Arr::except($input, ['c_password']);
            $otp_code = random_int(100000, 999999);
            $input['otp_code'] = $otp_code;
            $input['created_id'] = 1;
            $user = DB::table(env('TABLE_PREFIX') . 'm_users')->insertGetId($input);

            $dataCaleg = Arr::only($request->all(), ['partai_id', 'dapil_id', 'tipe_caleg_id', 'nomor_urut', 'prov_id', 'kab_id', 'kec_id', 'kel_id', 'koordinat_point']);
            $coordinates = explode(',', $dataCaleg['koordinat_point']);
            $latitude = trim($coordinates[0]);
            $longitude = trim($coordinates[1]);
            $dataCaleg['koordinat_point'] = DB::raw("POINT($longitude, $latitude)");
            $dataCaleg['cp_m_users_id'] = $user;
            $dataCaleg['created_id'] = 1;
            DB::table('cp_m_users_d_caleg')->insert($dataCaleg);

            $user = json2arr(DB::table(env('TABLE_PREFIX') . 'm_users')->find($user));
            Cache::put("otp_code_$otp_code", true, 86400);
            Cache::put("otp_code_{$otp_code}_data", $user['id'], 86400);
            Cache::put("otp_code_{$otp_code}_for", 'signup', 86400);
            $data = [
                'otp_code' => $otp_code,
                'mode' => 'register'
            ];

            if (env('APP_ENV') !== 'local') {
                $this->sendWaNotification($otp_code);
                Mail::to($request->email)->send(new SendOtpCode($data));
            }

            DB::commit();
            return $this->sendResponse(
                Arr::only($user, [
                    'name',
                    'email',
                    'phone',
                    'created_at',
                ]),
                'Pendaftaran berhasil. Silakan cek email Anda untuk mendapatkan Kode OTP dan akan kedaluwarsa setelah 24 jam. atau gunakan kode ini ' . $otp_code
            );
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendError('Pendaftaran gagal.', [], 500);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return $this->sendResponse(null, 'Pengguna telah berhasil logout.');
    }

    public function encrypt(string|int $id)
    {
        try {
            $token = Auth::user()->token()->id;
            $userId = Auth::user()->id;
            $tokenLength = strlen($token);

            $rand = random_int(1, $tokenLength < 4 ? 4 : $tokenLength - 4);

            $randString = substr($token, $rand, 4);
            $intRandString = filter_var($randString, FILTER_SANITIZE_NUMBER_INT);

            if (!is_numeric($intRandString)) {
                $intRandString = 0;
            }
            $encrypted = $randString . ($id + $userId + $intRandString);
            return Str::substrReplace($encrypted, '_', random_int(0, 4), 0);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function me(Request $req = null)
    {
        $data = $req ? $req->user() : app()->request->user();
        $users = new \App\Models\m_users();
        $data = $users->arr2json($data);
        $data->permissions = $this->getPermissionsV2($data->id);
        $data->permissions_mb = $this->getPermissionsV2_mb($data->id);
        $detail = null;
        if ($data->role === 'CALEG') {
            $detail = DB::table('cp_m_users_d_caleg as d_caleg')
                ->selectRaw('d_caleg.*, gen.value_1 as tipe_caleg')
                ->join('cp_m_general as gen', 'gen.id', '=', 'd_caleg.tipe_caleg_id')
                ->where('cp_m_users_id', $data->id)
                ->whereNull('d_caleg.deleted_at')
                ->first();
            $data->tipe_caleg = $detail->tipe_caleg;
        }

        $data->id = app()->request->encrypt === 'false' ? $data->id : $users->encrypt($data->id);
        $data->roles = json_decode($data->roles);

        return $this->sendResponse($data);
    }

    public function change_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->all(), 400);
        }

        DB::beginTransaction();
        try {
            DB::table(env('TABLE_PREFIX') . 'm_users')
                ->where('id', $request->user()->id)
                ->update(['password' => bcrypt($request->password)]);

            DB::commit();
            return $this->sendResponse(null, 'Kata sandi telah berhasil diperbarui!', 201);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendError('Pembaruan kata sandi gagal.', [], 500);
        }
    }

    public function signup_verification_html(Request $request, $otp_code)
    {
        DB::beginTransaction();
        try {
            if (Cache::has("otp_code_$otp_code")) {
                $for = Cache::get("otp_code_{$otp_code}_for");
                if ($for === 'signup') {
                    $userId = Cache::get("otp_code_{$otp_code}_data");
                    $user = DB::table(env('TABLE_PREFIX') . 'm_users')
                        ->where('id', $userId)
                        ->update([
                            "status" => "VERIFIED",
                            'email_verified_at' => now(),
                            'updated_at' => now()
                        ]);

                    Cache::put("otp_code_$otp_code", null);
                    Cache::put("otp_code_{$otp_code}_data", null);
                    DB::commit();
                    return view('emails.signup-verification');
                } else {
                    DB::rollBack();
                    return view('emails.signup-verification-invalid');
                }
            } else {
                DB::rollBack();
                return view('emails.signup-verification-invalid');
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendError('Register failed.', [], 500);
        }
    }

    public function signup_resend_otp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identity' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->all(), 400);
        }

        $user = DB::table(env('TABLE_PREFIX') . 'm_users')
            ->orWhere('email', $request->identity)
            ->orWhere('username', $request->identity)
            ->first();

        if ($user) {
            if ($user->status === 'PENDING') {
                $user = $user->first();
                $otp_code = random_int(100000, 999999);
                Cache::put("otp_code_$otp_code", true, 86400);
                Cache::put("otp_code_{$otp_code}_data", $user->id, 86400);
                Cache::put("otp_code_{$otp_code}_for", 'signup', 86400);
                $data = [
                    'otp_code' => $otp_code,
                    'mode' => 'register'
                ];
                Mail::to($user->email)->send(new SendOtpCode($data));
                return $this->sendResponse(
                    null,
                    'Please check your email to get OTP Code and will be expired after 24 hours.'
                );
            } else {
                return $this->sendError('The user is already registered and there is no need to re-send the OTP Code', [], 400);
            }
        } else {
            return $this->sendError('Invalid credentials.', [], 401);
        }
    }

    public function forgot_password_web(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identity' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->all(), 400);
        }

        $user = DB::table(env('TABLE_PREFIX') . 'm_users')
            ->orWhere('email', $request->identity)
            ->orWhere('phone', $request->identity);

        if ($user->exists()) {
            $user = $user->first();
            $otp_code = random_int(100000, 999999);

            DB::table(env('TABLE_PREFIX') . 'm_users')->where('id', $user->id)->update([
                'otp_code' => $otp_code
            ]);

            Cache::put("otp_code_$otp_code", true, 86400);
            Cache::put("otp_code_{$otp_code}_data", $user->id, 86400);
            Cache::put("otp_code_{$otp_code}_for", 'forgot_password', 86400);
            $data = [
                'otp_code' => $otp_code,
                'mode' => 'forgot-password'
            ];

            if ($request->identity == $user->phone) {
                $url = env("APP_URL");
                $phone = $user->phone;

                if ($phone[0] === '0') {
                    // Ganti karakter pertama dengan '62'
                    $phone = '62' . substr($phone, 1);
                }
                sendWhatsappText($phone, "JANGAN MEMBERITAHUKAN LINK DENGAN OTP CODE INI KE SIAPAPUN TERMASUK PIHAK IHOME. WASPADA TERHADAP KASUS PENIPUAN! KLIK LINK VERIFIKASI DI BAWAH UNTUK MELANJUTKAN PENGGANTIAN PASSWORD: $url/forgot-password-verification?otp_code=$otp_code");
            } else if ($request->identity == $user->email) {
                if (env('APP_ENV') !== 'local') {
                    Mail::to($user->email)->send(new SendOtpCode($data));
                }
            }

            return $this->sendResponse(
                null,
                'Please check your email to get OTP Code and will be expired after 24 hours.'
            );
        } else {
            return $this->sendError('Invalid credentials.', [], 401);
        }
    }

    public function forgot_verification(Request $request, $otp_code)
    {
        DB::beginTransaction();
        try {
            // dd(Cache::has("otp_code_$otp_code"));
            if (Cache::has("otp_code_$otp_code")) {
                $for = Cache::get("otp_code_{$otp_code}_for");
                $user = DB::table(env('TABLE_PREFIX') . 'm_users')->where('otp_code', $otp_code)->first();
                if ($for === 'forgot_password') {
                    if ($user !== null) {
                        return $this->sendResponse($otp_code, 'Email or Phone has been sucessfully to verified');
                    } else {
                        dd("error");
                        DB::rollBack();
                        return $this->sendError('OTP Code is invalid', [], 400);
                    }
                } else {
                    DB::rollBack();
                    return $this->sendError('OTP Code is invalid', [], 400);
                }
            } else {
                DB::rollBack();
                return $this->sendError('OTP Code is invalid', [], 400);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendError('Verification failed.', [], 500);
        }
    }

    public function change_password_web(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->all(), 400);
        }

        $user = DB::table(env('TABLE_PREFIX') . 'm_users')->where('otp_code', $request->otp_code)->first();
        Cache::put("otp_code_$request->otp_code", null);
        Cache::put("otp_code_{$request->otp_code}_data", null);
        DB::table(env('TABLE_PREFIX') . 'm_users')->where('id', $user->id)->update([
            'password' => bcrypt($request->password),
            'otp_code' => null
        ]);
        return $this->sendResponse(null, 'Password has been successfully to update!', 201);
    }

}
