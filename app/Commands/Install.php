<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;

use App\Traits\InteractsWithPackage;

class Install extends Command
{
	use InteractsWithPackage;

	/**
	 * The signature of the command.
	 *
	 * @var string
	 */
	protected $signature = 'package:install {path?}';

	/**
	 * The description of the command.
	 *
	 * @var string
	 */
	protected $description = 'Attach package to laravel project';

	/**
	 * Package slug for composer
	 *
	 * @var string
	 */
	protected $package_slug;

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$laravel = $this->searchLaravelProjectPath();

		if (empty($laravel)) {
			return $this->error('This path not have any Laravel project');
		}

		$path = realpath($this->argument('path') ?? $this->ask('Enter the package path'));

		if (!$path) {
			return $this->error('The package path not exist.');
		}

		if (!file_exists("{$path}/composer.json")) {
			return $this->error('The package path is not a valid composer package');
		}

		$this->package_slug = $this->getComposerFromPath($path)->name;

		$this->registerPackage($this->package_slug, $path);

		$this->task('Run composer update', function() use ($laravel) {
			chdir($laravel);
			shell_exec('composer update -q');
		});
	}
}
