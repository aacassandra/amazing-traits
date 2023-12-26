<?php

use AmazingTraits\Helpers\Cryptor;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

if (!function_exists('getModel')) {
    function getModel(string $modelOrTable, $clean = false, $trait = true)
    {
        if (!$clean && class_exists("App\Models\\$modelOrTable")) {
            $className = "App\Models\\$modelOrTable";
            return new $className;
        } elseif (Schema::hasTable($modelOrTable)) {
            if ($trait) {
                $model = new \App\Models\ApiModel;
                $model->setTable($modelOrTable);
                return $model;
            } else {
                $model = new \App\Models\ApiModelNoTrait;
                $model->setTable($modelOrTable);
                return $model;
            }
        }
        return false;
    }
}

/**
 * Mendapatkan request atau by key
 */
if (!function_exists('req')) {
    function req(string $key = null)
    {
        $pairs = explode("&", !app()->request->isMethod('GET') ? file_get_contents("php://input") : (@$_SERVER['QUERY_STRING'] ?? @$_SERVER['REQUEST_URI']));
        $data = (object)[];
        foreach ($pairs as $pair) {
            $nv = explode("=", $pair);
            if (count($nv) < 2) continue;
            $name = urldecode($nv[0]);
            $value = urldecode($nv[1]);
            $data->$name = $value;
        }

        if ($key !== null) {
            if (Str::contains($key, '%')) {
                $key = str_replace('%', '', $key);
                $newData = [];
                foreach ((array)$data as $keyArr => $dt) {
                    if (Str::startsWith($keyArr, $key)) {
                        $newData[$keyArr] = $dt;
                    }
                }
                $data = (object) $newData;
            } else {
                return isset($data->$key) ? $data->$key : null;
            }
        }
        return $data;
    }
}

if (!function_exists('sendError')) {
    function sendError($message, array $errorMessages = [], int $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($errorMessages)) {
            $response['errors'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}

if (!function_exists('sendResponse')) {
    function sendResponse($result = [], string $message = '')
    {
        $response = [
            'success' => true
        ];

        if (is_array($result)) {
            if (isset($result['data'])) {
                foreach ($result as $key => $rslt) {
                    if ($key === 'data') {
                        $cryptor = new \App\Helpers\Cryptor();
                        foreach ($rslt as $item) {
                            foreach ($item as $key => $val) {
                                if ($key === 'id') {
                                    $item->$key = $cryptor->encrypt($val);
                                } else if (Str::endsWith($key, '_id') && is_numeric($val)) {
                                    $item->$key = $cryptor->encrypt($val);
                                } else if (strpos($key, '.') > -1) {
                                    $exp = explode('.', $key);
                                    if (Str::endsWith($exp[count($exp) - 1], '_id') && is_numeric($val)) {
                                        $item->$key = $cryptor->encrypt($val);
                                    } else if (Str::is('id', $exp[count($exp) - 1]) && is_numeric($val)) {
                                        $item->$key = $cryptor->encrypt($val);
                                    }
                                }
                            }
                        }
                        $response[$key] = $rslt;
                    } else {
                        $response[$key] = $rslt;
                    }
                }
            } else if (count($result)) {
                $response['data'] = $result;
            }
        } else {
            $response['data'] = $result;
        }

        if ($message) {
            $response['message'] = $message;
        }

        return response()->json($response, 200);
    }
}

if (!function_exists('array_paginate')) {
    function array_paginate($array, $pageSize, $page = 1)
    {
        $pages = array_chunk($array, $pageSize);
        return $pages[$page - 1] ?? [];
    }
}

if (!function_exists('arr2json')) {
    function arr2json($arr)
    {
        return json_decode(json_encode($arr));
    }
}

if (!function_exists('json2arr')) {
    function json2arr($json)
    {
        return json_decode(json_encode($json), true);
    }
}

if (!function_exists('removeSpecialChar')) {
    function removeSpecialChar($string)
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }
}

if (!function_exists('haversineGreatCircleDistance')) {
    function haversineGreatCircleDistance(
        $latitudeFrom,
        $longitudeFrom,
        $latitudeTo,
        $longitudeTo,
        $earthRadius = 6371000
    ): float|int {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }
}

if (!function_exists('generate_code')) {
    function generate_code(String $table_name, String $prefix): string
    {
        $code = '';
        $counterTbl = env('TABLE_PREFIX') . 'e_counter';
        DB::beginTransaction();
        try {
            $counter = DB::table("$counterTbl")->where([
                ['table_name', '=', $table_name],
                ['year', '=', date('Y')]
            ])->first();
            if (!$counter) {
                DB::table("$counterTbl")->insert([
                    'table_name' => $table_name,
                    'year' => date('Y'),
                    'value' => 1,
                    'created_at' => now()
                ]);
                $code = "$prefix-0000001";
            } else {
                $newCounter = $counter->value + 1;
                DB::table("$counterTbl")->where([
                    ['table_name', '=', $table_name],
                    ['year', '=', date('Y')]
                ])->update([
                    'value' => $newCounter
                ]);
                $code = "$prefix-";
                $value = (string)$newCounter;
                if (strlen($value) === 1) {
                    $code .= "000000$value";
                } elseif (strlen($value) === 2) {
                    $code .= "00000$value";
                } elseif (strlen($value) === 3) {
                    $code .= "0000$value";
                } elseif (strlen($value) === 4) {
                    $code .= "000$value";
                } elseif (strlen($value) === 5) {
                    $code .= "00$value";
                } elseif (strlen($value) === 6) {
                    $code .= "0$value";
                } elseif (strlen($value) === 7) {
                    $code .= "$value";
                }
            }

            DB::commit();
            return $code;
        } catch (\Exception $e) {
            DB::rollback();
            //
        }

        return $code;
    }
}

if (!function_exists('getCurrentUser')) {
    function getCurrentUser(): mixed
    {
        if (app()->request->user('api') !== null) {
            return app()->request->user('api');
        } else {
            return null;
        }
    }
}

if (!function_exists('getTablePrefix')) {
    function getTablePrefix(): string
    {
        return env('TABLE_PREFIX');
    }
}

if (!function_exists('sendWhatsappText')) {
    function sendWhatsappText(string $phone_number, string $message)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('WHATSAPP_SEND_TEXT_API_URL'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('msisdn' => $phone_number, 'message' => $message),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . env('WHATSAPP_BEARER_TOKEN')
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response, true);
    }
}

if (!function_exists('getWhatsappGroupList')) {
    function getWhatsappGroupList()
    {
        $host = env('WHATSAPP_HOST_URL');
        $api_url = "{$host}/api/v1/whatsapp/group";

        // Inisialisasi cURL session
        $ch = curl_init($api_url);

        // Set pilihan-pilihan cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Mengembalikan respons sebagai string
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . env('WHATSAPP_BEARER_TOKEN'))); // Menambahkan header Authorization

        // Melakukan permintaan GET
        $response = curl_exec($ch);

        // Memeriksa apakah ada kesalahan cURL
        if (curl_errno($ch)) {
            echo 'Kesalahan cURL: ' . curl_error($ch);
        }

        // Menutup sesi cURL
        curl_close($ch);

        // Menampilkan respons dari API
        return json_decode($response);
    }
}

if (!function_exists('t')) {
    function t(string $key, $options = [
        'lowercase' => false
    ])
    {
        $request = app()->request;
        $params = \Illuminate\Support\Arr::except($options, ['lowercase']);
        $lang = $request->query('lang', 'en') ?? $request->input('lang', 'en');
        if (isset($options['lowercase']) && $options['lowercase'] === true) {
            $before = strtolower(__($key, $params, $lang));
            if ($before === $key) {
                // untuk memberi default value jika translate = key , maka akan dikembalikan key nya
                if (strpos($before, '.') !== false) {
                    $tree = explode('.', $before);
                    return preg_replace('/_+/', ' ', $tree[1]);
                }
            }

            return strtolower(__($key, $params, $lang));
        } else {
            $before = __($key, $params, $lang);
            if ($before === $key) {
                // untuk memberi default value jika translate = key , maka akan dikembalikan key nya
                if (strpos($before, '.') !== false) {
                    $tree = explode('.', $before);
                    return preg_replace('/_+/', ' ', $tree[1]);
                }
            }

            return __($key, $params, $lang);
        }
    }
}

if (!function_exists('generate_validation_message')) {
    function generate_validation_message(array $will_validation) {
        $messages = [];
        foreach ($will_validation as $key => $value) {
            $splits = explode('|', $value);
            foreach ($splits as $sp) {
                if (strpos($sp, ':') !== false) {
                    $subexplode = explode(':', $sp);
                    if ($subexplode[0] === 'max') {
                        $messages["$key.{$subexplode[0]}"] = t("validation.{$subexplode[0]}", ['attribute' => t("fields.$key"), 'max' => $subexplode[1]]);
                    } elseif ($subexplode[0] === 'min') {
                        $messages["$key.{$subexplode[0]}"] = t("validation.{$subexplode[0]}", ['attribute' => t("fields.$key"), 'min' => $subexplode[1]]);
                    } elseif ($subexplode[0] === 'in') {
                        $exp = explode(',', $subexplode[1]);
                        $messages["$key.{$subexplode[0]}"] = t("validation.{$subexplode[0]}", ['attribute' => t("fields.$key"), 'values' => implode(', ', $exp)]);
                    } else {
                        $messages["$key.{$subexplode[0]}"] = t("validation.{$subexplode[0]}", ['attribute' => t("fields.$key")]);
                    }
                } else {
                    $messages["$key.$sp"] = t("validation.{$sp}", ['attribute' => t("fields.$key", ['lowercase' => true])]);
                }
            }
        }

        return $messages;
    }
}

if (!function_exists('smart_validator')) {
    function smart_validator($data = [], $rules = []) {
        $data = (new Cryptor)->autoDecryptAllNested($data);
        $response = json_decode(json_encode([
            'data' => [],
            'response' => null
        ]));
        $response->data = $data;
        $response->response = Validator::make($data, $rules, generate_validation_message($rules));
        return $response;
    }
}

if (!function_exists('smart_trigger_error')) {
    function smart_trigger_error(string $message, $errors = [])
    {
        return trigger_error(json_encode(['use_smart_trigger_error' => true, 'message' => $message, 'errors' => $errors]));
    }
}
