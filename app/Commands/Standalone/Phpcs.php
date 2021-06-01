<?php

namespace App\Commands\Standalone;

use App\Commands\Helpers\MakeFile;

class Phpcs extends MakeFile
{
	/**
	 * The signature of the command.
	 *
	 * @var string
	 */
	protected $signature = 'create:phpcs';

	/**
	 * The description of the command.
	 *
	 * @var string
	 */
	protected $description = 'Create PhpCS ruleset file';

	public function getStub()
	{
		if ($this->isLaravelPackage()) {
			return __DIR__ . '/stubs/Phpcs.stub';
		}

		return __DIR__ . '/stubs/PhpcsProject.stub';
	}

	public function getFilename()
	{
		return '.phpcs.xml';
	}

	public function getPath()
	{
		return $this->getPackagePath();
	}
}
