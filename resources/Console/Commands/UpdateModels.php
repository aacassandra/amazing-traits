<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UpdateModels extends Command
{
    private $model_name = 'm_models';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:m_models';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update master models';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $tables = [];
        $path = app_path() . "/Models";
        $results = scandir($path);

        foreach ($results as $result) {
            if ($result === '.' or $result === '..') continue;
            $filename = $result;
            if (is_dir($filename)) {
                $tables = array_merge($tables, getModels($filename));
            } elseif ($filename !== '.DS_Store') {
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

                    $pms = DB::table(env('TABLE_PREFIX') . $this->model_name);
                    $current_permission = $pms->where('name', $model->getOnlyTable());

                    $dataPrepare = [
                        'name' => $model->getOnlyTable(),
                        'created_id' => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];

                    if ($current_permission->exists()) {
                        $current_permission->update($dataPrepare);
                        $this->info($model->getOnlyTable().' has been replace');
                    } else {
                        $pms->insert($dataPrepare);
                        $this->info($model->getOnlyTable().' has been insert');
                    }
                }
            }
        }
    }
}
