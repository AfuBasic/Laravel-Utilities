<?php

namespace Afubasic\LaravelUtilities\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeActionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:action {name}';
    

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Action class inside app/Actions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = Str::studly($this->argument('name'));
        $path = app_path("Actions/{$name}.php");

        if(!File::exists(app_path('Actions'))) {
            File::makeDirectory(app_path('Actions'), 0755, true);
        }

        if(File::exists($path)) {
            $this->error("Action {$name} already exists!");
            return Command::FAILURE;
        }

        $data = (object) $this->getNameSeparations($name);
        $namespace = $data->namespace ? "\\{$data->namespace}" : '';

        $stub = <<<PHP
        <?php

        namespace App\Actions{$namespace};

        class {$data->class}
        {
            public function execute()
            {
                //
            }
        }

        PHP;


        File::put($path, $stub);

        $this->info("✅ Action Created: App\\Actions\\{$name}");
        return Command::SUCCESS;
    }

    public function getNameSeparations($name): array {

        //Break a string into arrays
        $segments = collect(Str::of(trim($name))
        ->replace("\\","/")
        ->explode('/')
        ->filter());

        $length = $segments->count();
        
        if($length == 1) {
            return [
                'class' => $segments[0],
                'namespace' => ''
            ];
        }

        $class_name = Str::studly($segments->last());
        $namespace = $segments->map(fn ($seg) => Str::studly($seg))->implode('\\');

        return [
            'class' => $class_name,
            'namespace' => $namespace
        ];
    }
}
