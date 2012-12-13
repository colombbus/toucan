<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Database expression class to allow for explicit joins and where expressions.
 *
 * $Id: Database_Expression.php,v 1.1 2010-03-25 17:59:03 benoit Exp $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2009 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Database_Expression_Core {

	protected $expression;

	public function __construct($expression)
	{
		$this->expression = $expression;
	}

	public function __toString()
	{
		return (string) $this->expression;
	}

} // End Database Expr Class