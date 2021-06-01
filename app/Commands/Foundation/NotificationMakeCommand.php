<?php

namespace App\Commands\Foundation;

use App\Traits\ClassInputName;
use Illuminate\Foundation\Console\NotificationMakeCommand as NotificationMake;
use App\Traits\InteractsWithFoundationCommands;

class NotificationMakeCommand extends NotificationMake
{
	use InteractsWithFoundationCommands;
	use ClassInputName;
	
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'make:notification';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new Notification class in your package';
}
