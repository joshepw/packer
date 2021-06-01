<?php

namespace App\Commands\Standalone;

use LaravelZero\Framework\Commands\Command;
use App\Commands\Helpers\MakeFile;

class Bitbucket extends MakeFile
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'create:bitbucket-pipeline';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create bitbucket-pipelines.yml file';

    public function getStub()
    {
        return __DIR__ . '/stubs/Bitbucket.stub';
    }

    public function getFilename()
    {
        return 'bitbucket-pipelines.yml';
    }

    public function getPath()
    {
        return $this->getPackagePath();
    }
}
