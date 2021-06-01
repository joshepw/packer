<?php

namespace App\Commands;

use App\Traits\WithCliMenu;
use App\Traits\InteractsWithFoundationCommands;

use App\MenuItems\ExitMenu;
use App\MenuItems\HelpMenu;
use App\MenuItems\ReturnMenu;
use App\MenuItems\ContinueMenu;
use App\Entities\ActionCommand;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use LaravelZero\Framework\Commands\Command;

use PhpSchool\CliMenu\CliMenu;
use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use PhpSchool\CliMenu\Builder\SplitItemBuilder;

class Make extends Command
{
	use WithCliMenu;
	use InteractsWithFoundationCommands;

	const COLUMNS = 4;

	/**
	 * The signature of the command.
	 *
	 * @var string
	 */
	protected $signature = 'make';

	/**
	 * The description of the command.
	 * The description of the command.
	 *
	 * @var string
	 */
	protected $description = 'Interactive make package element';

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

		$this->initMenu();

		collect(config('make'))->each(function ($section, $key) {
			$text = Arr::get($section, 'description');
			$items = Arr::get($section, 'commands');

			$this->menu->addSubMenu($text, function (CliMenuBuilder $builder) use ($key, $items) {
				$builder->disableDefaultItems()->setTitle(config("make.$key.description"));
	
				collect($items)->chunk(self::COLUMNS)->each(function (Collection $chunk) use ($builder) {
					$builder->addSplitItem(function (SplitItemBuilder $split) use ($chunk) {	
						$chunk->each(function ($text, $command) use ($split) {
							$this->addOptionToMenu($command, $text, $split, function (CliMenu $menu) use ($command) {
								$this->onSelectOptionMenu($menu, $command);
							});
						});
	
						for ($i = 0; $i < self::COLUMNS - $chunk->count(); $i++) {
							$split->addStaticItem('');
						}
					});
				});
	
				$this->addFooter([
					new ReturnMenu,
					new ExitMenu,
				], $builder);
			});
		});

		if ($this->searchLaravelPackagePath()) {
			$this->menu->addItem('Update the package', function (CliMenu $menu) {
				$this->onSelectOptionMenu($menu, 'package:new');
			});

			$this->menu->addItem('Attach the package', function (CliMenu $menu) {
				$this->setMenuOption('path', $this->searchLaravelPackagePath());
				$this->onSelectOptionMenu($menu, 'package:install');
			});

			$this->menu->addItem('Remove the package', function (CliMenu $menu) {
				$this->onSelectOptionMenu($menu, 'package:remove');
			});
		} elseif ($this->searchLaravelProjectPath()) {
			$this->menu->addItem('Create a new package', function (CliMenu $menu) {
				$this->onSelectOptionMenu($menu, 'package:new');
			});

			$this->menu->addItem('Install a package', function (CliMenu $menu) {
				$this->onSelectOptionMenu($menu, 'package:install');
			});

			$this->menu->addItem('Remove a package', function (CliMenu $menu) {
				$this->onSelectOptionMenu($menu, 'package:remove');
			});
		}

		$this->addFooter([
			new ExitMenu(),
			new HelpMenu(function() {
				$this->call('list');
			})
		]);

		$this->open();
	}

	private function onSelectOptionMenu(CliMenu $menu, $command)
	{
		$this->selected_item = $menu->getSelectedItem();
		$action = $this->getSelectedCommand($command);

		if (!empty($action->arguments)) {
			foreach ($action->arguments as $key => $question) {
				$replace = [
					'class' => $menu->getSelectedItem()->getText().' class',
					'file' => $menu->getSelectedItem()->getText().' file',
				];

				$question = str_replace(array_keys($replace), array_values($replace), $question);
				$this->askForOption($key, $question, $menu, config('validation.rules.'.$key, []));
			}
		}
		
		$menu->close();

		if (empty($action->questions)) {
			return $this->call($command, $this->selected_options);
		}

		$child_menu = $this->makeMenu($this->getOptionsMenuTitle($action));

		foreach ($action->questions as $key => $question) {
			$this->addOptionToMenu($key, $question, $child_menu);
			
			if (is_array($question) && count($action->questions) > 1) {
				$child_menu->addLineBreak('·');
			}
		}

		$this->addFooter([
			new ContinueMenu(function () use ($command) {
				$this->call($command, $this->selected_options);
			}),
			new ReturnMenu(function (CliMenu $submenu) use ($menu) {
				$this->selected_item = null;
				$this->selected_options = [];

				$submenu->closeThis();
				$menu->open();
			}),
		], $child_menu);

		$this->open($child_menu);
	}

	private function getOptionsMenuTitle(ActionCommand $action = null)
	{
		$title = 'Select options for create ';

		if (!is_null($action) && !empty($action->type)) {
			$name = $this->selected_options['name'] ?? '';

			$replace = [
				'KebabName' => Str::kebab($name),
				'StudlyName' => Str::studly($name),
				'SnakeName' => Str::snake($name),
			];

			$title .= str_replace(array_keys($replace), array_values($replace), $action->type);
		} elseif (!empty($this->selected_item)) {
			$title .= $this->selected_item->getText();
		}

		return $title;
	}

	/**
	 * Return the selected command
	 *
	 * @return ActionCommand
	 */
	private function getSelectedCommand($name)
	{
		$action = new ActionCommand();

		$commands = collect(Artisan::all());
		$command = $commands->filter(function ($command) use ($name) {
			return $command->getName() === $name;
		})->first();

		if (!empty($command)) {
			$action->name = str_replace('make:', '', $command->getName());
			$action->description = $command->getDescription();
			$action->command = $command->getName();
			$action->questions = method_exists($command, 'getQuestions') ? optional($command)->getQuestions() : [];
			$action->type = method_exists($command, 'getMakerType') ? optional($command)->getMakerType() : null;

			foreach ($command->getArguments() as $argument) {
				$action->arguments[$argument[0]] = $argument[2];
			}
		}

		return $action;
	}
}
