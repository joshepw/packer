<?php

namespace App\Commands\Standalone;

use App\Commands\Helpers\MakeFile;
use App\Traits\FileInputName;
use Symfony\Component\Console\Input\InputArgument;

class Sonar extends MakeFile
{
	use FileInputName;

	/**
	 * The signature of the command.
	 *
	 * @var string
	 */
	protected $signature = 'create:sonar {name?}';

	/**
	 * The description of the command.
	 *
	 * @var string
	 */
	protected $description = 'Create SonarCloud config file';

	public function getStub()
	{
		return __DIR__ . '/stubs/Sonar.stub';
	}

	public function getFilename()
	{
		return 'sonar-project.properties';
	}

	public function getPath()
	{
		return $this->getPackagePath();
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['name', InputArgument::OPTIONAL, 'Sonar project key name (sonar.projectKey)'],
		];
	}
}
