<?php

namespace App\Commands\Foundation;

use App\Traits\ClassInputName;
use Illuminate\Foundation\Console\RequestMakeCommand as RequestMake;
use App\Traits\InteractsWithFoundationCommands;

class RequestMakeCommand extends RequestMake
{
	use InteractsWithFoundationCommands;
	use ClassInputName;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'make:request';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new Request class in your package';

	/**
	 * Get the stub file for the generator.
	 *
	 * @return string
	 */
	protected function getStub()
	{
		return __DIR__ . '/stubs/request.stub';
	}
}
