<?php

namespace App\Commands\Standalone;

use App\Traits\FileInputName;
use App\Commands\Helpers\MakeFile;
use Symfony\Component\Console\Input\InputArgument;

class Constant extends MakeFile
{
	use FileInputName;

	/**
	 * The signature of the command.
	 *
	 * @var string
	 */
	protected $signature = 'create:constant {name?}';

	/**
	 * The description of the command.
	 *
	 * @var string
	 */
	protected $description = 'Create Constant for package';

	public function getStub()
	{
		return __DIR__ . '/stubs/Constant.stub';
	}

	public function getFilename()
	{
		return $this->getNameInputStudly() . '.php';
	}

	public function getMakerType()
	{
		return 'StudlyNameEntity';
	}

	public function getPath()
	{
		return $this->getPackagePath($this->isLaravelPackage() ? '/src/Constants' : '/app/Constants');
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
