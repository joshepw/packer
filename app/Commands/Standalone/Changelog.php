<?php

namespace App\Commands\Standalone;

use App\Commands\Helpers\MakeFile;

class Changelog extends MakeFile
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'create:changelog';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create CHANGELOG.md file';

    public function getStub()
    {
        return __DIR__ . '/stubs/Changelog.stub';
    }

    public function getFilename()
    {
        return 'CHANGELOG.md';
    }

    public function getPath()
    {
        return $this->getPackagePath();
    }

	protected function replaceContent(): array
	{
		$extend = parent::replaceContent();

		return array_merge($extend, [
			'2000-12-31' => now()->format('Y-m-d')
		]);
	}
}
