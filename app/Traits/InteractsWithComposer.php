<?php

namespace App\Traits;

use stdClass;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use App\Exceptions\SaveException;

trait InteractsWithComposer
{
	protected $composer_path;

	protected function getComposer()
	{
		return $this->getComposerFromPath(($this->composer_path ?? Cache::get('package_path', getcwd())));
	}

	private function getComposerFromPath($path)
	{
		return file_exists("$path/composer.json") ? json_decode(file_get_contents("$path/composer.json")) : null;
	}

	private function recursiveParentFinder($path, array $types = ['project'])
	{
		if (empty($path)) {
			return null;
		}

		$composer = file_exists($path) ? $this->getComposerFromPath($path) : null;

		if (file_exists($path) && $composer && in_array($composer->type ?? null, $types)) {
			return $path;
		} else {
			return $path !== realpath($path . '/../') ? $this->recursiveParentFinder(realpath($path . '/../'), $types) : null;
		}
	}

	protected function saveComposer($json, $path)
	{
		$new_composer = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

		if (File::put("{$path}/composer.json", $new_composer) === false) {
			throw new SaveException("Cannot write to composer.json [$path]");
		}
	}

	public function isLaravelProject()
	{
		return optional($this->getComposer())->type === 'project';
	}

	public function isLaravelPackage()
	{
		return optional($this->getComposer())->type === 'laravel-package' ||
            optional($this->getComposer())->name === 'pixel/support';
	}

	public function namespaceFromComposer()
	{
		$content = $this->getComposer();
		$psr     = 'psr-4';

		if (empty($content)) {
			return null;
		}

		cache()->forever('namespaceFromComposer', key($content->autoload->$psr));

		return key($content->autoload->$psr);
	}

	public function searchLaravelProjectPath()
	{
		return $this->recursiveParentFinder(getcwd());
	}

	protected function registerPackage($package_slug, $package_path)
	{
		$path = $this->searchLaravelProjectPath();
		$composer = $this->getComposerFromPath($path);

		if (empty($composer->repositories ?? null)) {
			$composer->repositories = [];
		} else {
			$composer->repositories = (array) $composer->repositories ?? [];
		}

		$filtered = array_filter($composer->repositories, function ($repository) use ($package_path) {
			return ($repository->type ?? null) === 'path'
				&& ($repository->url ?? null) === $package_path;
		});

		if (count($filtered) === 0) {
			$this->info('Register composer repository for package.');

			$repository = new stdClass;
			$repository->type = 'path';
			$repository->url = $package_path;

			$composer->repositories[] = $repository;
		} else {
			$this->warn('Composer repository for package is already registered.');
		}

		if (empty($composer->require ?? null)) {
			$composer->require = [];
		} else {
			$composer->require = (array) $composer->require ?? [];
		}

		$composer->require[$package_slug] = '@dev';

		$this->saveComposer($composer, $path);
		$this->info('Package was successfully registered in composer.json.');
	}

	protected function unregisterPackage($package_slug, $package_path)
	{
		$path = $this->searchLaravelProjectPath();
		$composer = $this->getComposerFromPath($path);

		if (empty($composer->require ?? null)) {
			$composer->require = [];
		} else {
			$composer->require = (array) $composer->require ?? [];
		}

		if (!empty($composer->require ?? null)) {
			unset($composer->require[$package_slug]);
		}

		if (empty($composer->repositories ?? null)) {
			$composer->repositories = [];
		} else {
			$composer->repositories = (array) $composer->repositories ?? [];
		}

		$composer->repositories = array_filter($composer->repositories, function ($repository) use ($package_path) {
			return ($repository->type ?? null) !== 'path'
				|| ($repository->url ?? null) !== $package_path;
		});

		$this->saveComposer($composer, $path);
		$this->info('Package was successfully unregistered from composer.json.');
	}

	protected function findLocalInstalledPackages()
	{
		$path = $this->searchLaravelProjectPath();
		$composer = $this->getComposerFromPath($path);

		if (empty($composer->require ?? null)) {
			$composer->require = [];
		} else {
			$composer->require = (array) $composer->require ?? [];
		}

		return array_filter($composer->require, function ($version, $key) {
			return $version === '@dev' || Str::contains($key, ['pixel', strtolower(config('package.vendor'))]);
		}, ARRAY_FILTER_USE_BOTH);
	}
}
