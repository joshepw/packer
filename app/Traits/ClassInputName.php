<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait ClassInputName
{
	/**
	 * Get the desired class name from the input.
	 *
	 * @return string
	 */
	protected function getNameInput()
	{
		$name_input = Str::studly($this->argument('name'));
		$class_type = Str::studly($this->type);

		if (!empty($class_type ?? null) && !Str::contains($name_input, $class_type)) {
			$name_input = $name_input . $class_type;
		}

		return $name_input;
	}
}
