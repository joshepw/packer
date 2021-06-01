<?php

namespace App\Commands\Standalone;

use App\Commands\Helpers\MakeFile;
use App\Traits\FileInputName;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class View extends MakeFile
{
	use FileInputName;

	/**
	 * The signature of the command.
	 *
	 * @var string
	 */
	protected $signature = 'create:view {name?} {--layout : Create a layout view} {--partial : Create a partial/component view}';

	/**
	 * The description of the command.
	 *
	 * @var string
	 */
	protected $description = 'Create view file';

	public function getStub()
	{
		if ($this->input->getOption('layout')) {
			return __DIR__ . '/stubs/ViewLayout.stub';
		}

		if ($this->input->getOption('partial')) {
			return __DIR__ . '/stubs/ViewPartial.stub';
		}

		return __DIR__ . '/stubs/View.stub';
	}

	public function getFilename()
	{
		return $this->getNameInput() . ".blade.php";
	}

	public function getMakerType()
	{
		return 'KebabName.blade.php';
	}

	public function getPath()
	{
		$path = '';

		if ($this->input->getOption('layout')) {
			$path = '/layouts';
		}

		if ($this->input->getOption('partial')) {
			$path = '/partials';
		}

		return $this->getPackagePath('/resources/views' . $path);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['name', InputArgument::OPTIONAL, 'The name of the view file'],
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
			['layout', null, InputOption::VALUE_NONE, 'Create a layout view'],
			['partial', null, InputOption::VALUE_NONE, 'Create a partial/component view'],
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
				'default' => 'Create a view',
				'--layout' => 'Create a layout view',
				'--partial' => 'Create a partial/component view',
			],
		];
	}
}
