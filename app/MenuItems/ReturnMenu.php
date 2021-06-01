<?php

namespace App\MenuItems;

use PhpSchool\CliMenu\CliMenu;
use PhpSchool\CliMenu\MenuItem\SelectableItem;

class ReturnMenu extends SelectableItem
{
	/**
	 * Creates a new menu option.
	 *
	 * @param bool $showItemExtra
	 * @param bool $disabled
	 */
	public function __construct(callable $callback = null, $showItemExtra = false, $disabled = false)
	{
		parent::__construct('Press [r] for return', function (CliMenu $menu) use ($callback) {
			if ($parent = $menu->getParent()) {
				$menu->closeThis();
				$parent->open();
			}

			if (is_callable($callback)) {
				$callback($menu);
			}
		}, $showItemExtra, $disabled);
	}
}
