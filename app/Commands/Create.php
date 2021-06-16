<?php

namespace App\Commands;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use LaravelZero\Framework\Commands\Command;

use App\MenuItems\ExitMenu;
use App\MenuItems\ContinueMenu;

use App\Traits\WithCliMenu;
use App\Traits\InteractsWithPackage;

use PhpSchool\CliMenu\CliMenu;
use PhpSchool\CliMenu\Builder\SplitItemBuilder;
use PhpSchool\CliMenu\MenuItem\CheckboxItem;
use PhpSchool\CliMenu\MenuItem\SelectableItem;

class Create extends Command
{
	use WithCliMenu;
	use InteractsWithPackage;

	const COLUMNS = 2;

	/**
	 * The signature of the command.
	 *
	 * @var string
	 */
	protected $signature = 'package:new {name?} {author_name?} {author_email?}';

	/**
	 * The description of the command.
	 *
	 * @var string
	 */
	protected $description = 'Create scaffolding of your package';

	/**
	 * The list of options for wizzard
	 *
	 * @var array
	 */
	protected $options_list = [
		'base' => 'Create base configuration files (*)',
		'required' => 'Create required files for package (*)',
		'unit' => 'Create unit testing files (?)',
		'docs' => 'Create documentation files (?)',
		'ci' => 'Create CI configuration files',
		'api' => 'Create API routes and controller',
		'web' => 'Create Web routes and controller',
		'lang' => 'Create lang and locale files',
		'assets' => 'Create assets files (js,scss,views)',
	];

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
	 * Author name
	 *
	 * @var string
	 */
	protected $author_name;

	/**
	 * Author email
	 *
	 * @var string
	 */
	protected $author_email;

	/**
	 * Laravel Project path
	 *
	 * @var string
	 */
	protected $project_path;

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		if (empty($this->searchLaravelPackagePath() ?? $this->searchLaravelProjectPath())) {
			return $this->error('This path not have any Laravel package or project');
		}

		$this->initializePackageOptions();

		$this->initMenu();

		$item_title = function() {
			return 'Package name: '.(empty($this->package_name) ? ' - - - ' : $this->package_name);
		};

		if (!empty($this->package_name)) {
			$this->menu->addStaticItem('* '.$item_title());
		} else {
			$this->menu->addMenuItem((new SelectableItem($item_title(), function(CliMenu $menu) use ($item_title) {
				$this->promptPackageName($menu);
				optional($menu->getSelectedItem())->setText($item_title());
				$menu->redraw();
			})));
		}

		if (!empty($this->project_path)) {
			$this->menu->addStaticItem('* Laravel path: '.$this->project_path);
		}

		if (!empty($this->package_name)) {
			$this->menu->addMenuItem((new CheckboxItem('Overwrite existing files?', function(CliMenu $menu) {
				Cache::forever('overwrite_files', optional($menu->getSelectedItem())->getChecked() ?? false);
			})));
		}

		$this->menu->addLineBreak('·');
		
		collect($this->options_list)->chunk(self::COLUMNS)->each(function (Collection $chunk) {
			$this->menu->addSplitItem(function (SplitItemBuilder $split) use ($chunk) {	
				$chunk->each(function ($option, $key) use ($split) {
					$this->addOptionToMenu($key, $option, $split);
				});

				for ($i = 0; $i < self::COLUMNS - $chunk->count(); $i++) {
					$split->addStaticItem('');
				}
			});
		});

		$this->menu->addLineBreak('·')
			->addSplitItem(function (SplitItemBuilder $split) {
				$split->addStaticItem('(*) Required files for functionality')
					->addStaticItem('(?) Recommended files for functionality');
			});

		$this->addFooter([
			new ContinueMenu(function(CliMenu $menu) {
				$this->continueCreation($menu);
			}, false),
			new ExitMenu,
		]);

		$this->open();
	}

	/**
	 * Continue the creation of package
	 *
	 * @param CliMenu $menu
	 * @return void
	 */
	private function continueCreation(CliMenu $menu)
	{
		if (empty($this->package_name)) {
			return $menu->flash('The package name is required')->display();
		}

		$this->validateAuthorDetails($menu);
	
		$this->createFilesTask('base', function() {
			$this->runCommandTask('create:composer', ['--quiet' => true]);
			$this->runCommandTask('create:editor', ['--quiet' => true]);
			$this->runCommandTask('create:gitattributes', ['--quiet' => true]);
			$this->runCommandTask('create:gitignore', ['--quiet' => true]);
		});

		$this->createFilesTask('required', function() {
			$this->runCommandTask('create:config', ['name' => $this->package_name, '--quiet' => true]);
			$this->runCommandTask('create:provider', ['--quiet' => true]);
			$this->runCommandTask('make:seeder', ['name' => 'Database', '--quiet' => true]);
		});

		$this->createFilesTask('unit', function() {
			$this->runCommandTask('create:phpunit', ['--quiet' => true]);
			$this->runCommandTask('create:test-case', ['--quiet' => true]);
			$this->runCommandTask('create:test-case', ['--browser' => true, '--quiet' => true]);
			$this->runCommandTask('make:test', ['name' => $this->package_name, '--unit' => true, '--quiet' => true]);
		});

		$this->createFilesTask('docs', function() {
			$this->runCommandTask('create:changelog', ['--quiet' => true]);
			$this->runCommandTask('create:contributing', ['--quiet' => true]);
			$this->runCommandTask('create:license', ['--quiet' => true]);
			$this->runCommandTask('create:readme', ['--quiet' => true]);
		});

		$this->createFilesTask('ci', function() {
			$this->runCommandTask('create:phpcs', ['--quiet' => true]);
			$this->runCommandTask('create:sonar', ['name' => '', '--quiet' => true]);
			$this->runCommandTask('create:bitbucket-pipeline', ['--quiet' => true]);
		});

		$this->createFilesTask('api', function() {
			$this->runCommandTask('make:routefile', ['name' => 'api', '--quiet' => true]);

			if (!$this->hasMenuOption('web')) {
				$this->runCommandTask('make:controller', ['name' => $this->package_name, '--api' => true, '--quiet' => true]);
			}
		});

		$this->createFilesTask('web', function() {
			$this->runCommandTask('make:controller', ['name' => $this->package_name, '--quiet' => true]);
			$this->runCommandTask('make:routefile', ['name' => 'web', '--quiet' => true]);
		});

		$this->createFilesTask('lang', function() {
			$this->runCommandTask('create:lang', ['name' => 'lang', 'locale' => 'en', '--quiet' => true]);
			$this->runCommandTask('create:lang', ['name' => 'lang', 'locale' => 'es', '--quiet' => true]);
		});

		$this->createFilesTask('assets', function() {
			$this->runCommandTask('create:package-js', ['--quiet' => true]);
			$this->runCommandTask('create:webpack', ['--quiet' => true]);
			$this->runCommandTask('create:css', ['name' => $this->package_name, '--scss' => true, '--quiet' => true]);
			$this->runCommandTask('create:js', ['name' => $this->package_name, '--quiet' => true]);
			$this->runCommandTask('create:keep', ['name' => 'resources/icons', '--quiet' => true]);
			$this->runCommandTask('create:keep', ['name' => 'resources/components', '--quiet' => true]);
			$this->runCommandTask('create:keep', ['name' => 'public', '--quiet' => true]);
			$this->runCommandTask('create:view', ['name' => 'index', '--quiet' => true]);
			$this->runCommandTask('create:view', ['name' => $this->package_name, '--layout' => true, '--quiet' => true]);
		});

		$this->task('Create required folders and keep files', function() {
			$files = [
				'src/Database/Migrations',
				'src/Database/Factories',
				'src/Http/Resources',
				'src/Models',
				'src/Exceptions',

				'resources',
				'config',
				'tests',
			];

			foreach ($files as $file) {
				if (! file_exists($file)) {
					$this->runCommandTask('create:keep', ['name' => $file, '--quiet' => true]);
				}		
			}
		});
		
		$this->initializeGit();
		$this->initializeComposer();
		$this->initializeNPM();
		$this->attachPackageToLaravel();
		$this->openInVisualStudioCode();
	}

	/**
	 * Initialize properties
	 *
	 * @return void
	 */
	private function initializePackageOptions()
	{
		$this->composer_path = $this->searchLaravelPackagePath() ?? $this->searchLaravelProjectPath();
		$this->project_path = $this->composer_path;

		if ($this->isLaravelPackage()) {
			$this->package_name = Str::studly($this->getPackageName());
		}

		$this->author_name = Cache::get('author_name', config('package.author'));
		$this->author_email = Cache::get('author_email', config('package.email'));

		if (!empty($this->package_name)) {
			$this->package_slug = $this->makeNamespace($this->package_name, $this->getPackageVendor());
			$this->setPath();
		}

		cache()->forever('package_vendor', config('package.vendor', 'Pixel'));
	}

	/**
	 * Validate if author details is completed
	 *
	 * @param CliMenu $menu
	 * @return void
	 */
	private function validateAuthorDetails(CliMenu $menu)
	{
		if (empty($this->author_name)) {
			$this->author_name =$this->askForOption('author_name', 'Enter the author name', $menu, 'required|min:3|max:32|alpha_name');
			Cache::forever('author_name', $this->author_name);

			$this->runCommandTask('set:author', ['value' => $this->author_name]);
		}
		
		if (empty($this->author_email)) {
			$this->author_email = $this->askForOption('author_email', 'Enter the author email', $menu, 'required|min:3|max:60|email');
			Cache::forever('author_email', $this->author_email);

			$this->runCommandTask('set:email', ['value' => $this->author_email]);
		}

		$menu->close();
	}

	/**
	 * Prop the package name
	 *
	 * @param CliMenu $menu
	 * @return void
	 */
	protected function promptPackageName(CliMenu $menu)
	{
		$this->package_name = Str::studly($this->askForOption('package_name', 'Enter the package name', $menu, 'required|min:3|max:20|alpha_spaces'));
		Cache::forever('package_name', $this->package_name);

		$this->setPath();
		$this->package_slug = $this->makeNamespace($this->package_name, $this->getPackageVendor());
	}

	/**
	 * Create files task
	 *
	 * @param string $key
	 * @param callable $callback
	 * @return void
	 */
	protected function createFilesTask($key, callable $callback)
	{
		if ($this->hasMenuOption($key)) {
			$text = $this->options_list[$key];
			$this->task($text, $callback);
		}
	}

	/**
	 * Get package path
	 *
	 * @return string
	 */
	private function getBasePath()
	{
		if ($package_path = $this->searchLaravelPackagePath()) {
			return $package_path;
		} elseif ($laravel_path = $this->searchLaravelProjectPath()) {
			$path = "$laravel_path/packages";
		} else {
			$path = getcwd();
		}

		return "{$path}/{$this->package_name}";
	}

	/**xx
	 * Define the base path
	 *
	 * @return void
	 */
	protected function setPath()
	{
		$base = $this->getBasePath();
		Cache::forever('package_path', $base);
		$this->composer_path = $base;
	}

	/**
	 * Install all composer dependencies
	 *
	 * @return void
	 */
	protected function initializeGit()
	{
		
		if (!file_exists('.git') && $this->shellCommandExist('git')) {
			$this->task('Initialize Git in package folder', function() {
				chdir($this->getBasePath());
				shell_exec('git init --quiet && git add .');
				shell_exec("git commit -m \"(✔) Created package\" --author=\"$this->author_name <$this->author_email>\"");
			});
		}
	}

	/**
	 * Install all composer dependencies
	 *
	 * @return void
	 */
	protected function initializeComposer()
	{
		if (!file_exists('vendor') && 
			$this->shellCommandExist('composer') && 
			$this->confirm('Do you want to run composer install?')) {
				$this->task('Run composer install', function() {
					shell_exec('composer install -q');
				});

				if(file_exists('.git') && $this->shellCommandExist('git')) {
					$this->task('Add hooks to package git', function() {
						shell_exec('composer hooks:add -q');
					});
				}
		}
	}

	/**
	 * Install all node.js dependencies
	 *
	 * @return void
	 */
	protected function initializeNPM()
	{
		if (file_exists('package.json') && 
			!file_exists('node_modules') && $this->shellCommandExist('npm') && 
			$this->confirm('Do you want to run npm install?')) {
				$this->task('Run npm install', function() {
					shell_exec('npm install &>/dev/null');
				});
		}
	}

	/**
	 * Add package to composer.json laravel project and install via composer
	 *
	 * @return void
	 */
	private function attachPackageToLaravel()
	{
		$laravel = $this->searchLaravelProjectPath();

		if (!empty($laravel) && 
			$this->shellCommandExist('composer') && 
			$this->confirm('Do you want to attach this package to Laravel project?')) {
				$this->registerPackage($this->package_slug, $this->getBasePath());
				chdir($laravel);

				$this->task('Run composer update', function() {
					shell_exec('composer update -q');
				});
		}
	}

	/**
	 * Verify the sheel command exist
	 *
	 * @param string $cmd
	 * @return bool
	 */
	private function shellCommandExist(string $cmd) {
		return !empty(shell_exec(sprintf("which %s", escapeshellarg($cmd))));
	}

	/**
	 * Open the project in VisualStudio Code
	 *
	 * @return void
	 */
	private function openInVisualStudioCode()
	{
		if ($this->shellCommandExist('code') && 
			$this->confirm('Do you want to open project in VSCode?', true)) {
				chdir($this->getBasePath());
				shell_exec('code .');
		}
	}

	/**
	 * Run the command assigned
	 *
	 * @param string $command
	 * @param array $args
	 * @return void
	 */
	private function runCommandTask(string $command, array $args = [])
	{
		$this->callSilent($command, $args);
	}
}
