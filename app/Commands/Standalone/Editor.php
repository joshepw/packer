<?php

namespace App\Commands\Standalone;

use App\Commands\Helpers\MakeFile;

class Editor extends MakeFile
{
	/**
	 * The signature of the command.
	 *
	 * @var string
	 */
	protected $signature = 'create:editor';

	/**
	 * The description of the command.
	 *
	 * @var string
	 */
	protected $description = 'Create editor config file';

	public function getStub()
	{
		return __DIR__ . '/stubs/Editor.stub';
	}

	public function getFilename()
	{
		return '.editorconfig';
	}

	public function getPath()
	{
		return $this->getPackagePath();
	}
}
