<?php

use App\Http\Controllers\API\AuthController;
use AmazingTraits\Controllers\API\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//API LOGIN, REGISTER, LOGOUT
Route::post('signin', [AuthController::class, 'login']);
Route::post('signin-mobile', [AuthController::class, 'login_mobile']);
Route::get('otp-verification/{otp_code}', [AuthController::class, 'otp_verification']);
Route::post('signup', [AuthController::class, 'register']);
Route::post('signout', [AuthController::class, 'logout'])->middleware('auth:api');

// Route::get('sent-event', function() {
//     broadcast(new \App\Events\ModelEvent());
// });

Route::middleware('auth:api')->group(function () {
    //API GET AUTH USER
    Route::get('me', [AuthController::class, 'me']);

    // for handle upload file
    Route::post('/upload', function (Request $request) {
        $isCkeditor = false;
        if (isset($request->upload)) {
            $isCkeditor = true;
        }

        $validator = Validator::make($request->all(), [
            $isCkeditor ? 'upload' : 'file' => 'required|file|mimes:jpeg,png,jpg,webp,gif,svg,pdf,doc,docx,xls,xlsx|max:2500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => "File must be exists",
                'errors' => $validator->errors()->all()
            ], 422);
        }
        try {
            $file = $isCkeditor ? $request->upload : $request->file;
            // $subFolder = $request->folder??'files';
            $subFolder = $request->folder ?? 'files';
            $path = "public/uploads/$subFolder";
            $filename = $file->getClientOriginalName();
            $stored = $file->storeAs($path, $filename);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "Failed when trying to Store File",
                'errors' => [
                    $e->getMessage()
                ]
            ], 422);
        }

        if ($isCkeditor) {
            return [
                'url' => str_replace(env('APP_URL'), '', asset(str_replace('public', 'storage', $stored)))
            ];
        }

        return str_replace(env('APP_URL'), '', asset(str_replace('public', 'storage', $stored)));
    });
});

//  API REQUEST ALL
Route::post("v1/table/{parent}", [ApiController::class, 'table']);
Route::get("v1/{parent}/{parent_id?}/{detail?}/{detail_id?}/{subdetail?}/{subdetail_id?}", [ApiController::class, 'index']);
Route::post("v1/{parent}/{parent_id?}/{detail?}/{detail_id?}/{subdetail?}/{subdetail_id?}", [ApiController::class, 'index']);
Route::put("v1/{parent}/{parent_id?}/{detail?}/{detail_id?}/{subdetail?}/{subdetail_id?}", [ApiController::class, 'index']);
Route::delete("v1/{parent}/{parent_id?}/{detail?}/{detail_id?}/{subdetail?}/{subdetail_id?}", [ApiController::class, 'index']);
Route::get("v1", [ApiController::class, 'index']);
