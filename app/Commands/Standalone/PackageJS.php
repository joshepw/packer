<?php

namespace App\Commands\Standalone;

use App\Commands\Helpers\MakeFile;

class PackageJS extends MakeFile
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'create:package-js';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create package.json file';

    public function getStub()
    {
        return __DIR__ . '/stubs/Package.stub';
    }

    public function getFilename()
    {
        return 'package.json';
    }

    public function getPath()
    {
        return $this->getPackagePath();
    }
}
