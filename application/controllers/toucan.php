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

abstract class Toucan_Controller extends Controller {

    protected $user = NULL;
    protected $authentication;
    protected $session;
    protected $error;
    protected $config;
    protected $lang;
    protected $dataName = null;
    protected $data = null;
    protected $access ;
    protected $xssFiltering = true;

    public function __construct() {
        // Enable or not XSS filtering before creating Input instance
        Kohana::config_set('core.global_xss_filtering', $this->xssFiltering);

        parent::__construct();

        // CONFIG
        $this->config=Kohana::config('config');

        // SESSION
        $this->session = Session::instance();

        // ERROR
        $this->error = Error::instance();

        // AUTHENTICATION
        $this->authentication = new Auth();
        if ($this->authentication->logged_in()) { // User is logged: get user data
            $this->user = $this->authentication->get_user();
        }

        $this->computeAccess();
    }

    abstract protected function displayError($message = false);

    protected function testAdminAccess() {
        if (isset($this->user)) {
            return $this->authentication->logged_in('admin');
        }
        return false;
    }

    protected function testAccess($role = null, $data = null) {
        if (isset($role)) {
            switch ($role) {
                case access::ADMIN :
                    return $this->testAdminAccess();
                    break;
                case access::ANYBODY :
                    return true;
                    break;
                default :
                    if (isset($data)) {
                        return DataAccess::testAccess($data, $this->user, $role);
                    } else {
                        return ($this->access >= $role);
                    }
                    break;
            }
        } else {
            return $this->authentication->logged_in(null);
        }
        return false;
    }

    protected function ensureAccess($role = null, $data = null) {
        if (!$this->testAccess($role, $data)) {
            if (!$this->testAccess()) {
                // user not logged: display login screen
                $this->error->addError('authentication_required','main');
                $this->error->addInfo('display_login',true);
            } else {
                $this->error->addError('restricted_access','main');
            }
            $this->error->addInfo('url',$this->uri->string());
            $this->displayError();
            return false;
        }
        return true;
    }

    public function index () {
    }

    protected function setMessage($message) {
        $this->session->set_flash('message',$message);
    }

    protected function setErrorMessage($message) {
        $this->session->set_flash('error_message',$message);
    }

    protected function hasMessage() {
        return ($this->session->get('message',false)!==false);
    }

    protected function hasErrorMessage() {
        return ($this->session->get('error_message',false)!==false);
    }

    protected function getMessage() {
        return $this->session->get_once('message',false);
    }

    protected function getErrorMessage() {
        return $this->session->get_once('error_message',false);
    }
    
    protected function keepMessage() {
        $this->session->keep_flash('message');
    }

    protected function clearMessage() {
        $this->session->delete('message');
    }

    protected function clearErrorMessage() {
        $this->session->delete('error_message');
    }
    
    protected function loadData($id) {
        $this->data = ORM::factory($this->dataName,$id);
        if (!$this->data->loaded) { // Data not found
            if (isset($this->dataName))
                $this->displayError($this->dataName.'_unknown');
            else
                $this->displayError('data_unknown');
        } else {
            $this->computeAccess();
        }
    }

    protected function userMayView() {
        return $this->data->isViewableBy($this->user);
    }

    protected function userMayEdit() {
        return $this->data->isEditableBy($this->user);
    }

    protected function userMayContribute() {
        return $this->data->mayBeContributedBy($this->user);
    }


    protected function userIsOwner() {
        if (isset($this->user))
            return $this->user->owns($this->data);
        else
            return false;
    }

    protected function computeAccess() {
        $this->access = DataAccess::computeAccess($this->data,$this->user);
        if ($this->access == access::NO_ACCESS) {
            if ($this->authentication->logged_in(null))
                $this->access = access::REGISTERED;
            else
                $this->access = access::ANYBODY;
        }
    }
}
?>