<?php

namespace App\Traits;

use App\MenuItems\ExitMenu;
use InvalidArgumentException;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

use PhpSchool\CliMenu\CliMenu;
use PhpSchool\CliMenu\MenuStyle;
use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use PhpSchool\CliMenu\Builder\SplitItemBuilder;

use PhpSchool\CliMenu\MenuItem\RadioItem;
use PhpSchool\CliMenu\MenuItem\AsciiArtItem;
use PhpSchool\CliMenu\MenuItem\CheckboxItem;
use PhpSchool\CliMenu\MenuItem\MenuItemInterface;
use PhpSchool\CliMenu\MenuItem\SelectableItem;
use PhpSchool\CliMenu\Style\RadioStyle;
use PhpSchool\CliMenu\Style\SelectableStyle;

trait WithCliMenu
{
	/**
	 * Width of menu
	 *
	 * @var integer
	 */
	private static $width = 100;

	/**
	 * Main interactive menu
	 *
	 * @var CliMenuBuilder
	 */
	protected $menu;

	/**
	 * Promp Style
	 *
	 * @var MenuStyle
	 */
	protected $promp_style;

	/**
	 * Result value of menu
	 *
	 * @var MenuItemInterface
	 */
	protected $selected_item;

	/**
	 * Result of submenu options
	 *
	 * @var array
	 */
	protected $selected_options = [];

	/**
	 * Boot the menu helper
	 *
	 * @param string $title
	 * @return void
	 */
	protected function initMenu($title = null)
	{
		$this->promp_style = (new MenuStyle())
			->setBg('green')
			->setFg('magenta');

		$this->menu = $this->makeMenu($title);
	}

	/**
	 * make a new Menu
	 *
	 * @param string $title
	 * @return CliMenuBuilder
	 */
	protected function makeMenu($title = null)
	{
		$builder = (new CliMenuBuilder())
			->setWidth(self::$width)
			->enableAutoShortcuts()
			->disableDefaultItems()
			->setTitleSeparator('-')
			->setMarginAuto();

		if (empty($title)) {
			$builder->addAsciiArt(config('logo.art'), AsciiArtItem::POSITION_LEFT)
				->addStaticItem($this->getVersionLine($builder));
		} else {
			$builder->setTitle($title);
		}

		return $builder;
	}

	/**
	 * Open the menu
	 *
	 * @param CliMenuBuilder $builder
	 * @return MenuItemInterface
	 */
	protected function open(CliMenuBuilder $builder = null)
	{
		($builder ?? $this->menu)->build()->open();

		return $this->selected_item;
	}

	/**
	 * Add footer to menu/submenu
	 *
	 * @param CliMenuBuilder $builder
	 * @param array $items
	 * @return static
	 */
	protected function addFooter(array $items = [], CliMenuBuilder $builder = null)
	{
		if (!empty($items)) {
			($builder ?? $this->menu)
				->addLineBreak('-')
				->addSplitItem(function (SplitItemBuilder $split) use ($items) {
					foreach ($items as $item) {
						if ($item instanceof MenuItemInterface) {
							$split->addMenuItem($item);
						}
					}
				});
		} else {
			($builder ?? $this->menu)
				->addLineBreak('-')
				->addMenuItem(new ExitMenu);
		}

		return $this;
	}

	/**
	 * Add footer to submenu
	 *
	 * @param CliMenuBuilder $builder
	 * @return static
	 */
	protected function addFooterToSubmenu(CliMenuBuilder $builder = null)
	{
		($builder ?? $this->menu)
			->addLineBreak('-')
			->addSplitItem(function (SplitItemBuilder $split) {
				$split->addItem('Press [c] for continue', function (CliMenu $menu) {
					$menu->close();
				});

				$split->addItem('Press [x] for cancel', function (CliMenu $menu) {
					$this->selected_item = null;
					$menu->close();
				});
			});

		return $this;
	}

	/**
	 * Set an option to result
	 *
	 * @param string|array $key
	 * @param mixed $value
	 * @return void
	 */
	protected function setMenuOption($key, $value)
	{
		if (is_array($key)) {
			foreach ($key as $subkey) {
				$this->setMenuOption($subkey, $value);
			}
		} else {
			if (empty($value)) {
				if (array_key_exists($key, $this->selected_options)) {
					unset($this->selected_options[$key]);
				}
			} else {
				$this->selected_options[$key] = $value;
			}
		}
	}

	/**
	 * Verify is have menu option
	 *
	 * @param string $key
	 * @return boolean
	 */
	protected function hasMenuOption(string $key)
	{
		return array_key_exists($key, $this->selected_options);
	}

	/**
	 * Add option to menu or builder
	 * 
	 * @param string $key 
	 * @param mixed $content 
	 * @param CliMenuBuilder|SplitItemBuilder|null $builder 
	 * @param callable|null $callback 
	 * @return void 
	 * 
	 * @throws InvalidArgumentException 
	 */
	protected function addOptionToMenu(string $key, $content, $builder = null, callable $callback = null)
	{
		$on_action = function(CliMenu $menu) use ($key, $content, $callback) {
			$item = $menu->getSelectedItem();

			if (Str::contains($key, '=')) {
				$result = $menu->askText($this->promp_style)
					->setPromptText($content)
					->ask()
					->fetch();
			} elseif ($item instanceof CheckboxItem) {
				$result = $item->getChecked();
			} else {
				$result = $item->getText();
			}

			if (is_callable($callback)) {
				$callback($menu);
			} else {
				$this->setMenuOption($key, $result);
			}
		};

		if (is_array($content)) {
			$this->generateChoiceForOption($content, $builder);
		} else {
			if (is_callable($callback)) {
				$item = new SelectableItem($content, $on_action);
				$item->setStyle((new SelectableStyle)
					->setSelectedMarker('[*] ')
					->setUnselectedMarker('[ ] '));
			} else {
				$item = new CheckboxItem($content, $on_action);
			}

			if (Str::contains($content, '(?)') || Str::contains($content, '(*)')) {
				$item->setChecked();
				$this->setMenuOption($key, true);
			}

			($builder ?? $this->menu)->addMenuItem($item);
		}
	}

	/**
	 * Generate a group of radio items group
	 *
	 * @param array $items
	 * @param CliMenuBuilder $builder
	 * @return void
	 */
	private function generateChoiceForOption(array $items, CliMenuBuilder $builder = null)
	{
		foreach ($items as $key => $text) {
			$item = new RadioItem($text, function() use ($key, $items) {
				$this->setMenuOption(array_keys($items), null);

				if ($key !== 'default') {
					$this->setMenuOption($key, true);
				}
			});

			$item->setStyle((new RadioStyle)
				->setCheckedMarker('[*] ')
				->setUncheckedMarker('[ ] '));

			if ($key == 'default') {
				$item->setChecked();
			}

			($builder ?? $this->menu)->addMenuItem($item);
		}
	}

	/**
	 * Ask for option and save it
	 *
	 * @param string $text
	 * @param CliMenu $menu
	 * @param array|string $rules
	 * @return string
	 */
	private function askForOption(string $key, string $text, CliMenu $menu, $rules = [])
	{
		$question = $menu->askText($this->promp_style);
		$question->setValidator(function ($value) use ($key, $rules, $question) {
			$validator = Validator::make([$key ?? 'input' => $value], [$key ?? 'input' => $rules]);

			if ($validator->fails()) {
				$question->setValidationFailedText($validator->errors()->first($key ?? 'input'));
			}

			return !$validator->fails();
		});
			
		$answer = $question->setPromptText($text)
			->ask()
			->fetch();

		$this->setMenuOption($key, $answer);

		return $answer;
	}

	/**
	 * Get application version
	 *
	 * @param CliMenuBuilder $builder
	 * @return string
	 */
	private function getVersionLine(CliMenuBuilder $builder = null)
	{
		$content_width = ($builder ?? $this->menu)->getStyle()->getContentWidth();
		$padding = ($builder ?? $this->menu)->getStyle()->getPaddingLeftRight();
		$min = $content_width + ($padding * 2);
		$padding_offset = $min % 2 ? $padding + ($padding / 2) : $padding;
		$version = config('app.version');
		$version_offset = config('logo.width');
		$left = str_pad(" $version ", $version_offset, '-', STR_PAD_LEFT);

		return str_pad($left, $content_width + ($min < self::$width ? $padding_offset : 0), '-');
	}
}
