<?php

namespace App\Commands\Foundation;

use App\Traits\ClassInputName;
use Illuminate\Foundation\Console\ExceptionMakeCommand as ExceptionMake;
use App\Traits\InteractsWithFoundationCommands;

class ExceptionMakeCommand extends ExceptionMake
{
	use InteractsWithFoundationCommands;
	use ClassInputName;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'make:exception';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new custom exception class';

	/**
	 * Get the stub file for the generator.
	 *
	 * @return string
	 */
	protected function getStub()
	{
		if ($this->option('render')) {
			return $this->option('report')
				? __DIR__ . '/stubs/exception-render-report.stub'
				: __DIR__ . '/stubs/exception-render.stub';
		}

		return $this->option('report')
			? __DIR__ . '/stubs/exception-report.stub'
			: __DIR__ . '/stubs/exception.stub';
	}

	/**
	 * Options to show on make command
	 *
	 * @return array
	 */
	public function getQuestions(): array
	{
		return [
			'--render' => 'Create the exception with an empty render method',
			'--report' => 'Create the exception with an empty report method',
		];
	}
}
