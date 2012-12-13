<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Toucan is a web application to perform evaluation and follow-up of
 * activities.
 * Copyright (C) 2010 Colombbus (http://www.colombbus.org)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class AxHelp_Controller extends Controller {

    public $auto_render = TRUE;
    protected $view = null;

    public function __construct() {
        parent::__construct();
        if ($this->auto_render) {
            // Render the template immediately after the controller method
            Event::add('system.post_controller', array($this, '_render'));
        }

    }
    public function get($controller, $method) {
        $this->view=new View("help");
        $this->view->content = Kohana::lang("help_$controller.$method");
    }

    /**
     * Render the loaded template.
     */
    public function _render() {
        if ($this->auto_render) {
            // Render the template when the class is destroyed
            $this->view->render(TRUE);
        }
    }
}
?>