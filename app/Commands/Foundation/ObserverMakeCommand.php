<?php

namespace App\Commands\Foundation;

use App\Traits\ClassInputName;
use Illuminate\Foundation\Console\ObserverMakeCommand as ObserverMake;
use App\Traits\InteractsWithFoundationCommands;

class ObserverMakeCommand extends ObserverMake
{
	use InteractsWithFoundationCommands;
	use ClassInputName;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'make:observer';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new Observer class in your package';
}
