<?php

namespace App\Providers;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		Lang::setLocale('en');

		Validator::extend('alpha_spaces', function ($attribute, $value) {
			return preg_match('/^[\pL\s_-]+$/u', $value);
		});

		Validator::extend('alpha_name', function ($attribute, $value) {
			return preg_match('/^[\pL\s.-]+$/u', $value);
		});
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->instance('path.lang', $this->langPath());
		$this->app->instance('path.resources', $this->resourcePath());
	}

	/**
	 * Get the path to the language files.
	 *
	 * @return string
	 */
	public function langPath()
	{
		return $this->resourcePath() . DIRECTORY_SEPARATOR . 'lang';
	}

	/**
	 * Get the path to the resources directory.
	 *
	 * @param  string  $path
	 * @return string
	 */
	public function resourcePath($path = '')
	{
		return base_path() . DIRECTORY_SEPARATOR . 'resources' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
	}
}
