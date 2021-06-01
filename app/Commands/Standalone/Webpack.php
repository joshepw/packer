<?php

namespace App\Commands\Standalone;

use App\Commands\Helpers\MakeFile;

class Webpack extends MakeFile
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'create:webpack';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create Laravel mix webpack file';

    public function getStub()
    {
        return __DIR__ . '/stubs/Webpack.stub';
    }

    public function getFilename()
    {
        return 'webpack.mix.js';
    }

    public function getPath()
    {
        return $this->getPackagePath();
    }
}
