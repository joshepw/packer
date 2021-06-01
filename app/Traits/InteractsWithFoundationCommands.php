<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

trait InteractsWithFoundationCommands
{
	use InteractsWithPackage;

	public function handle()
	{
		$this->composer_path = $this->getPackagePath();

		if ($this->isReservedName($this->getNameInput())) {
			$this->error('The name "' . $this->getNameInput() . '" is reserved by PHP.');

			return false;
		}

		$name = $this->qualifyClass($this->getNameInput());
		$path = $this->getPath($name);
		$relative_path = str_replace($this->composer_path, '', $path);

		if ((!$this->hasOption('force') ||
				!$this->option('force')) &&
			$this->alreadyExists($this->getNameInput())
		) {
			$this->error($this->type . ' already exists!');

			return false;
		}

		$this->makeDirectory($path);

		if ($this->files->put($path, $this->sortImports($this->buildClass($name)))) {
			$this->line("<info>✔</info> {$this->type} created successfully at <info>{$relative_path}</info>.");
		} else {
			$this->error("Error to create file.");
		}
	}

	/**
	 * Get the root namespace for the class.
	 *
	 * @return string
	 */
	protected function rootNamespace()
	{
		return $this->namespaceFromComposer();
	}

	/**
	 * Get class path
	 *
	 * @param string $name
	 * @return string
	 */
	public function getPath($name)
	{
		$name = Str::replaceFirst($this->rootNamespace(), '', $name);
		$path = $this->getPackagePath('/');
		$path = $this->isLaravelPackage() ? $path . 'src/' : $path . 'app/';

		return $path . str_replace('\\', '/', $name) . '.php';
	}

	/**
	 * Get Class type
	 *
	 * @return string|null
	 */
	public function getMakerType()
	{
		return "StudlyName$this->type" ?? null;
	}
}
