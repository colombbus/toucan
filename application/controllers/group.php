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

class Group_Controller extends DataPage_Controller {

    protected $dataName = "group";

    protected function controlAccess($action) {
        switch ($action) {
            case 'SHOW_ALL' :
            case 'SHOW' :
            case 'CREATE' :
                // Access only for registered users
                $this->ensureAccess();
                break;
            case 'EDIT' :
            case 'ACTIVATE' :
            case 'MEMBERS' :
            case 'SET_MEMBERS' :
                // Access only for registered users
                $this->ensureAccess(access::OWNER);
                if ($this->data->isProtected()) {
                    // Group is protected
                    $this->displayError('group_protected');
                }
                break;
            case 'DELETE' :
            case 'OWNER' :
                $this->ensureAccess(access::ADMIN);
                if ($this->data->isProtected()) {
                    // Group is protected
                    $this->displayError('group_protected');
                }
                break;
            default : // in case other cases not covered
                $this->ensureAccess(access::ADMIN);
                break;
        }
    }

    public function showAll() {
        parent::showAll();

        // Set default sorting to field "name"
        $filter = ListFilter::instance();
        $filter->setDefaultSorting("name");
        $this->template->content->sortingName = $filter->getSortingName();
        $this->template->content->sortingOrder = $filter->getSortingOrderInt();

        // SEARCH
        $search = array();
        $search[0] = array('name'=>'name','text'=>'group.name');
        $this->template->content->showSearch = $filter->fillSearchFields($search);
        $this->template->content->search = $search;
    }

    public function show($id=null) {
        parent::show($id);
        if ($this->data->isProtected()) {
            $this->template->content->info = Kohana::lang("group.protected");
        }
    }

    public function activate($id,$value=true, $url=false) {
        $this->loadData($id);

        $this->controlAccess('ACTIVATE');

        $this->data->setActive($value,true);

        if ($value)
           $this->setMessage(Kohana::lang("group.message_activated"));
        else
           $this->setMessage(Kohana::lang("group.message_deactivated"));

        if (!$url)
            url::redirect('group/show/'.$id);
        else
            url::redirect(urldecode($url));
    }

    public function members($id) {
        $this->loadData($id);

        $this->controlAccess('MEMBERS');

        $this->template->content=new View('data/list');

        $action = "user/show/";
        $fields = array('firstname'=>'firstname', 'name'=>'name', 'username'=>'username');
        $this->template->content->listUrl = List_Controller::initList($this->user, access::REGISTERED,"user",$action, $fields, $id);
        $this->template->content->dataName = "user";

        // Set default sorting to field "name"
        $filter = ListFilter::instance();
        $filter->setDefaultSorting("name");
        $this->template->content->sortingName = $filter->getSortingName();
        $this->template->content->sortingOrder = $filter->getSortingOrderInt();

        // SEARCH
        $search = array();
        $search[0] = array('text'=>'user.firstname', 'name'=>'firstname');
        $search[1] = array('text'=>'user.name','name'=>'name');
        $search[2] = array('text'=>'user.username','name'=>'username');
        $this->template->content->showSearch = $filter->fillSearchFields($search);
        $this->template->content->search = $search;

        // PAGE INFO
        $this->setPageInfo('MEMBERS');

        // HEADERS
        $this->setHeaders('MEMBERS');
    }

    public function setMembers($id) {
        $this->loadData($id);

        $this->controlAccess('SET_MEMBERS');

        $this->template->content=new View('data/members');

        $fields = array('firstname'=>'firstname', 'name'=>'name', 'username'=>'username');
        $this->template->content->registerUrl = 'list/register/user/1';
        $this->template->content->unregisterUrl = 'list/register/user/0';
        $this->template->content->listUrl = List_Controller::initList($this->user, access::REGISTERED,"user","", $fields, null, true, null, "group", $id);
        $this->template->content->dataName = "user";

        // Set default sorting to field "name"
        $filter = ListFilter::instance();
        $filter->setDefaultSorting("name");
        $this->template->content->sortingName = $filter->getSortingName();
        $this->template->content->sortingOrder = $filter->getSortingOrderInt();

        // SEARCH
        $search = array();
        $search[0] = array('text'=>'user.firstname', 'name'=>'firstname');
        $search[1] = array('text'=>'user.name','name'=>'name');
        $search[2] = array('text'=>'user.username','name'=>'username');
        $this->template->content->showSearch = $filter->fillSearchFields($search);
        $this->template->content->search = $search;


        // Page INFO
        $this->setPageInfo('SET_MEMBERS');

        // HEADERS
        $this->setHeaders('SET_MEMBERS');
    }

    protected function setActions($action) {
        $group = $this->data;
        $actions = array();
        $actions_back = array();
        switch ($action) {
            case 'SHOW_ALL' :
                $actions_back[] = array('type' => 'button','text' => 'group.users','url' => 'user/showAll');
                if ($this->testAccess()) {
                    $actions[] = array('type' => 'button','url' => 'group/create','text' => 'group.create');
                }
                break;
            case 'SHOW' :
                if ((!$group->isProtected())&&($this->testAccess(access::OWNER))) {
                    $actions[] = array('type' => 'button','text' => 'button.edit','url' => 'group/edit/'.$group->id);
                    if ($group->isActive()) {
                        $actions[] = array('type' => 'button_confirm','text' => 'button.deactivate','confirm' => 'group.deactivate_confirm','url' => 'group/activate/'.$group->id.'/0');
                    } else {
                        $actions[] = array('type' => 'button_confirm','text' => 'button.activate','confirm' => 'group.activate_confirm','url' => 'group/activate/'.$group->id.'/1');
                    }
                    //$actions[] = array('type' => 'button','text' => 'group.view_members','url' => 'group/members/'.$group->id);
                    if ($this->testAdminAccess()) {
                        $actions[] = array('type' => 'button','text' => 'group.set_owner','url' => 'group/owner/'.$group->id);
                        $actions[] = array('type' => 'button_confirm','text' => 'button.delete','confirm' => 'group.delete_text','url' => 'group/delete/'.$group->id);
                    }
                }
                break;
            case 'EDIT' :
                $actions_back[] = array('type' => 'button','text' => 'button.cancel','url' => 'group/show/'.$group->id);
                $actions[] = array('type' => 'submit','text' => 'button.save');
                break;
            case 'CREATE' :
                $actions_back[] = array('type' => 'cancel','text' => 'button.cancel');
                $actions[] = array('type' => 'submit','text' => 'button.save');
                break;
            case 'OWNER' :
                $actions_back[] = array('type' => 'cancel','text' => 'button.cancel');
                break;
            case 'MEMBERS' :
                $actions[] = array('type' => 'button','text' => 'group.set_members','url' => 'group/setMembers/'.$group->id);
                break;
            case 'SET_MEMBERS' :
                //$actions_back[] = array('type' => 'button','url' => 'group/members/'.$group->id,'text' => 'button.back');
                $actions[] = array('type' => 'button','text' => 'button.terminate','url' => 'group/members/'.$group->id);
                break;
        }
        $tabs = array();
        switch ($action) {
            case 'SHOW' :
            case 'OWNER' :
            case 'EDIT' :
                $tabs[] = array('text'=>'group.info', 'link' => 'group/show/'.$group->id, 'current' => 1, 'image'=>Kohana::config('toucan.images_directory')."/information.png");
                $tabs[] = array('text'=>'group.view_members', 'link' => 'group/members/'.$group->id, 'image'=>Kohana::config('toucan.images_directory')."/group.png");
                break;
            case 'MEMBERS' :
            case 'SET_MEMBERS' :
                $tabs[] = array('text'=>'group.info', 'link' => 'group/show/'.$group->id, 'image'=>Kohana::config('toucan.images_directory')."/information.png");
                $tabs[] = array('text'=>'group.view_members', 'link' => 'group/members/'.$group->id, 'current' => 1, 'image'=>Kohana::config('toucan.images_directory')."/group.png");
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
                $headers[0] = array('name'=>'name', 'text'=>'group.name');
                if ($this->testAdminAccess()) {
                    $headers[1] = array('text'=>'group.owner');
                }
                break;
            case 'OWNER' :
            case 'MEMBERS' :
            case 'SET_MEMBERS' :
                $headers[0] = array('text'=>'user.firstname', 'name'=>'firstname');
                $headers[1] = array('text'=>'user.name','name'=>'name');
                $headers[2] = array('text'=>'user.username','name'=>'username');
                break;
        }
        $this->template->content->headers = $headers;
    }

    protected function setPath($action) {
        $user = $this->data;
        $path = array();
        if (($action != 'SHOW_ALL')&&($action != 'CREATE')) {
                $path[] = array('text'=>Kohana::lang('group.show_all_title'), 'link'=>'group/showAll');
        }
        $this->template->content->path = $path;
        $this->template->content->pathType = "path_group";
    }

    protected function setDescription($action) {
        $group = $this->data;
        parent::setDescription($action);
        if (($action != 'SHOW_ALL')&&($action != 'CREATE'))
            $this->template->content->title = sprintf(Kohana::lang('group.main_title', $group->name));
    }

}
?>