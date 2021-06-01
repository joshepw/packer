<?php

namespace App\Commands\Foundation;

use App\Traits\ClassInputName;
use Illuminate\Foundation\Console\ChannelMakeCommand as ChannelMake;
use App\Traits\InteractsWithFoundationCommands;

class ChannelMakeCommand extends ChannelMake
{
	use InteractsWithFoundationCommands;
	use ClassInputName;
	
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'make:channel';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new channel class in your Package';

	/**
	 * Get the stub file for the generator.
	 *
	 * @return string
	 */
	protected function getStub()
	{
		return __DIR__ . '/stubs/channel.stub';
	}
}
