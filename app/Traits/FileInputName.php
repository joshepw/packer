<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait FileInputName
{
	/**
	 * Get the desired class name from the input.
	 *
	 * @return string
	 */
	protected function getNameInput()
	{
		$name = $this->argument('name') ?? cache()->get('package_name', $this->getPackageName());

		return Str::kebab($name);
	}

	/**
	 * Get the desired class name from the input.
	 *
	 * @return string
	 */
	protected function getNameInputStudly()
	{
		$name = $this->argument('name') ?? cache()->get('package_name', $this->getPackageName());

		return Str::studly($name);
	}
}
