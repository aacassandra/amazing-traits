<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeModelTrait extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:model-trait {modelName} {--m}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a model with specific traits';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $modelName = $this->argument('modelName');
        $shouldMakeMigration = $this->option('m');
        $stubPath = base_path('vendor/aacassandra/amazing-traits/resources/stubs/model.stub'); // Sesuaikan dengan path ke file model.stub di dalam custom package

        if (!File::exists($stubPath)) {
            $this->error('Model stub file not found!');
            return;
        }

        $stub = File::get($stubPath);
        $modelContent = str_replace(['{{modelName}}', '{{table_name}}'], [$modelName, strtolower($modelName)], $stub);

        $modelPath = app_path('Models/' . $modelName . '.php');

        if (File::exists($modelPath)) {
            $this->error('Model already exists!');
            return;
        }

        File::put($modelPath, $modelContent);

        $this->info('Model ' . $modelName . ' created successfully.');

        if ($shouldMakeMigration) {
            $migrationName = 'create_' . strtolower($modelName) . '_table';
            $this->call('make:migration', [
                'name' => $migrationName,
            ]);
        }
    }
}
