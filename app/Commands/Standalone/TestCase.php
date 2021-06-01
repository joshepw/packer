<?php

namespace App\Commands\Standalone;

use App\Commands\Helpers\MakeFile;

class TestCase extends MakeFile
{
	/**
	 * The signature of the command.
	 *
	 * @var string
	 */
	protected $signature = 'create:test-case';

	/**
	 * The description of the command.
	 *
	 * @var string
	 */
	protected $description = 'Create Test Case class';

	public function getStub()
	{
		return __DIR__ . '/stubs/TestCase.stub';
	}

	public function getFilename()
	{
		return 'TestCase.php';
	}

	public function getPath()
	{
		return $this->getPackagePath('/tests');
	}
}
