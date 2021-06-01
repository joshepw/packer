<?php

namespace App\Commands\Foundation\Factories;

use App\Traits\ClassInputName;
use Illuminate\Support\Str;
use App\Traits\InteractsWithFoundationCommands;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Console\Input\InputOption;

class FactoryMakeCommand extends GeneratorCommand
{
	use ClassInputName;
	use InteractsWithFoundationCommands;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'make:factory';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new model factory';

	/**
	 * The type of class being generated.
	 *
	 * @var string
	 */
	protected $type = 'Factory';

	/**
	 * Get the stub file for the generator.
	 *
	 * @return string
	 */
	protected function getStub()
	{
		if ($this->option('legacy')) {
			return __DIR__ . '/stubs/factory_legacy.stub';
		}

		return __DIR__ . '/stubs/factory.stub';
	}

	/**
	 * Build the class with the given name.
	 *
	 * @param  string  $name
	 * @return string
	 */
	protected function buildClass($name)
	{
		$modelClass = $this->option('model') ? Str::studly($this->option('model')) : $this->getNameInput();
		$namespaceModel = $this->rootNamespace().'Models\\'.$modelClass;
		$namespaceModel = str_replace($this->type, '', $namespaceModel);
		$model = str_replace($this->type, '', class_basename($namespaceModel));

		if (Str::startsWith($namespaceModel, 'App\\Models')) {
			$namespace = Str::beforeLast('Database\\Factories\\' . Str::after($namespaceModel, 'App\\Models\\'), '\\');
		} else {
			$namespace = $this->namespaceFromComposer().'Database\\Factories';
		}

		$replace = [
			'{{ factoryNamespace }}' => $namespace,
			'NamespacedDummyModel'   => $namespaceModel,
			'{{ namespacedModel }}'  => $namespaceModel,
			'{{namespacedModel}}'    => $namespaceModel,
			'DummyModel'             => $model,
			'{{ model }}'            => $model,
			'{{model}}'              => $model,
		];

		$stub = $this->files->get($this->getStub());

		return str_replace(array_keys($replace), array_values($replace), $stub);
	}

	/**
	 * Get the destination class path.
	 *
	 * @param  string  $name
	 * @return string
	 */
	protected function getPath($name)
	{
		$name = Str::replaceFirst(strval($this->rootNamespace()), '', $name);
		$path = $this->getPackagePath($this->isLaravelPackage() ? '/src/Database/Factories/' : '/database/factories/');

		return $path . str_replace('\\', '/', $name) . '.php';
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			['model', 'm', InputOption::VALUE_OPTIONAL, 'The name of the model'],
			['legacy', 'l'],
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
			'--model=' => 'Attach custom model name',
			'--legacy' => 'Create the class in legacy format',
		];
	}
}
