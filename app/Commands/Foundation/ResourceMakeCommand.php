<?php

namespace App\Commands\Foundation;

use App\Traits\ClassInputName;
use Illuminate\Foundation\Console\ResourceMakeCommand as ResourceMake;
use App\Traits\InteractsWithFoundationCommands;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class ResourceMakeCommand extends ResourceMake
{
	use InteractsWithFoundationCommands;
	use ClassInputName;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'make:resource';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new Resource class in your package';

	/**
	 * Get the stub file for the generator.
	 *
	 * @return string
	 */
	protected function getStub()
	{
		return $this->input->getOption('bag')
			? __DIR__ . '/stubs/resource_responsebag.stub'
			: __DIR__ . '/stubs/resource.stub';
	}

	// $this->collection()

	/**
	 * Determine if the command is generating a resource collection.
	 *
	 * @return bool
	 */
	protected function collection()
	{
		return $this->option('collection') || Str::endsWith($this->getNameInput(), 'Collection');
	}

	/**
	 * Build the class with the given name.
	 *
	 * @param  string  $name
	 * @return string
	 */
	protected function buildClass($name)
	{
		return str_replace('DummyResource', $this->collection() ? 'ResourceCollection' : 'JsonResource', parent::buildClass($name));
	}

	/**
	 * Options to show on make command
	 *
	 * @return array
	 */
	public function getQuestions(): array
	{
		return [
			'type' => [
				'default' => 'Create a JSON resource',
				'--collection' => 'Create a resource collection',
			],
			'--bag' => 'Add format ResponseBag support',
		];
	}

	/**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
			['bag', 'b', InputOption::VALUE_NONE, 'Add format ResponseBag support'],
            ['collection', 'c', InputOption::VALUE_NONE, 'Create a resource collection'],
        ];
    }
}
