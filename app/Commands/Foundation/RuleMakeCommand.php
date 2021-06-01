<?php

namespace App\Commands\Foundation;

use App\Traits\ClassInputName;
use Illuminate\Foundation\Console\RuleMakeCommand as RuleMake;
use App\Traits\InteractsWithFoundationCommands;

class RuleMakeCommand extends RuleMake
{
	use InteractsWithFoundationCommands;
	use ClassInputName;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'make:rule';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new Rule class for your package';

	/**
	 * Get the stub file for the generator.
	 *
	 * @return string
	 */
	protected function getStub()
	{
		return __DIR__ . '/stubs/rule.stub';
	}
}
