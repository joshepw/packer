<?php

namespace App\MenuItems;

use PhpSchool\CliMenu\CliMenu;
use PhpSchool\CliMenu\MenuItem\SelectableItem;

class HelpMenu extends SelectableItem
{
	/**
	 * Creates a new menu option.
	 *
	 * @param bool $showItemExtra
	 * @param bool $disabled
	 */
	public function __construct(callable $callback = null, $showItemExtra = false, $disabled = false)
	{
		parent::__construct('(?) Press [h] for help', function (CliMenu $menu) use ($callback) {
			$menu->close();

			if (is_callable($callback)) {
				$callback();
			}
		}, $showItemExtra, $disabled);
	}
}
