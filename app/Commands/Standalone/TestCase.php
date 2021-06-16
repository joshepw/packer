<?php

namespace App\Commands\Standalone;

use App\Commands\Helpers\MakeFile;
use Symfony\Component\Console\Input\InputOption;

class TestCase extends MakeFile
{
	/**
	 * The signature of the command.
	 *
	 * @var string
	 */
	protected $signature = 'create:test-case {--browser : Create a Browser test case}';

	/**
	 * The description of the command.
	 *
	 * @var string
	 */
	protected $description = 'Create Test Case class';

	public function getStub()
	{
		if ($this->input->getOption('browser')) {
			return __DIR__ . '/stubs/BrowserTestCase.stub';
		}

		return __DIR__ . '/stubs/TestCase.stub';
	}

	public function getFilename()
	{
		if ($this->input->getOption('browser')) {
			return 'BrowserTestCase.php';
		}

		return 'TestCase.php';
	}

	public function getPath()
	{
		return $this->getPackagePath('/tests');
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			['browser', null, InputOption::VALUE_NONE, 'Create a Browser test case'],
		];
	}
}
