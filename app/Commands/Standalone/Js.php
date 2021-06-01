<?php

namespace App\Commands\Standalone;

use App\Commands\Helpers\MakeFile;
use App\Traits\FileInputName;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class Js extends MakeFile
{
	use FileInputName;

	/**
	 * The signature of the command.
	 *
	 * @var string
	 */
	protected $signature = 'create:js {name?} {--ts : Create a typescript file} {--jsx : Create a react javascript file} {--tsx : Create a react typescript file}';

	/**
	 * The description of the command.
	 *
	 * @var string
	 */
	protected $description = 'Create Javascript file';

	public function getStub()
	{
		return __DIR__ . '/stubs/Js.stub';
	}

	public function getFilename()
	{
		$type = 'js';

		if ($this->input->getOption('ts')) {
			$type = 'ts';
		}

		if ($this->input->getOption('jsx')) {
			$type = 'jsx';
		}

		if ($this->input->getOption('tsx')) {
			$type = 'tsx';
		}

		return $this->getNameInput() . ".$type";
	}

	public function getMakerType()
	{
		return 'KebabName.js';
	}

	public function getPath()
	{
		return $this->getPackagePath('/resources/js');
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

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			['ts', null, InputOption::VALUE_NONE, 'Create a typescript file'],
			['jsx', null, InputOption::VALUE_NONE, 'Create a react javascript file'],
			['tsx', null, InputOption::VALUE_NONE, 'Create a react typescript file'],
		];
	}

	/**
	 * Options to show on make command
	 *
	 * @return array
	 */
	public function getQuestions(): array
	{
		return [
			'type' => [
				'default' => 'Create a javascript file (.js)',
				'--jsx' => 'Create a react javascript file (.jsx)',
				'--ts' => 'Create a typescript file (.ts)',
				'--tsx' => 'Create a react typescript file (.tsx)',
			],
		];
	}
}
