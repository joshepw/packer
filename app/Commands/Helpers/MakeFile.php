<?php

namespace App\Commands\Helpers;

use App\Traits\InteractsWithPackage;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Cache;
use LaravelZero\Framework\Commands\Command;

abstract class MakeFile extends Command
{
	use InteractsWithPackage;

	abstract public function getStub();

	abstract public function getFilename();

	abstract public function getPath();

	protected $filesystem;

	public function __construct(Filesystem $filesystem)
	{
		parent::__construct();
		$this->filesystem = $filesystem;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$this->makeFile();
	}

	protected function makeFile()
	{
		$this->makeDir();
		$file_path = $this->getPath() . '/' . $this->getFilename();
		$relative_path = str_replace(getcwd(), '', $file_path);

		if (file_exists($file_path) && $this->filesystem->isFile($file_path)) {
			$this->line("The file <info>{$relative_path}</info> already exists.");

			if (!$this->confirm("Do you want to overwrite the file?")) {
				return;
			}
		}

		if ($this->filesystem->put($file_path, $this->getReplaceContent())) {
			$this->line("<info>✔</info> The file <info>{$relative_path}</info> was created.");
		} else {
			$this->error("Error to create file.");
		}
	}

	public function confirm($question, $default = null)
	{
		if (is_null($default)) {
			$default = Cache::get('overwrite_files', false);
		}

		if ($this->input->getOption('quiet')) {
			return $default;
		}

		return $this->output->confirm($question, $default);
	}

	/**
	 * @return bool
	 */
	protected function makeDir()
	{
		if (!$this->filesystem->isDirectory($this->getPath())) {
			return $this->filesystem->makeDirectory($this->getPath(), 0755, true);
		}
	}

	protected function getContent()
	{
		return $this->filesystem->get($this->getStub());
	}

	protected function getReplaceContent()
	{
		$content = $this->getContent();

		$content = str_replace(
			$this->stringsToReplace(),
			$this->replaceContent(),
			$content
		);

		return str_replace('App\;', 'App;', $content);
	}

	protected function stringsToReplace()
	{
		return [
			'LowerCaseDummyVendor',
			'LowerCaseDummyPackageName',
			'StudlyDummyVendor',
			'StudlyDummyPackageName',
			'KebabDummyVendor',
			'KebabDummyPackageName',
			'DummyAuthorName',
			'DummyAuthorEmail',
			'DummyFileName',
			'LowerDummyName',
			'StudlyDummyName',
			'DummyKeywords',
		];
	}

	protected function replaceContent()
	{
		$vendor      = $this->getPackageVendor();
		$packageName = $this->getPackageName() ?? cache()->get('package_name');
		$keywords    = $this->getPackageKeywords() ?? cache()->get('keywords');

		$name = method_exists($this, 'getNameInput') ? $this->getNameInput() : false;
		$name_studly = method_exists($this, 'getNameInputStudly') ? $this->getNameInputStudly() : false;

		if (!$keywords) {
			$composerKeywords = '';
		} else {
			$composerKeywords = PHP_EOL;
			$keywords = explode(',', $keywords);
			foreach ($keywords as $keyword) {
				$composerKeywords .= "\t\t" . '"' . $keyword . '",' . PHP_EOL;
			}

			$composerKeywords .= "\t";
			$composerKeywords = Str::replaceLast(',', '', $composerKeywords);
		}

		return [
			Str::snake($vendor),
			Str::snake($packageName),
			Str::studly($vendor),
			Str::studly($packageName),
			Str::kebab($vendor),
			Str::kebab($packageName),
			cache()->get('author_name') ?? 'Ivan Suazo',
			cache()->get('author_email') ?? 'ivan@pixel.hn',
			$this->getFilename() ?? false,
			$name,
			$name_studly,
			$composerKeywords,
		];
	}

	public function getMakerType()
	{
		return $this->getFilename();
	}
}
