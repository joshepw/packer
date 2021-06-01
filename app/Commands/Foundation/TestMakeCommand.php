<?php

namespace App\Commands\Foundation;

use App\Traits\ClassInputName;
use Illuminate\Support\Str;
use App\Traits\InteractsWithFoundationCommands;
use Illuminate\Foundation\Console\TestMakeCommand as TestMake;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Console\Input\InputOption;

class TestMakeCommand extends TestMake
{
	use InteractsWithFoundationCommands;
	use ClassInputName;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'make:test';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new test for your package';

	/**
	 * Get the stub file for the generator.
	 *
	 * @return string
	 */
	protected function getStub()
	{
		return __DIR__ . '/stubs/test.stub';
	}

	/**
	 * Build the class with the given name.
	 *
	 * @param  string  $name
	 * @return string
	 *
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	protected function buildClass($name)
	{
		$uses_header = '';
		$uses_classes = '';

		if ($this->option('with-faker')) {
			$uses_header .= "use Illuminate\Foundation\Testing\WithFaker;\n";
			$uses_classes .= "use WithFaker;\n\t";
		} 

		if ($this->option('without-middleware')) {
			$uses_header .= "use Illuminate\Foundation\Testing\WithoutMiddleware;\n";
			$uses_classes .= "use WithoutMiddleware;\n\t";
		}

		if ($this->option('with-faker') || $this->option('without-middleware')) {
			$uses_classes .= "\n\t";
		}
		
		$replace = [
			'{{ uses }}' => $uses_header,
			'{{ usescases }}' => $uses_classes,
		];

		$stub = $this->files->get($this->getStub());
		$stub = $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);

		return str_replace(array_keys($replace), array_values($replace), $stub);
	}

	/**
	 * Get the root namespace for the class.
	 *
	 * @return string
	 */
	protected function rootNamespace()
	{
		$content    = $this->getComposer();
		$loading    = 'autoload-dev';
		$psr        = 'psr-4';
		
		return key($content->$loading->$psr);
	}

	public function getPath($name)
	{
		$name     = Str::replaceFirst($this->rootNamespace(), '', $name);
		$path     = $this->getPackagePath('/tests');
		return $path . '/' . str_replace('\\', '/', $name) . '.php';
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			['unit', 'u', InputOption::VALUE_NONE, 'Create a unit test.'],
			['with-faker', null, InputOption::VALUE_NONE, 'Add Facker functionality.'],
			['without-middleware', null, InputOption::VALUE_NONE, 'Remove Middleware functionality.'],
		];
	}

	/**
	 * Options to show on make command
	 *
	 * @return array
	 */
	public function getQuestions(): array
	{
		return [
			'type' => [
				'default' => 'Create a feature test',
				'--unit' => 'Create a unit test',
			],
			'--with-faker' => 'Add Facker functionality',
			'--without-middleware' => 'Remove Middleware functionality',
		];
	}
}
