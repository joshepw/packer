<?php

namespace App\Commands\Standalone;

use Illuminate\Support\Str;
use App\Commands\Helpers\MakeFile;
use App\Traits\FileInputName;
use Symfony\Component\Console\Input\InputArgument;

class Lang extends MakeFile
{
	use FileInputName;

	/**
	 * The signature of the command.
	 *
	 * @var string
	 */
	protected $signature = 'create:lang {name?} {locale?}';

	/**
	 * The description of the command.
	 *
	 * @var string
	 */
	protected $description = 'Create Lang file';

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
		$locale = Str::kebab($this->argument('locale'));
		return $this->getPackagePath('/resources/lang/' . $locale);
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
			['locale', InputArgument::OPTIONAL, 'The locale code (ej. "en")'],
		];
	}
}
