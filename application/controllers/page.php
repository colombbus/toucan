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

abstract class Page_Controller extends Toucan_Controller {

    // Template view name
    public $template = 'template';
    // Default to do auto-rendering
    public $auto_render = TRUE;
    protected $helpTopic = FALSE;


    public function __construct() {
        parent::__construct();

        if ($this->auto_render == TRUE)
        {
            // Render the template immediately after the controller method
            Event::add('system.post_controller', array($this, '_render'));
        }

        // Load the template
        $this->template = new View($this->template);
        $this->template->title = 'Toucan';

        // AUTHENTICATION
        if (isset($_POST['user'])&&isset($_POST['password'])) { // Login
            $this->authenticateUser();
        }

        // MENUS
        if ($this->authentication->logged_in()) { // User is logged in
            $this->template->user_menu = new View('user/logged');
            if ($this->testAdminAccess())
                $this->template->actions_menu = new View('menu/actions_admin');
            else
                $this->template->actions_menu = new View('menu/actions_private');
            if( $this->user->logo_id>0){
                $this->template->user_menu->photo_path = $this->user->logo->path;
            }
            $this->template->user_menu->username = $this->user->username;
            
            // deal with tabs
            Kohana::config_set('toucan.tabs_expanded', $this->user->getOption("tabs_expanded", false));   

        } else { // User is not logged in
            $this->template->user_menu = new View('user/login');
            $this->template->actions_menu = new View('menu/actions_public');
            if ($this->error->hasErrors('login')) {
                $this->template->user_menu->log_error = $this->error->getErrorMessage('login');
                $url = $this->error->getInfo('url');
                if (isset($url))
                    $this->template->user_menu->url = $url;
            }
            $this->template->user_menu->may_register = Kohana::config("toucan.registration_auto");
            
            // deal with tabs
            Kohana::config_set('toucan.tabs_expanded', $this->session->get("tabs_expanded", false));
        }

        $this->template->currentLanguage = language::getCurrentLanguage();
        $this->template->languages = language::getAvailableLanguages();
    }

    /**
     * Render the loaded template.
     */
    public function _render() {
        if ($this->auto_render == TRUE) {
            $controller = Router::$controller;
            if (!$this->helpTopic) {
                $helpTopic = Router::$method;
            } else {
                $helpTopic = $this->helpTopic;
            }
            $this->template->helpUrl = "axHelp/get/$controller/$helpTopic";
            // Render the template when the class is destroyed
            if ($this->hasMessage() && isset($this->template->content) && is_object($this->template->content))
                $this->template->content->message = $this->getMessage();
            if ($this->hasErrorMessage())
                $this->template->content->errorMessage = $this->getErrorMessage();
            $this->template->render(TRUE);
        }
    }

    private function authenticateUser() {
        $username = $this->input->post('user');
        $password = $this->input->post('password');
        $url = $this->input->post('url');
        $user = ORM::factory('user')->where('username', $username)->find();
        if ($user->loaded) {
            if ($user->isActive()) {
                $success =  $this->authentication->login($user,$password);
                if (!$success) {
                    $this->error->addError('login_wrong_password','login');
                    if (isset($url)) {
                        $this->error->addInfo('url',$url);
                    }
                } else {
                    // User successfully authenticated
                    $this->user = $this->authentication->get_user();
                    $this->computeAccess();
                    $this->setMessage(sprintf(Kohana::lang('user.logged_succesfully'),$this->user->firstname));
                    if (isset($url)) {
                        url::redirect($url);
                    }
                }
            } else {
                $this->error->addError('login_user_deactivated','login');
                if (isset($url)) {
                    $this->error->addInfo('url',$url);
                }
            }
        } else {
            $this->error->addError('login_user_unknown','login');
            if (isset($url)) {
                $this->error->addInfo('url',$url);
            }
        }
    }

    protected function displayError($message = false) {
        if ($message)
            $this->error->addError($message);
        url::redirect('error');
    }

    public function index () {
    }

}
?>