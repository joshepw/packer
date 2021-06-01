<?php

namespace App\Commands;

use App\MenuItems\ExitMenu;
use App\MenuItems\ContinueMenu;

use App\Traits\WithCliMenu;
use App\Traits\InteractsWithPackage;

use Illuminate\Support\Str;
use PhpSchool\CliMenu\CliMenu;
use PhpSchool\CliMenu\Style\RadioStyle;
use LaravelZero\Framework\Commands\Command;
use PhpSchool\CliMenu\MenuItem\RadioItem;

class Remove extends Command
{
	use WithCliMenu;
	use InteractsWithPackage;

	/**
	 * The signature of the command.
	 *
	 * @var string
	 */
	protected $signature = 'package:remove';

	/**
	 * The description of the command.
	 *
	 * @var string
	 */
	protected $description = 'Remove a selected package';

	/**
	 * Package name setup
	 *
	 * @var string
	 */
	protected $package_name;

	/**
	 * Package slug for composer
	 *
	 * @var string
	 */
	protected $package_slug;

	/**
	 * Package vendor for composer
	 *
	 * @var string
	 */
	protected $package_vendor;

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$packages = $this->findLocalInstalledPackages();

		if (empty($packages)) {
			return $this->error('The Laravel project not have packages to remove');
		}

		if ($package = $this->searchLaravelPackagePath()) {
			$this->composer_path = $package;

			$this->package_name = Str::studly($this->getPackageName());
			$this->package_vendor = Str::studly($this->getPackageVendor());
			$this->package_slug = $this->getPackageSlug();

			if ($this->confirm("Do you want to remove the {$this->package_slug} package?")) {
				$this->removePackageBySlug();
			}
		} else {
			$this->initMenu();
	
			$this->menu->addStaticItem('Remove the selected package:')->addLineBreak();
	
			foreach ($packages as $package => $version) {
				$name = str_replace('/', '', Str::after($package, '/'));
				$name = Str::studly($name);
	
				$vendor = str_replace('/', '', Str::before($package, '/'));
				$vendor = Str::studly($vendor);
	
				$radio = new RadioItem("{$name} ({$package})", function() use ($name, $package, $vendor) {
					$this->package_slug = $package;
					$this->package_name = $name;
					$this->package_vendor = $vendor;
				});
	
				$radio->setStyle((new RadioStyle)
					->setCheckedMarker('[*] ')
					->setUncheckedMarker('[ ] '));
	
				$this->menu->addMenuItem($radio);
			}
	
			$this->addFooter([
				new ContinueMenu(function(CliMenu $menu) {
					$this->removePackageBySlug($menu);
				}, false),
				new ExitMenu,
			]);
	
			$this->open();
		}
	}

	public function removePackageBySlug(CliMenu $menu = null)
	{
		$this->unregisterPackage($this->package_slug, $this->getPackagePathFromLoader() ?? '');

		$this->task('Run composer update', function() use ($menu) {
			$laravel = $this->searchLaravelProjectPath();
			chdir($laravel);
			shell_exec('composer update -q');

			if (!empty($menu)) {
				$menu->close();
			}
		});
	}

	/**
	 * Get the package base path
	 *
	 * @return string|bool
	 */
	public function getPackagePathFromLoader()
	{
		$path = $this->searchLaravelProjectPath() . '/vendor/autoload.php';

		if (file_exists($path)) {
			$loader = require $path;
			$class = "{$this->package_vendor}\\{$this->package_name}\\{$this->package_name}ServiceProvider";
			$url = $loader->findFile($class);

			return realpath(str_replace("src/{$this->package_name}ServiceProvider.php", '', $url));
		}

		return false;
	}
}
