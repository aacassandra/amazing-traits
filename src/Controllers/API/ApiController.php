<?php

namespace AmazingTraits\Controllers\API;

use AmazingTraits\Controllers\API\BaseController as BaseController;
use App\Models\ApiModel;
use App\Models\configs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Http\Controllers\API\MainDocsController;

class ApiController extends BaseController
{
    private function makeModel( string $selectedSchema, string $tableName )
    {
        $model = new ApiModel;
        if (env("DB_CONNECTION") === "pgsql") {
            $model->setTable("$selectedSchema.$tableName");
        } else {
            $model->setTable("$tableName");
        }
        return $model;
    }

    private function getModel( string $modelOrTable ){
        $systemTables = config('model-trait.list-table');
        $fixModelName = $modelOrTable;
        foreach ($systemTables as $tbl) {
            if (env('TABLE_PREFIX') . $tbl === $modelOrTable) {
                $fixModelName = str_replace(env('TABLE_PREFIX'), '', $modelOrTable);
            }
        }

        $selectedSchema = '';
        if (env("DB_CONNECTION") === "pgsql") {
            $schemas = config('model-trait.schema');

            // Pengecekan schema
            $match = false;
            foreach ($schemas as $schema => $tables) {
                foreach ($tables as $tbl) {
                    if ($tbl === $fixModelName) {
                        $match = true;
                        $selectedSchema = $schema;
                    }
                }
            }

            // Gunakan default schema jika table/model tidak di daftarkan ke konfigurasi schema
            // konfigurasi ada di file ./config/model-trait.php
            if (!$match) {
                $selectedSchema = config('model-trait.default');
            }
        }

        if ( class_exists("App\Models\\$fixModelName") ){
            $className = "App\Models\\$fixModelName";
            $model = new $className;
            if (env("DB_CONNECTION") === "pgsql") {
                return $model->setTable("$selectedSchema.{$model->getTable()}");
            } else {
                return $model->setTable("{$model->getTable()}");
            }
        } elseif ( Schema::hasTable( $modelOrTable ) ){
            return $this->makeModel( env("DB_CONNECTION") === "pgsql" ? $selectedSchema : "", $fixModelName );
        }

        return false;
    }

    private function generateApiDoc(): array
    {
        // $tables = Schema::getAllTables();

        $tables = [];
        $path = app_path() . "/Models";
        $results = scandir($path);

        foreach ($results as $result) {
            if ($result === '.' or $result === '..') continue;
            $filename = $result;
            if (is_dir($filename)) {
                $tables = array_merge($tables, getModels($filename));
            } else {
                $substr = substr($filename,0,-4);
                if (!Str::contains($substr, ['ApiModel', 'ApiModelNoTrait', 'User'])) {
                    $fixTable = '';
                    if (
                        Str::startsWith($substr, 'm_') or
                        Str::startsWith($substr, 'e_') or
                        Str::startsWith($substr, 'd_') or
                        Str::startsWith($substr, 't_') or
                        $substr === 'configs'
                    ) {
                        $fixTable = env('TABLE_PREFIX') . $substr;
                    } else {
                        $fixTable = $substr;
                    }

                    $className = "App\Models\\$substr";
                    $model = new $className;

                    if (!$model->hide) {
                        $tables[] = $fixTable;
                    }
                }
            }
        }

        $main_docs = new MainDocsController();
        $template = $main_docs->template;
        $prefix = "api/v1/";
        $generates = array_map(function($table) use ($prefix) {
            $genTables = config('model-trait.list-table');
            $fixGenTables = [];
            foreach ($genTables as $tbl) {
                $fixGenTables[] = env('TABLE_PREFIX') . $tbl;
            }

            $fixTable = '';
            if (Str::contains($table, $fixGenTables)) {
                $beforeFixTable = str_replace(env('TABLE_PREFIX'), '', $table);
                if ( class_exists("App\Models\\$beforeFixTable") ){
                    $fixTable = $beforeFixTable;
                } else {
                    $fixTable = $table;
                }
            } else {
                $fixTable = $table;
            }

            $className = "App\Models\\$fixTable";
            $model = new $className;

            $description = $model->description ?? '';
            $postValidation = '[]';
            $putValidation = '[]';
            foreach (get_class_methods($model) as $func) {
                if ($func === 'creatingRules') {
                    $postValidation = json_encode($model->creatingRules());
                } else if ($func === 'updatingRules') {
                    $putValidation = json_encode($model->updatingRules(1));
                }
            }


            $availableScopes = $model->getAvailableScopes();
            $items = [
                [
                    'type' => 'GET',
                    'path' => $prefix.$table,
                    'labels' => $model->docs['GET']['labels'] ?? [],
                    'screenshot' => $model->docs['GET']['ss'] ?? [],
                    'notes' => $model->docs['GET']['notes'] ?? [],
                    'hide' => $model->docs['GET']['hide'] ?? false,
                    'available_scopes' => $availableScopes
                ],
                [
                    'type' => 'GET_ID',
                    'path' => $prefix.$table."/{ID}",
                    'labels' => $model->docs['GET_ID']['labels'] ?? [],
                    'screenshot' => $model->docs['GET_ID']['ss'] ?? [],
                    'notes' => $model->docs['GET_ID']['notes'] ?? [],
                    'hide' => $model->docs['GET_ID']['hide'] ?? false
                ],
                [
                    'type' => 'POST',
                    'path' => $prefix.$table,
                    'validation' => $postValidation,
                    'labels' => $model->docs['POST']['labels'] ?? [],
                    'screenshot' => $model->docs['POST']['ss'] ?? [],
                    'notes' => $model->docs['POST']['notes'] ?? [],
                    'hide' => $model->docs['POST']['hide'] ?? false
                ],
                [
                    'type' => 'DELETE',
                    'path' => $prefix.$table."/{ID}",
                    'labels' => $model->docs['DELETE']['labels'] ?? [],
                    'screenshot' => $model->docs['DELETE']['ss'] ?? [],
                    'notes' => $model->docs['DELETE']['notes'] ?? [],
                    'hide' => $model->docs['DELETE']['hide'] ?? false
                ],
                [
                    'type' => 'PUT',
                    'path' => $prefix.$table."/{ID}",
                    'validation' => $putValidation,
                    'labels' => $model->docs['PUT']['labels'] ?? [],
                    'screenshot' => $model->docs['PUT']['ss'] ?? [],
                    'notes' => $model->docs['PUT']['notes'] ?? [],
                    'hide' => $model->docs['PUT']['hide'] ?? false
                ]
            ];
            $methods = array_filter(get_class_methods($model), function ($dt) {
                $dt = Str::lower($dt);
                return Str::startsWith($dt, ['custom','getfunc']);
            });

            foreach($model->getAvailableMethods() as $func) {
                if (Str::endsWith($func, ' :POST')) {
                    $path = str_replace(' :POST', '', $func);
                    $validation = '[]';
                    $labels = [];
                    $screenshot = [
                        'url' => '',
                        'figma_url' => '',
                    ];
                    $hide = false;
                    $notes = [];
                    foreach ($methods as $method) {
                        $dt = Str::lower($method);
                        if (Str::startsWith($dt, ["custom$path"]) && Str::endsWith($dt, ['validation'])) {
                            $validation = json_encode($model->{$method}());
                        } elseif (Str::startsWith($dt, ["custom$path"]) && Str::endsWith($dt, ['configs'])) {
                            $validation = json_encode($model->{$method}()['validation'] ?? []);
                            $labels = $model->{$method}()['labels'] ?? [];
                            $hide = $model->{$method}()['hide'] ?? false;
                            $notes = $model->{$method}()['notes'] ?? [];
                            if (isset($model->{$method}()['ss']) && $model->{$method}()['ss']['url']) {
                                $screenshot['url'] = $model->{$method}()['ss']['url'];
                                $screenshot['figma_url'] = $model->{$method}()['ss']['figma_url'] ?? '';
                            }
                        }
                    }
                    $items[] = [
                        'notes' => $notes,
                        'labels' => $labels,
                        'type' => 'POST',
                        'path' => $prefix.$table.'/'.$path,
                        'validation' => $validation,
                        'screenshot' => $screenshot,
                        'hide' => $hide,
                    ];
                } else {
                    $path = str_replace(' :GET', '', $func);
                    $validation = '';
                    $labels = [];
                    $screenshot = [
                        'url' => '',
                        'figma_url' => '',
                    ];
                    $hide = false;
                    $notes = [];
                    foreach ($methods as $method) {
                        $dt = Str::lower($method);
                        if (Str::startsWith($dt, ["getfunc$path"]) && Str::endsWith($dt, ['validation'])) {
                            $validation = http_build_query($model->{$method}());
                        } elseif (Str::startsWith($dt, ["getfunc$path"]) && Str::endsWith($dt, ['configs'])) {
                            $validation = http_build_query($model->{$method}()['query_params'] ?? []);
                            $labels = $model->{$method}()['labels'] ?? [];
                            $hide = $model->{$method}()['hide'] ?? false;
                            $notes = $model->{$method}()['notes'] ?? [];
                            if (isset($model->{$method}()['ss']) && $model->{$method}()['ss']['url']) {
                                $screenshot['url'] = $model->{$method}()['ss']['url'];
                                $screenshot['figma_url'] = $model->{$method}()['ss']['figma_url'] ?? '';
                            }
                        }
                    }
                    $items[] = [
                        'notes' => $notes,
                        'labels' => $labels,
                        'type' => 'GET',
                        'path' => $validation ?
                            $prefix.$table.'/'.$path."?$validation" :
                            $prefix.$table.'/'.$path,
                        'screenshot' => $screenshot,
                        'hide' => $hide
                    ];
                }
            }

            return [
                'description' => $description,
                'model' => $table,
                'items' => $items
            ];
        }, $tables);
        foreach ($generates as $gen) {
            $template[] = $gen;
        }
        return $template;
    }

    public function index(
        Request $request,
        string $parent = null,
        string $parent_id = null,
        string $detail = null,
        string $detail_id = null,
        string $subdetail = null,
        string $subdetail_id = null
    ) {
        // ======================== API Documentation
        if ( !$parent ){
            $metaData = [
                "params" => [
                    "paginate"=>"numeric, ex: 25 [Untuk melakukan paginasi data]",
                    "where"=>"text, ex: name = 'Jhon Doe' [Melakukan custom where raw query]",
                    "wherein"=>"text, ex: id = [1,2] [Melakukan custom where in query]",
                    "wherenotin"=>"text, ex: id = [1,2] [Melakukan custom where not in query]",
                    "distinct"=>"text, ex: group [Mendapatkan nilai group dari suatu kolom]",
                    "search" => "text, ex: jawatimur [Melakukan searching ke all columns dengan logika LIKE]",
                    "selectfield"=> "text, ex: kolom1,kolom_N [Memilih kolom yang diinginkan saja, bisa subquery AS]",
                    "addselect"=> "text, ex: kolom1,kolom_N [Menambahkan kolom tambahan, bisa subquery AS]",
                    "data_filter_KOLOM" => "text [Memfilter data dengan logika MATCH]",
                    "join" => "boolean, ex: false [Meng-enable auto join atau tidak]",
                    "joins" => "string, ex: user_id=nama_tabel atau user_id=nama_tabel.kolom1.kolom2... [Men-join-kan ngefly]",
                    "orderby"=>"string, ex: this.id [Untuk melakukan order by id table bersangkutan]",
                    "ordertype"=>"string, ex: DESC [Untuk melakukan order type ASC atau DESC]",
                    "encrypt"=>"string, ex: false [Untuk mematikan encryption data kolom ID]",
                    "all"=>"string, ex: true [Get data termasuk yang terhapus(deleted)]",
                ],
                "endpoints" => $this->generateApiDoc()
            ];

            return view('vendor/amazing-traits/apidoc', compact('metaData'));
        }

        // ======================== GET MODEL / FLY MODEL BY TABLE
        $parentModel = $this->getModel( $parent );
        if ( !$parentModel ){
            return response()->json([
                'message' => "Resource $parent tidak ditemukan",
                "errors" => ["Resource $parent tidak ditemukan"]
            ], 404);
        }
        // ======================== API /parent
        if ( !$parent_id ){
            if ( $request->isMethod('GET')) {
                // GET
                if (isset($parentModel->auths) && is_array($parentModel->auths) && !auth('api')->check()) {
                    foreach ($parentModel->auths as $auth) {
                        if ($auth === 'default' || $auth === 'get') {
                            return response()->json([
                                "success" => false,
                                "message" => "Sorry, you don't have authentication"
                            ], 401);
                        }
                    }
                }

                if (!$parentModel->checkPermissionsV2('get', $parentModel->getOnlyTable())) {
                    return response()->json([
                        "success" => false,
                        "message" => "Sorry, you don't have permission"
                    ], 401);
                }

                if ($request->has('all')){
                    $parentModel = $parentModel->withTrashed();
                }

                $availableScopes = $parentModel->getAvailableScopes();
                $availableMethods = $parentModel->getAvailableMethods();
                if ( app()->request->has('distinct') ){
                    $data = [
                        'data' => $parentModel->distinct()->pluck(app()->request->distinct)->all()
                    ];
                } else {
                    $clientScopes = $request->has('scopes')? explode( ',', $request->scopes ):[];
                    foreach($clientScopes as $scope){
                        if (!in_array($scope, $availableScopes)){
                            return response()->json([
                                'message' => "failed to retrieve data",
                                'errors' => [ "scope $scope tidak tersedia untuk resource ini" ]
                            ], 404);
                        }
                    }

                    try {
                        $data = $parentModel
                            ->scopes( array_merge($parentModel->scopes, $clientScopes ))
                            ->paginate( $request->paginate ?? 25 );
                    } catch (\Illuminate\Database\QueryException $e) {
                        $str=trim(preg_replace('/\s\s+/', ' ', $e->getMessage()));
                        $line = preg_split('#\r?\n#', $str, 2)[0];

                        if (strpos($e->getMessage(), 'Ambiguous column')) {
                            return response()->json([
                                'success' => false,
                                'message' => "$line <br/><br/> {$e->getSql()} <br/><br/> Please, contact the developer",
                            ], 400);
                        } else if (strpos($e->getMessage(), 'Undefined column: 7 ERROR')) {
                            return response()->json([
                                'success' => false,
                                'message' => "$line <br/><br/> {$e->getSql()} <br/><br/> Please, contact the developer",
                            ], 500);
                        } else {
                            return response()->json([
                                'success' => false,
                                'message' => $e->getMessage()
                            ], 400);
                        };
                    }
                }

                return collect([
                    'success' => true,
                    'available_scopes' => $availableScopes,
                    'available_methods' => $availableMethods
                ])->merge($data);
            } elseif ( $request->isMethod('POST')){
                // STORE
                if (isset($parentModel->auths) && is_array($parentModel->auths) && !auth('api')->check()) {
                    foreach ($parentModel->auths as $auth) {
                        if ($auth === 'default' || $auth === 'store') {
                            return response()->json([
                                "success" => false,
                                "message" => "Sorry, you don't have authentication"
                            ], 401);
                        }
                    }
                }

                if (!$parentModel->checkPermissionsV2('store', $parentModel->getOnlyTable())) {
                    return response()->json([
                        "success" => false,
                        "message" => "Sorry, you don't have permission"
                    ], 401);
                }

                return $parentModel->creatin();
            }

            // ======================== API /parent/parent_id
        } elseif ( $parent_id ){
            if (
                $request->isMethod('GET') &&
                method_exists($parentModel, 'getfunc'.ucfirst( $parent_id ) )
            ) {
                // Custom GET
                $function = 'getfunc'.ucfirst( $parent_id );

                if (isset($parentModel->auths) && is_array($parentModel->auths) && !auth('api')->check()) {
                    foreach ($parentModel->auths as $auth) {
                        if ($auth === 'default' || $auth === $function) {
                            return response()->json([
                                "success" => false,
                                "message" => "Sorry, you don't have authentication"
                            ], 401);
                        }
                    }
                }

                if (!$parentModel->checkPermissionsV2($function, $parentModel->getOnlyTable())) {
                    return response()->json([
                        "success" => false,
                        "message" => "Sorry, you don't have permission"
                    ], 401);
                }

                return $parentModel->$function( $request , $detail);
            } else if (!$detail){
                $decryptedId = is_numeric($parent_id) && isset($request->encrypt) && $request->encrypt === 'false' ? $parent_id : $parentModel->decrypt( $parent_id );
                if ( $request->isMethod('GET')){
                    // FIND
                    if (isset($parentModel->auths) && is_array($parentModel->auths) && !auth('api')->check()) {
                        foreach ($parentModel->auths as $auth) {
                            if ($auth === 'default' || $auth === 'find') {
                                return response()->json([
                                    "success" => false,
                                    "message" => "Sorry, you don't have authentication"
                                ], 401);
                            }
                        }
                    }

                    if (!$parentModel->checkPermissionsV2('find', $parentModel->getOnlyTable())) {
                        return response()->json([
                            "success" => false,
                            "message" => "Sorry, you don't have permission"
                        ], 401);
                    }

                    $availableScopesForById = array_filter($parentModel->scopes, function($scope){
                        return !in_array( $scope, [
                            'wherin','whereadnotin','whereadin','distinctin','orderin','filterin','searchin'
                        ]) ;
                    });
                    $data = $parentModel->scopes( $availableScopesForById );
                    if ($request->has('all')){
                        $data = $data->withTrashed();
                    }
                    $data = $data->find( $decryptedId );

                    if ( $data ){
                        $data = $data->toArray();
                    } else {
                        return response()->json(['message'=>'Data tidak ditemukan','errors'=>['No Data']],404);
                    }

                    if ( @$parentModel->details ){
                        $data = $parentModel->detailinDetails($parentModel->details, $parent, $decryptedId, $data);
                        if (isset($data->original)){
                            return response()->json($data->original, $data->status());
                        }
                    }

                    if ( !$data ){
                        return response()->json([
                            'message'   => "No data was found",
                            'data'      => null
                        ], 404);
                    }

                    if (app()->request->is_approval) {
                        $data = $parentModel->getApprovalPermissions(trx_id: $decryptedId, data: $data);
                    }

                    return [
                        'success' => true,
                        'data' => $data
                    ];
                } elseif ( $request->isMethod('PUT')){
                    //  UPDATE
                    if (isset($parentModel->auths) && is_array($parentModel->auths) && !auth('api')->check()) {
                        foreach ($parentModel->auths as $auth) {
                            if ($auth === 'default' || $auth === 'update') {
                                return response()->json([
                                    "success" => false,
                                    "message" => "Sorry, you don't have authentication"
                                ], 401);
                            }
                        }
                    }

                    if (!$parentModel->checkPermissionsV2('update', $parentModel->getOnlyTable())) {
                        return response()->json([
                            "success" => false,
                            "message" => "Sorry, you don't have permission"
                        ], 401);
                    }

                    $decryptedId = is_numeric($parent_id) && isset($request->encrypt) && $request->encrypt === 'false' ? $parent_id : $parentModel->decrypt( $parent_id );
                    return $parentModel->updatin( $decryptedId );
                } elseif ( $request->isMethod('DELETE')){
                    // DESTROY
                    if (isset($parentModel->auths) && is_array($parentModel->auths) && !auth('api')->check()) {
                        foreach ($parentModel->auths as $auth) {
                            if ($auth === 'default' || $auth === 'destroy') {
                                return response()->json([
                                    "success" => false,
                                    "message" => "Sorry, you don't have authentication"
                                ], 401);
                            }
                        }
                    }

                    if (!$parentModel->checkPermissionsV2('destroy', $parentModel->getOnlyTable())) {
                        return response()->json([
                            "success" => false,
                            "message" => "Sorry, you don't have permission"
                        ], 401);
                    }

                    $decryptedId = $parentModel->decrypt( $parent_id );
                    return $parentModel->deletin( $decryptedId );
                } elseif ( $request->isMethod('POST') && method_exists($parentModel, 'custom'.ucfirst( $parent_id ) ) ){
                    // Custom POST
                    $function = 'custom'.ucfirst( $parent_id );

                    if (isset($parentModel->auths) && is_array($parentModel->auths) && !auth('api')->check()) {
                        foreach ($parentModel->auths as $auth) {
                            if ($auth === 'default' || $auth === $function) {
                                return response()->json([
                                    "success" => false,
                                    "message" => "Sorry, you don't have authentication"
                                ], 401);
                            }
                        }
                    }

                    if (!$parentModel->checkPermissionsV2($function, $parentModel->getOnlyTable())) {
                        return response()->json([
                            "success" => false,
                            "message" => "Sorry, you don't have permission"
                        ], 401);
                    }

                    return $parentModel->$function( $request );
                }
            }
        }
    }

    public function table(
        Request $request,
        string $parent = null
    ) {
        $parentModel = $this->getModel( $parent );
        if ( !$parentModel ){
            return response()->json([
                'message' => "Resource $parent tidak ditemukan",
                "errors" => ["Resource $parent tidak ditemukan"]
            ], 404);
        }

        if (isset($parentModel->auths) && is_array($parentModel->auths) && !auth('api')->check()) {
            foreach ($parentModel->auths as $auth) {
                if ($auth === 'default' || $auth === 'get') {
                    return response()->json([
                        "success" => false,
                        "message" => "Sorry, you don't have authentication"
                    ], 401);
                }
            }
        }

        if (!$parentModel->checkPermissionsV2('get', $parentModel->getTable())) {
            return response()->json([
                "success" => false,
                "message" => "Sorry, you don't have permission"
            ], 401);
        }

        $body = $request->getContent();
        $model = $parent;
        if ($body) {
            $body = json_decode($body, true);
        }

        $params = [];

        $params['paginate'] = $body['perPage'] ?? 25;
        $params['skip'] = $body['page'] * $params['paginate'];

        $sysTbls = config('model-trait.list-table');
        foreach ($sysTbls as $sys) {
            if (Str::endsWith($model, $sys)) {
                $model = str_replace(env('TABLE_PREFIX'), '', $model);
            }
        }

        if (class_exists("App\Models\\$model")) {
            $className = "App\Models\\$model";
            $model = new $className;

            if (isset($model->auths) && is_array($model->auths)) {
                foreach ($model->auths as $auth) {
                    if ($auth === 'default' && !auth('api')->check()) {
                        return response()->json([
                            "success" => false,
                            "message" => "Sorry, you don't have authentication"
                        ], 401);
                    }
                }
            }

            $clientScopes = $request->has('scopes')? explode( ',', $request->scopes ):[];

            $req = $model->scopes(array_merge($model->scopes, $clientScopes ));
            $where = [];
            if ($body['columnFilters'] && count($body['columnFilters'])) {
                foreach ($body['columnFilters'] as $key => $value) {
                    if (Str::endsWith($key, '_date')) {
                        $req = $req->where($key, '=', $value);
                    } else if (Str::endsWith($key, 'select_range')) {
                        if ($value['start'] && $value['end']) {
                            $mdl = $model->getTable();
                            $req = $req
                                ->whereDate("$mdl.created_at", '>=', $value['start'])
                                ->whereDate("$mdl.created_at", '<=', $value['end']);
                        }
                    } else {
                        if (is_string($value)) {
                            $req = $req->where($key, 'LIKE', '%'.$value.'%');
                        } else {
                            $req = $req->where($key, '=', $value);
                        }
                    }
                }
            }

            if ($request->sort && count($request->sort)) {
                foreach ($request->sort as $sort) {
                    $req = $req->orderBy($sort['field'], $sort['type']);
                }
            }

            $req = $req
                ->skip($params['skip'])
                ->paginate($params['paginate']);

            $req = json_encode($req);
            $req = json_decode($req, true);
            $req['success'] = true;
            return response()->json($req, 200);
        } else {
            return response()->json([
                'message' => "Resource of $model is not found",
                "errors" => ["Resource of $model is not found"]
            ], 404);
        }
    }
}
