<?php

namespace App\Commands\Standalone;

use App\Traits\FileInputName;
use Illuminate\Support\Str;
use App\Commands\Helpers\MakeFile;
use Symfony\Component\Console\Input\InputArgument;

class Entity extends MakeFile
{
	use FileInputName;

	/**
	 * The signature of the command.
	 *
	 * @var string
	 */
	protected $signature = 'create:entity {name?}';

	/**
	 * The description of the command.
	 *
	 * @var string
	 */
	protected $description = 'Create Entity for package';

	public function getStub()
	{
		return __DIR__ . '/stubs/Entity.stub';
	}

	public function getFilename()
	{
		return $this->getNameInputStudly() . 'Entity.php';
	}

	public function getMakerType()
	{
		return 'StudlyNameEntity';
	}

	public function getPath()
	{
		return $this->getPackagePath($this->isLaravelPackage() ? '/src/Mappings' : '/app/Mappings');
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
