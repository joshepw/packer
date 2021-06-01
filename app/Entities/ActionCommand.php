<?php

namespace App\Entities;

class ActionCommand {
	/**
	 * Command name
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Command description
	 *
	 * @var string
	 */
	public $description;

	/**
	 * Command key
	 *
	 * @var string
	 */
	public $command;

	/**
	 * Command type
	 *
	 * @var string
	 */
	public $type;

	/**
	 * Command arguments
	 *
	 * @var array
	 */
	public $arguments = [];

	/**
	 * Command interactive questions
	 *
	 * @var array
	 */
	public $questions = [];
}