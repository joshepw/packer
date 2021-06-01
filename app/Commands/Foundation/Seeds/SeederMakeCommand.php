<?php

namespace App\Commands\Foundation\Seeds;

use App\Traits\ClassInputName;
use App\Traits\InteractsWithFoundationCommands;
use Illuminate\Database\Console\Seeds\SeederMakeCommand as BaseMakeCommand;
use Illuminate\Support\Str;

class SeederMakeCommand extends BaseMakeCommand
{
	use InteractsWithFoundationCommands;
	use ClassInputName;

	/**
	 * Get the stub file for the generator.
	 *
	 * @return string
	 */
	protected function getStub()
	{
		return __DIR__ . '/stubs/seeder.stub';
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
		$replace = [
			'DummyNamespace' => $this->isLaravelProject() ? 'Database\\Seeders' : $this->rootNamespace().'Database\\Seeders',
			'{{ class }}' => $name,
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
		$path = $this->getPackagePath($this->isLaravelPackage() ? '/src/Database/Seeders/' : '/database/seeders/');

		return $path . str_replace('\\', '/', $name) . '.php';
	}
}
