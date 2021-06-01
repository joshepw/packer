<?php

namespace App\Commands\Standalone;

use App\Commands\Helpers\MakeFile;
use App\Traits\FileInputName;
use Symfony\Component\Console\Input\InputArgument;

class Config extends MakeFile
{
	use FileInputName;

	/**
	 * The signature of the command.
	 *
	 * @var string
	 */
	protected $signature = 'create:config {name?}';

	/**
	 * The description of the command.
	 *
	 * @var string
	 */
	protected $description = 'Create Config file';

	public function getStub()
	{
		return __DIR__ . '/stubs/Config.stub';
	}

	public function getFilename()
	{
		return $this->getNameInput() . '.php';
	}

	public function getMakerType()
	{
		return 'KebabName.php';
	}

	public function getPath()
	{
		return $this->getPackagePath('/config');
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['name', InputArgument::OPTIONAL, 'The name of the file'],
		];
	}
}
