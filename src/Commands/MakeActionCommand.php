<?php

namespace Afubasic\LaravelUtilities\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeActionCommand extends Command
{
    protected $signature = 'make:action {name}';

    protected $description = 'Create a new Action class inside app/Actions';


    public function handle()
    {
        $name = trim($this->argument('name'));

        // Parse class + namespace
        $data = (object) $this->getNameSeparations($name);

        $path = app_path("Actions/{$name}.php");

        // ensure ALL parent directories exist (recursive)
        File::ensureDirectoryExists(dirname($path));

        if (File::exists($path)) {
            $this->error("Action {$data->class} already exists!");
            return Command::FAILURE;
        }

        $namespace = $data->namespace
            ? "App\\Actions\\{$data->namespace}"
            : "App\\Actions";

        $stub = <<<PHP
<?php

namespace {$namespace};

class {$data->class}
{
    public function execute()
    {
        //
    }
}

PHP;

        File::put($path, $stub);

        $this->info("Action Created: {$namespace}\\{$data->class}");

        return Command::SUCCESS;
    }


    /**
    * Break name into namespace + class
    */
    
    protected function getNameSeparations($name): array
    {
        $segments = collect(
            Str::of($name)
                ->replace('\\', '/')
                ->explode('/')
                ->filter()
        );

        if ($segments->count() === 1) {
            return [
                'class' => Str::studly($segments[0]),
                'namespace' => '',
            ];
        }

        $class = Str::studly($segments->pop());

        $namespace = $segments
            ->map(fn ($seg) => Str::studly($seg))
            ->implode('\\');

        return [
            'class' => $class,
            'namespace' => $namespace,
        ];
    }
}
