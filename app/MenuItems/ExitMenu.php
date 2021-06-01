<?php

namespace App\MenuItems;

use PhpSchool\CliMenu\CliMenu;
use PhpSchool\CliMenu\MenuItem\SelectableItem;

class ExitMenu extends SelectableItem
{
	/**
	 * Creates a new menu option.
	 *
	 * @param bool $showItemExtra
	 * @param bool $disabled
	 */
	public function __construct(callable $callback = null, $showItemExtra = false, $disabled = false)
	{
		parent::__construct('Press [x] for exit', $callback ?? function (CliMenu $menu) {
			$menu->close();
		}, $showItemExtra, $disabled);
	}
}
