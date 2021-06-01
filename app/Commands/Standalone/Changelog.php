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
}
