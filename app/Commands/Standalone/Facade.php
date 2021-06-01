<?php

namespace App\Commands\Standalone;

use App\Traits\FileInputName;
use Illuminate\Support\Str;
use App\Commands\Helpers\MakeFile;
use Symfony\Component\Console\Input\InputArgument;

class Facade extends MakeFile
{
	use FileInputName;

	/**
	 * The signature of the command.
	 *
	 * @var string
	 */
	protected $signature = 'create:facade {name?}';

	/**
	 * The description of the command.
	 *
	 * @var string
	 */
	protected $description = 'Create Facade for package';

	public function getStub()
	{
		return __DIR__ . '/stubs/Facade.stub';
	}

	public function getFilename()
	{
		return $this->getNameInputStudly() . 'Facade.php';
	}

	public function getMakerType()
	{
		return 'StudlyNameFacade';
	}

	public function getPath()
	{
		return $this->getPackagePath($this->isLaravelPackage() ? '/src' : '/app');
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
