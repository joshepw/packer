<?php

namespace App\Commands\Foundation;

use App\Traits\InteractsWithFoundationCommands;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class RouteMakeCommand extends GeneratorCommand
{
	use InteractsWithFoundationCommands;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name      = 'make:routefile';
	protected $signature = 'make:routefile {name}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a route files for package';

	/**
	 * The type of class being generated.
	 *
	 * @var string
	 */
	protected $type = 'class';

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function handle()
	{
		$this->composer_path = $this->getPackagePath();
		
		$this->createRouteFile();
	}

	/**
	 * Get the desired class name from the input.
	 *
	 * @return string
	 */
	protected function getNameInput()
	{
		return Str::snake($this->argument('name'));
	}

	protected function getStub()
	{
		return __DIR__ . '/stubs/routes.stub';
	}

	public function createRouteFile()
	{
		$path = $this->getPackagePath($this->isLaravelPackage() ? '/src/Routes' : '/routes');
		$route_name = $this->getNameInput();

		if (!is_dir($path)) {
			mkdir($path, 0777, true);
		}

		$stub = file_get_contents($this->getStub());
		file_put_contents("$path/$route_name.php", $stub);

		$this->line("<info>Router created:</info> {$route_name}.php");
		$this->line("<info>Created at:</info> {$path}");
	}

	public function getMakerType()
	{
		return 'SnakeName.php';
	}
}
