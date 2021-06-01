<?php

namespace App\Commands\Foundation;

use App\Traits\ClassInputName;
use Illuminate\Foundation\Console\ConsoleMakeCommand as ConsoleMake;
use App\Traits\InteractsWithFoundationCommands;

class ConsoleMakeCommand extends ConsoleMake
{
	use InteractsWithFoundationCommands;
	use ClassInputName;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'make:console';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new Artisan command';

	/**
	 * The type of class being generated.
	 *
	 * @var string
	 */
	protected $type = 'Command';

	/**
	 * Get the stub file for the generator.
	 *
	 * @return string
	 */
	protected function getStub()
	{
		return __DIR__ . '/stubs/console.stub';
	}
}
