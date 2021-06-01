<?php

namespace App\MenuItems;

use PhpSchool\CliMenu\CliMenu;
use PhpSchool\CliMenu\MenuItem\SelectableItem;

class ContinueMenu extends SelectableItem
{
	/**
	 * Creates a new menu option.
	 *
	 * @param bool $showItemExtra
	 * @param bool $disabled
	 */
	public function __construct(callable $callback = null, $closeBefore = true, $showItemExtra = false, $disabled = false)
	{
		parent::__construct('Press [c] for continue', function (CliMenu $menu) use ($callback, $closeBefore) {
			if ($closeBefore) {
				$menu->close();
			}

			if (is_callable($callback)) {
				$callback($menu);
			}
		}, $showItemExtra, $disabled);
	}
}
