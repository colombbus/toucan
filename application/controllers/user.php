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

class User_Controller extends DataPage_Controller {

    protected $dataName = "user";

    protected function controlAccess($action) {
        switch ($action) {
            case 'SHOW_ALL' :
            case 'SHOW' :
                // Access only for registered users
                $this->ensureAccess();
                break;
            case 'PASSWORD' :
            case 'EDIT' :
                $this->ensureAccess(access::OWNER);
                break;
            case 'DELETE' :
            case 'ACTIVATE' :
                $this->ensureAccess(access::ADMIN);
                if ($this->data->isProtected()) {
                    // User is protected
                    $this->displayError('user_protected');
                }
                break;
            case 'CREATE' :
                if ($this->testAccess()) {
                    if (Kohana::config("toucan.new_user_admin_only"))
                        $this->ensureAccess(access::ADMIN);
                } else {
                    if (!Kohana::config("toucan.registration_auto"))
                        $this->displayError('restricted_access');
                }
                break;
            case 'SET_GROUPS' :
                $this->ensureAccess(access::ADMIN);
                break;
            case 'SEND_PASSWORD' :
            case 'VALIDATION' :
                // Access to anybody
                break;
            default : // in case other cases not covered
                $this->ensureAccess(access::ADMIN);
                break;
        }
    }


    public function index () {
        $this->profile();
    }

    public function profile($id=null) {
        $this->show($id);
    }

    public function show($id=null) {
        if ((!isset($id))&&(isset($this->user))) {
            $id = $this->user->id;
        }
        parent::show($id);
        if (isset($this->data)&&$this->data->isProtected()) {
            $this->template->content->info = Kohana::lang("user.protected");
        }
    }

     public function showAll(){
        $fields = array('firstname'=>'firstname', 'name'=>'name', 'username'=>'username');

        parent::showAll($fields);

        $filter = ListFilter::instance();

        // Deal with search
        $search = array();
        $search[0] = array('text'=>'user.firstname', 'name'=>'firstname');
        $search[1] = array('text'=>'user.name','name'=>'name');
        $search[2] = array('text'=>'user.username','name'=>'username');
        $this->template->content->showSearch = $filter->fillSearchFields($search);
        $this->template->content->search = $search;

        // Set default sorting to field "name"
        $filter->setDefaultSorting("name");
        $this->template->content->sortingName = $filter->getSortingName();
        $this->template->content->sortingOrder = $filter->getSortingOrderInt();
    }

    public function edit($id=null) {
        if ((!isset($id))&&(isset($this->user))) {
            $id = $this->user->id;
        }
        parent::edit($id);
    }

    public function activate($id,$value=true, $url=false) {
        $this->loadData($id);

        $this->controlAccess('ACTIVATE');

        $this->data->setActive($value,true);

        if ($value)
           $this->setMessage(Kohana::lang("user.message_activated"));
        else
           $this->setMessage(Kohana::lang("user.message_deactivated"));

        if (!$url)
            url::redirect('user/profile/'.$id);
        else
            url::redirect(urldecode($url));
    }

    public function register($url=false) {
        $this->create($url);
    }

    public function create($url=false) {
        if (!$url) {
            if ($this->testAccess())
                $url = 'user/showAll';
            else
                $url = 'home';
        }
        $parameters = array('url_next'=>$url);

        if (!$this->testAccess()) {
            // User registration : we test if we have to mark the user as pending
            if (Kohana::config("toucan.registration_email_confirmation")) {
                if (($post = $this->input->post())&&isset($post["form_create_user"])) {
                    $_POST["pending_url"] = url::site('user/validation')."/%s/%s";
                } else {
                    $parameters['url_next'] = 'user/validation/';
                    $parameters['url_next_with_id'] = 1;
                }
            }
        }
        parent::create($parameters);
    }

    public function validation($id = null, $chain = null) {
        $this->loadData($id);
        $this->controlAccess('VALIDATION');
        $this->template->content = new View('data/text');
        if (!isset($chain)) {
            $this->template->content->text = Kohana::lang('user.validation_message_sent');
        } else {
            if ($this->data->checkValidation($chain)) {
                $this->template->content->text = Kohana::lang('user.validation_ok');
            } else {
                $this->template->content->text = Kohana::lang('user.validation_nok');
            }
        }
        $this->setPageInfo('VALIDATION');
    }

    public function changePassword($id=null) {
        if ((!isset($id))&&(isset($this->user))) {
            $id = $this->user->id;
        }
        $this->loadData($id);
        $user = $this->data;

        $this->controlAccess('PASSWORD');

        $formErrors= array();
        if (($post = $this->input->post())&&isset($post['form_user_password'])) { // form submitted
            if ($user->validatePassword($post,true)) { // password could be validated and saved
               $this->setMessage(Kohana::lang("user.message_edited"));
                if ($id==null) {
                    url::redirect('user/profile');
                } else {
                    url::redirect('user/profile/'.$id);
                }
                return;
            } else {
                $formErrors = $user->getErrors("form_errors");
            }
        }

        $this->template->content=new View('data/edit');
        $this->template->content->formId = 'form_user_password';
        $this->template->content->data = $user->getPasswordEditableData($this->testAdminAccess());
        $this->setPageInfo('PASSWORD');
        $this->template->content->errors = $formErrors;
    }


    public function setGroups($id) {
        $this->controlAccess('SET_GROUPS');

        $formErrors= array();
        $error = false;

        $this->loadData($id);
        $user = $this->data;

        if (($post = $this->input->post())&&isset($post['form_user_set_groups'])) { // form submitted
            if ($user->validateGroupSelection($post,true)) { // user could be validated and saved
                $this->setMessage(Kohana::lang('user.message_edited'));
                url::redirect('user/profile/'.$user->id);
            } else {
                $formErrors = $user->getErrors("form_errors");
                $error = true;
            }
        }

        $this->template->content=new View('data/edit');
        $this->template->content->formId = 'form_user_set_groups';
        $data = $user->getGroupsEditableData($this->user);

        // Set previous values in case of an error
        if (($error)&&(isset($post['group']))&&(is_array($post['group']))) {
            $previous = $post['group'];
            for($i=0;$i<count($data);$i++) {
                if (in_array($data[$i]['value'], $previous))
                    $data[$i]['checked'] = 1;
                else
                    $data[$i]['checked'] = 0;
            }
        }
        $this->setPageInfo('SET_GROUPS');
        $this->template->content->data = $data;
        $this->template->content->errors = $formErrors;
    }

    public function sendPassword() {
        $this->controlAccess('SEND_PASSWORD');

        $user = ORM::factory('user');

        $formErrors= array();
        if (($post = $this->input->post())&&isset($post['form_user_send_password'])) { // form submitted
            if ($user->retrieveAndSendPassword($post)) { // password could be retrieved and sent
               $this->setMessage(Kohana::lang('user.password_sent'));
                url::redirect('home');
                return;
            } else {
                $formErrors = $user->getErrors("form_errors");
                $user->setEmailValue($post);
            }
        }

        $this->template->content=new View('data/edit');
        $this->template->content->formId = 'form_user_send_password';
        $this->template->content->data = $user->getPasswordRecoveryData();
        $this->template->content->errors = $formErrors;
        $this->setPageInfo('SEND_PASSWORD');
    }

    protected function setActions($action) {
        $user = $this->data;
        $actions = array();
        $actions_back = array();
        switch ($action) {
            case 'SHOW' :
                if ($this->testAccess(access::OWNER)) {
                    // Edit action
                    $actions[] = array('type' => 'button','text' => 'button.edit','url' => 'user/edit/'.$user->id);
                    // Change password action
                    $actions[] = array('type' => 'button','text' => 'button.password','url' => 'user/changePassword/'.$user->id);
                }
                if ($this->testAdminAccess()) {
                    //$actions[] = array('type' => 'button','text' => 'user.set_groups','url' => 'user/setGroups/'.$user->id);
                    //  Activate/Deactivate and Delete actions
                    if (!$user->isProtected()) {
                        if ($user->isActive()) {
                            $actions[] = array('type' => 'button_confirm','text' => 'button.deactivate','confirm' => 'user.deactivate_confirm','url' => 'user/activate/'.$user->id.'/0');
                        } else {
                            $actions[] = array('type' => 'button_confirm','text' => 'button.activate','confirm' => 'user.activate_confirm','url' => 'user/activate/'.$user->id.'/1');
                        }
                        $actions[] = array('type' => 'button_confirm','text' => 'button.delete','confirm' => 'user.delete_text','url' => 'user/delete/'.$user->id);
                    }
                }
                break;
            case 'SHOW_ALL' :
                $actions_back[] = array('type' => 'button','text' => 'user.groups','url' => 'group/showAll');
                if (!Kohana::config("toucan.new_user_admin_only")||$this->testAccess(access::ADMIN)) {
                    $actions[] = array('type' => 'button','text' => 'user.create','url' => 'user/create');
                }
                break;
            case 'EDIT' :
            case 'PASSWORD' :
                $actions_back[] = array('type' => 'button','text' => 'button.cancel','url' => 'user/profile/'.$user->id);
                $actions[] = array('type' => 'submit','text' => 'button.save');
                break;
            case 'SET_GROUPS' :
                $actions[] = array('type' => 'submit','text' => 'button.save');
                break;
            case 'CREATE' :
                $actions_back[] = array('type' => 'cancel','text' => 'button.cancel');
                $actions[] = array('type' => 'submit','text' => 'button.save');
                break;
            case 'SEND_PASSWORD' :
                $actions_back[] = array('type' => 'cancel','text' => 'button.cancel');
                $actions[] = array('type' => 'submit','text' => 'button.send');
                break;
            case 'VALIDATION' :
                $actions_back[] = array('type' => 'button','text' => 'button.back', 'url'=>'home');
                break;
        }
        $tabs = array();
        switch ($action) {
            case 'SHOW' :
            case 'EDIT' :
            case 'PASSWORD' :
                if ($this->testAdminAccess()) {
                    $tabs[] = array('text'=>'user.info', 'link' => 'user/show/'.$user->id, 'current' => 1,'image'=>Kohana::config('toucan.images_directory')."/user.png");
                    $tabs[] = array('text'=>'user.set_groups', 'link' => 'user/setGroups/'.$user->id,'image'=>Kohana::config('toucan.images_directory')."/group.png");
                }
                break;
            case 'SET_GROUPS' :
                $tabs[] = array('text'=>'user.info', 'link' => 'user/show/'.$user->id,'image'=>Kohana::config('toucan.images_directory')."/user.png");
                $tabs[] = array('text'=>'user.set_groups', 'link' => 'user/setGroups/'.$user->id, 'current' => 1, 'image'=>Kohana::config('toucan.images_directory')."/group.png");
                break;
        }
        $this->template->content->actions = $actions;
        $this->template->content->actions_back = $actions_back;
        $this->template->content->tabs = $tabs;
    }

    protected function setHeaders($action) {
        $headers = array();
        switch ($action) {
            case 'SHOW_ALL' :
                $headers[0] = array('text'=>'user.firstname_header', 'name'=>'firstname');
                $headers[1] = array('text'=>'user.name_header','name'=>'name');
                $headers[2] = array('text'=>'user.username_header','name'=>'username');
                break;
        }
        $this->template->content->headers = $headers;
    }

    protected function setPath($action) {
        $user = $this->data;
        $path = array();
        $this->template->content->path = $path;
        $this->template->content->pathType = "path_user";
    }

    protected function setDescription($action) {
        $user = $this->data;
        parent::setDescription($action);
        if (($action != 'SHOW_ALL')&&($action != 'CREATE')&&($action != 'SEND_PASSWORD')&&($action != 'VALIDATION')) {
            $this->template->content->title = sprintf(Kohana::lang('user.main_title', $user->fullName));
            if ($user->logo_id >0)
                $this->template->content->title_logo = $user->logo->path;
        }
        if (($action == 'CREATE') && !$this->testAccess() ) {
            // Self-registration
            $this->template->content->title = Kohana::lang('user.registration_title');
            $this->template->content->description = Kohana::lang('user.registration_description');
        }

    }
}
?>