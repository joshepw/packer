<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait InteractsWithPackage
{
	use InteractsWithComposer;

	protected function getPackageVendor()
	{
		$namespace = $this->namespaceFromComposer() ?? cache()->get('package_vendor', $this->isLaravelPackage() ? 'Pixel' : 'App');

		return Str::before($namespace, '\\');
	}

	protected function getPackageName()
	{
		$namespace = $this->namespaceFromComposer();
		
		return empty($namespace) ? null : str_replace('\\', '', Str::after($namespace, '\\'));
	}

	protected function getPackageVersion()
	{
		return $this->getComposer()->version ?? null;
	}

	protected function getPackageSlug()
	{
		return $this->getComposer()->name ?? null;
	}

	protected function getPackageDescription()
	{
		return $this->getComposer()->description ?? null;
	}

	protected function getPackageKeywords()
	{
		return implode(',', $this->getComposer()->keywords ?? []);
	}

	protected function getPackagePath($path = '')
	{
		return cache()->get('package_path', ($this->searchLaravelPackagePath() ??
			$this->searchLaravelProjectPath() ??
			getcwd())) . $path;
	}

	public function searchLaravelPackagePath()
	{
		return $this->recursiveParentFinder(getcwd(), ['library', 'laravel-package']);
	}

	public function makeNamespace($package, $vendor = 'Pixel') {
		$package = Str::kebab($package);
		$vendor = Str::kebab($vendor);

		return "$vendor/$package";
	}
}