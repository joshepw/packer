<?php

namespace App\Commands\Standalone;

use LaravelZero\Framework\Commands\Command;
use App\Commands\Helpers\MakeFile;
use Illuminate\Support\Facades\Cache;

class Composer extends MakeFile
{
	/**
	 * The signature of the command.
	 *
	 * @var string
	 */
	protected $signature = 'create:composer';

	/**
	 * The description of the command.
	 *
	 * @var string
	 */
	protected $description = 'Create composer.json file';

	public function getStub()
	{
		return __DIR__ . '/stubs/Composer.stub';
	}

	public function getFilename()
	{
		return 'composer.json';
	}

	public function getPath()
	{
		return $this->getPackagePath();
	}

	protected function replaceContent(): array
	{
		$extend = parent::replaceContent();

		$composer = $this->getComposer();

		return array_merge($extend, [
			'PackageDescriptionDummy' => $composer->description ?? 'Your package description here',
			'PackageVersionDummy' => $composer->version ?? '0.0.1',
		]);
	}
}
