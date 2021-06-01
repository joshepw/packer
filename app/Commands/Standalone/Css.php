<?php

namespace App\Commands\Standalone;

use App\Commands\Helpers\MakeFile;
use App\Traits\FileInputName;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class Css extends MakeFile
{
	use FileInputName;

	/**
	 * The signature of the command.
	 *
	 * @var string
	 */
	protected $signature = 'create:css {name?} {--scss : Create a Scss file} {--sass : Create a Sass file} {--less : Create a Less file}';

	/**
	 * The description of the command.
	 *
	 * @var string
	 */
	protected $description = 'Create style file';

	public function getStub()
	{
		return __DIR__ . '/stubs/Css.stub';
	}

	public function getFilename()
	{
		return $this->getNameInput() . '.' . $this->getFileType();
	}

	public function getMakerType()
	{
		return 'KebabName.css';
	}

	public function getPath()
	{
		return $this->getPackagePath('/resources/' . $this->getFileType());
	}

	private function getFileType()
	{
		$type = 'css';

		if ($this->option('scss')) {
			$type = 'scss';
		}

		if ($this->option('sass')) {
			$type = 'sass';
		}

		if ($this->option('less')) {
			$type = 'less';
		}

		return $type;
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
			['scss', null, InputOption::VALUE_NONE, 'Create a Scss file'],
			['sass', null, InputOption::VALUE_NONE, 'Create a Sass file'],
			['less', null, InputOption::VALUE_NONE, 'Create a Less file'],
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
				'default' => 'Create a Css file',
				'--scss' => 'Create a Scss file',
				'--sass' => 'Create a Sass file',
				'--less' => 'Create a Less file',
			],
		];
	}
}
