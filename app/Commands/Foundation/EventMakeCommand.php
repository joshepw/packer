<?php

namespace App\Commands\Foundation;

use App\Traits\ClassInputName;
use Illuminate\Foundation\Console\EventMakeCommand as EventMake;
use App\Traits\InteractsWithFoundationCommands;

class EventMakeCommand extends EventMake
{
	use InteractsWithFoundationCommands;
	use ClassInputName;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'make:event';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new event class';

	/**
	 * Get the stub file for the generator.
	 *
	 * @return string
	 */
	protected function getStub()
	{
		return __DIR__ . '/stubs/event.stub';
	}

	/**
	 * Get the default namespace for the class.
	 *
	 * @param  string  $rootNamespace
	 * @return string
	 */
	protected function getDefaultNamespace($rootNamespace)
	{
		return $rootNamespace . '\Events';
	}
}
