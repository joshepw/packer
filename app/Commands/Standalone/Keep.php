<?php

namespace App\Commands\Standalone;

use App\Commands\Helpers\MakeFile;
use App\Traits\FileInputName;
use Symfony\Component\Console\Input\InputArgument;

class Keep extends MakeFile
{
	use FileInputName;

	/**
	 * The signature of the command.
	 *
	 * @var string
	 */
	protected $signature = 'create:keep {name?}';

	/**
	 * The description of the command.
	 *
	 * @var string
	 */
	protected $description = 'Create .gitkeep file';

	public function getStub()
	{
		return __DIR__ . '/stubs/Keep.stub';
	}

	public function getFilename()
	{
		return '.gitkeep';
	}

	public function getPath()
	{
		return $this->getPackagePath('/' . $this->argument('name'));
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['name', InputArgument::OPTIONAL, 'Path or location to create keep file'],
		];
	}
}
