<?php

namespace App\Commands\Foundation;

use LogicException;
use Illuminate\Support\Str;
use App\Traits\InteractsWithFoundationCommands;
use App\Traits\ClassInputName;
use Illuminate\Foundation\Console\PolicyMakeCommand as PolicyMake;

class PolicyMakeCommand extends PolicyMake
{
	use InteractsWithFoundationCommands;
	use ClassInputName;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'make:policy';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new Policy for your package';

	/**
	 * Get the stub file for the generator.
	 *
	 * @return string
	 */
	protected function getStub()
	{
		app()['config']->set('auth.providers.users.model', 'App\Models\User');

		return $this->option('model')
			? __DIR__ . '/stubs/policy.stub'
			: __DIR__ . '/stubs/policy.plain.stub';
	}

	/**
	 * Build the class with the given name.
	 *
	 * @param  string  $name
	 * @return string
	 */
	protected function buildClass($name)
	{
		$stub = $this->replaceUserNamespace(
			parent::buildClass($name)
		);

		$model = Str::studly($this->option('model') ?? $this->getNameInput());
		$model = str_replace(['Policy', 'policy'], '', $model);

		return $model ? $this->replaceModel($stub, $model) : $stub;
	}

	/**
	 * Get the model for the guard's user provider.
	 *
	 * @return string|null
	 */
	protected function userProviderModel()
	{
		$config = $this->laravel['config'];

		$guard = $this->option('guard') ?: $config->get('auth.defaults.guard');

		if (is_null($guardProvider = $config->get('auth.guards.' . $guard . '.provider', 'users'))) {
			throw new LogicException('The [' . $guard . '] guard is not defined in your "auth" configuration file.');
		}

		return $config->get("auth.providers.$guardProvider.model");
	}

	/**
	 * Replace the model for the given stub.
	 *
	 * @param  string  $stub
	 * @param  string  $model
	 * @return string
	 */
	protected function replaceModel($stub, $model)
	{
		$model = str_replace('/', '\\', $model);

		$namespaceModel = $this->namespaceFromComposer() . $model;

		if (Str::startsWith($model, '\\')) {
			$stub = str_replace('NamespacedDummyModel', trim($model, '\\'), $stub);
		} else {
			$stub = str_replace('NamespacedDummyModel', $namespaceModel, $stub);
		}

		$stub = str_replace(
			"use {$namespaceModel};\nuse {$namespaceModel};",
			"use {$namespaceModel};",
			$stub
		);

		$model = class_basename(trim($model, '\\'));
		$dummyUser = class_basename($this->userProviderModel());
		$dummyModel = Str::camel($model) === 'user' ? 'model' : $model;

		$stub = str_replace('DocDummyModel', Str::snake($dummyModel, ' '), $stub);
		$stub = str_replace('DummyModel', $model, $stub);
		$stub = str_replace('dummyModel', Str::camel($dummyModel), $stub);
		$stub = str_replace('DummyUser', $dummyUser, $stub);

		return str_replace('DocDummyPluralModel', Str::snake(Str::plural($dummyModel), ' '), $stub);
	}

	/**
	 * Options to show on make command
	 *
	 * @return array
	 */
	public function getQuestions(): array
	{
		return [
			'--model=' => 'The model that the policy applies to',
			'--guard=' => 'The guard that the policy relies on',
		];
	}
}
