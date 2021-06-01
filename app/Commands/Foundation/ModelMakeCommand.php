<?php

namespace App\Commands\Foundation;

use Illuminate\Support\Str;
use App\Traits\InteractsWithFoundationCommands;
use App\Contracts\WithQuestions;
use Illuminate\Foundation\Console\ModelMakeCommand as ModelMake;
use Illuminate\Support\Facades\Cache;

class ModelMakeCommand extends ModelMake implements WithQuestions
{
	use InteractsWithFoundationCommands;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'make:model';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new Eloquent model class in your package';

	/**
	 * Get the desired class name from the input.
	 *
	 * @return string
	 */
	protected function getNameInput()
	{
		return Str::studly(trim($this->argument('name')));
	}

	/**
	 * Build the class with the given name.
	 *
	 * @param  string  $name
	 * @return string
	 */
	protected function buildClass($name)
	{
		$table = Str::snake(Str::pluralStudly(class_basename($this->argument('name'))));

		$replace = [
			'{{ table }}' => $table,
			'{{table}}' => $table,
		];

		return str_replace(
			array_keys($replace),
			array_values($replace),
			parent::buildClass($name)
		);
	}

	/**
	 * Get the stub file for the generator.
	 *
	 * @return string
	 */
	protected function getStub()
	{
		if ($this->option('factory')) {
			return __DIR__ . '/stubs/model_factory.stub';
		}

		if ($this->option('pivot')) {
			return __DIR__ . '/stubs/model_pivot.stub';
		}

		return __DIR__ . '/stubs/model.stub';
	}

	/**
	 * Options to show on make command
	 *
	 * @return array
	 */
	public function getQuestions(): array
	{
		return [
			'--force' => 'Create the class even if the model already exists',
			'--controller' => 'Create a new controller for the model',
			'--factory' => 'Create a new factory for the model',
			'--migration' => 'Create a new migration file for the model',
			'--seed' => 'Create a new seeder file for the model',
			'--pivot' => 'Indicates if the generated model should be a custom intermediate table model',
		];
	}

	/**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        return ($this->isLaravelProject() ? $this->laravel->getNamespace() : $this->namespaceFromComposer()).'Models\\';
    }

	/**
	 * Get the destination class path.
	 *
	 * @param  string  $name
	 * @return string
	 */
	public function getPath($name)
	{
		$name    = Str::replaceFirst($this->rootNamespace(), '', $name);
		$path    = $this->getPackagePath($this->isLaravelPackage() ? '/src/Models/' : '/app/Models/');
		return  $path . str_replace('\\', '/', $name) . '.php';
	}
}
