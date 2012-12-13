<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Kohana event subject. Uses the SPL observer pattern.
 *
 * $Id: Event_Subject.php,v 1.1 2010-08-11 17:46:54 benoit Exp $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
abstract class Event_Subject implements SplSubject {

	// Attached subject listeners
	protected $listeners = array();

	/**
	 * Attach an observer to the object.
	 *
	 * @chainable
	 * @param   object  Event_Observer
	 * @return  object
	 */
	public function attach(SplObserver $obj)
	{
		if ( ! ($obj instanceof Event_Observer))
			throw new Kohana_Exception('eventable.invalid_observer', get_class($obj), get_class($this));

		// Add a new listener
		$this->listeners[spl_object_hash($obj)] = $obj;

		return $this;
	}

	/**
	 * Detach an observer from the object.
	 *
	 * @chainable
	 * @param   object  Event_Observer
	 * @return  object
	 */
	public function detach(SplObserver $obj)
	{
		// Remove the listener
		unset($this->listeners[spl_object_hash($obj)]);

		return $this;
	}

    /**
	 * Notify all attached observers of a new message.
	 *
	 * @chainable
	 * @param   mixed   message string, object, or array
	 * @return  object
	 */
	public function notify($message = null)
	{
        if (isset($message)) {
            foreach ($this->listeners as $obj)
            {
                $obj->notify($message);
            }
        } else {
            foreach ($this->listeners as $obj)
            {
                $obj->notify();
            }
        }
		return $this;
	}

	/**
	 * Notify all attached observers of a new message.
	 *
	 * @chainable
	 * @param   mixed   message string, object, or array
	 * @return  object
	 */
	/*public function notify()
	{
		foreach ($this->listeners as $obj)
		{
			$obj->notify();
		}

		return $this;
	}*/

} // End Event Subject