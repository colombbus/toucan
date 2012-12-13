<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Cache driver interface.
 *
 * $Id: Cache.php,v 1.1 2010-03-25 17:59:07 benoit Exp $
 *
 * @package    Cache
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
interface Cache_Driver {

	/**
	 * Set a cache item.
	 */
	public function set($id, $data, array $tags = NULL, $lifetime);

	/**
	 * Find all of the cache ids for a given tag.
	 */
	public function find($tag);

	/**
	 * Get a cache item.
	 * Return NULL if the cache item is not found.
	 */
	public function get($id);

	/**
	 * Delete cache items by id or tag.
	 */
	public function delete($id, $tag = FALSE);

	/**
	 * Deletes all expired cache items.
	 */
	public function delete_expired();

} // End Cache Driver