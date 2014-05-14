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

class Category_Controller extends DataPage_Controller {

    protected $dataName = "category";
    protected $parentControllerName = "evaluation";
    protected $parentControllerMethod = "categories";
    protected $indicatorControllerName = "indicator";
    protected $indicatorModel = "indicator";
    protected $parentName = "evaluation";
    protected $parentIdField = "evaluationId";
    protected $parentIdName = "evaluation_id";
    protected $controllerName = "category";

    protected function controlAccess($action) {
        switch ($action) {
            case 'SHOW' :
            case 'MEMBERS' :
                $this->ensureAccess(access::MAY_VIEW);
                break;
            case 'CREATE' :
                $this->ensureAccess(access::MAY_EDIT, $this->getParent());
                break;
            case 'EDIT' :
            case 'ACTIVATE' :
            case 'SET_MEMBERS' :
            case 'DELETE' :
                $this->ensureAccess(access::MAY_EDIT);
                break;
            case 'OWNER' :
                $this->ensureAccess(access::ADMIN);
                break;
            default : // in case other cases not covered
                $this->ensureAccess(access::ADMIN);
                break;
        }
    }
    
    public function create($parentId) {
        $this->context[$this->parentIdField] = $parentId;
        $parameters = array($this->parentIdName=>$parentId);
        parent::create($parameters, 'data/edit');
        $this->createConditions();
    }    

    public function activate($id,$value=true, $url=false) {
        $this->loadData($id);

        $this->controlAccess('ACTIVATE');

        $this->data->setActive($value,true);

        if ($value)
           $this->setMessage(Kohana::lang("category.message_activated"));
        else
           $this->setMessage(Kohana::lang("category.message_deactivated"));

        if (!$url)
            url::redirect("$this->controllerName/show/$id");
        else
            url::redirect(urldecode($url));
    }

    public function edit($id) {
        parent::edit($id);
        $this->createConditions();
    }
    
    public function delete($id) {
        // LOAD DATA
        $this->loadData($id);
        $parentIdName = $this->parentIdName;
        $parentId = $this->data->$parentIdName;
        parent::delete($id, urlencode($this->parentName."/$this->parentControllerMethod/$parentId"));
    }

    public function members($id) {
        $this->loadData($id);
        $this->controlAccess('MEMBERS');

        if ($this->data->isRecapitulative()) {
            // we should not be here
            $this->setErrorMessage(Kohana::lang("category.error_recapitulative"));
            url::redirect("$this->controllerName/show/$id");
        }
        
        $this->template->content=new View('data/list');

        $action = "$this->indicatorControllerName/show/";
        $fields = array('name'=>'name', 'type'=>'fullType');
        $parentId = $this->parentIdName;

        $this->template->content->listUrl = List_Controller::initList($this->user, access::REGISTERED,$this->indicatorModel,$action, $fields, array($parentId=>$this->data->$parentId, 'category_id'=>$this->data->id));
        $this->template->content->dataName = $this->indicatorModel;

        // Set default sorting to field "name"
        $filter = ListFilter::instance();
        $filter->setDefaultSorting("name");
        $this->template->content->sortingName = $filter->getSortingName();
        $this->template->content->sortingOrder = $filter->getSortingOrderInt();

        // SEARCH
        $search = array();
        $search[0] = array('text'=>'indicator.name', 'name'=>'name');
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

        if ($this->data->isRecapitulative()) {
            // we should not be here
            $this->setErrorMessage(Kohana::lang("category.error_recapitulative"));
            url::redirect("$this->controllerName/show/$id");
        }

        $this->template->content=new View('data/members');

        $fields = array('order'=>'order', 'name'=>'name', 'type'=>'fullType');
        $this->template->content->registerUrl = "list/register/$this->indicatorModel/1";
        $this->template->content->unregisterUrl = "list/register/$this->indicatorModel/0";
        $parentId = $this->parentIdName;
        $this->template->content->listUrl = List_Controller::initList($this->user, access::REGISTERED,$this->indicatorModel,"", $fields, array($parentId=>$this->data->$parentId), true, null, "category", $id);
        $this->template->content->dataName = $this->indicatorModel;

        // Set default sorting to field "name"
        $filter = ListFilter::instance();
        $filter->setDefaultSorting("order");
        $this->template->content->sortingName = $filter->getSortingName();
        $this->template->content->sortingOrder = $filter->getSortingOrderInt();

        // SEARCH
        $search = array();
        $search[0] = array('text'=>'indicator.name', 'name'=>'name');
        $this->template->content->showSearch = $filter->fillSearchFields($search);
        $this->template->content->search = $search;


        // Page INFO
        $this->setPageInfo('SET_MEMBERS');

        // HEADERS
        $this->setHeaders('SET_MEMBERS');
    }
    
    protected function createConditions() {
        $conditional[] = array('trigger'=>'inherit', 'triggered'=>'inherit_view');
        $conditional[] = array('trigger'=>'inherit', 'triggered'=>'inherit_edit');
        $conditional[] = array('trigger'=>'inherit', 'triggered'=>'view_id','reverse'=>true);
        $conditional[] = array('trigger'=>'inherit', 'triggered'=>'edit_id','reverse'=>true);
        $conditional[] = array('trigger'=>'published', 'triggered'=>'contribute_id','reverse'=>true);
        $conditional[] = array('trigger'=>'published', 'triggered'=>'password_flag');
        $conditional[] = array('trigger'=>'published', 'triggered'=>'password');
        $conditional[] = array('trigger'=>'published', 'triggered'=>'style_id');
        $conditional[] = array('trigger'=>'published', 'triggered'=>'language');
        $conditional[] = array('trigger'=>'password_flag','triggered'=>'password', 'enable'=>true);

        $this->template->content->conditional = $conditional;
    }


    protected function setActions($action) {
        $category = $this->data;
        $actions = array();
        $actions_back = array();
        switch ($action) {
            case 'SHOW' :
                if ($this->testAccess(access::MAY_EDIT)) {
                    $actions[] = array('type' => 'button','text' => 'button.edit','url' => "$this->controllerName/edit/$category->id");
                    if ($category->isActive()) {
                        $actions[] = array('type' => 'button_confirm','text' => 'button.deactivate','confirm' => 'category.deactivate_confirm','url' => "$this->controllerName/activate/$category->id/0");
                    } else {
                        $actions[] = array('type' => 'button_confirm','text' => 'button.activate','confirm' => 'category.activate_confirm','url' => "$this->controllerName/activate/$category->id/1");
                    }
                    if ($this->testAccess(access::OWNER)) {
                        $actions[] = array('type' => 'button_confirm','text' => 'button.delete','confirm' => 'category.delete_text','url' => "$this->controllerName/delete/$category->id");
                    }
                    if ($this->testAdminAccess()) {
                        $actions[] = array('type' => 'button','text' => 'category.set_owner','url' => "$this->controllerName/owner/$category->id");
                    }
                }
                break;
            case 'EDIT' :
                $actions_back[] = array('type' => 'button','text' => 'button.cancel','url' => "$this->controllerName/show/$category->id");
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
                $actions[] = array('type' => 'button','text' => 'category.set_members','url' => "$this->controllerName/setMembers/$category->id");
                break;
            case 'SET_MEMBERS' :
                $actions[] = array('type' => 'button','text' => 'button.terminate','url' => "$this->controllerName/members/$category->id");
                break;
        }
        $tabs = array();
        if ($action != 'CREATE') {
            $parent = $this->getParent();
            $tabs[] = array('text'=>'category.info', 'link' => "$this->controllerName/show/$category->id", 'image'=>Kohana::config('toucan.images_directory')."/information.png");
            if (!$category->isRecapitulative())
                $tabs[] = array('text'=>'category.view_members', 'link' => "$this->controllerName/members/$category->id", 'image'=>Kohana::config('toucan.images_directory')."/application_cascade.png");
            $tabs[] = array('text'=>'category.display', 'link' => "$this->parentControllerName/indicators/$parent->id/$category->id", 'image'=>Kohana::config('toucan.images_directory')."/indicator.png");
            switch ($action) {
                case 'SHOW' :
                case 'OWNER' :
                case 'EDIT' :
                    $tabs[0]['current'] = 1;
                    break;
                case 'MEMBERS' :
                case 'SET_MEMBERS' :
                    $tabs[1]['current'] = 1;
                    break;
            }
        }
        $this->template->content->actions = $actions;
        $this->template->content->actions_back = $actions_back;
        $this->template->content->tabs = $tabs;
    }

    protected function setHeaders($action) {
        $headers = array();
        switch ($action) {
            case 'SHOW_ALL' :
                $headers[0] = array('name'=>'name', 'text'=>'category.name');
                $headers[1] = array('name'=>'description', 'text'=>'category.description');
                break;
            case 'OWNER' :
                $headers[0] = array('text'=>'user.firstname', 'name'=>'firstname');
                $headers[1] = array('text'=>'user.name','name'=>'name');
                $headers[2] = array('text'=>'user.username','name'=>'username');
                break;
            case 'MEMBERS' :
                $headers[0] = array('text'=>'indicator.name_header', 'name'=>'name');
                $headers[1] = array('text'=>'indicator.type_header','name'=>'type');
                break;
            case 'SET_MEMBERS' :
                $headers[0] = array('text'=>'indicator.order', 'name'=>'order');
                $headers[1] = array('text'=>'indicator.name_header', 'name'=>'name');
                $headers[2] = array('text'=>'indicator.type_header','name'=>'type');
                break;
        }
        $this->template->content->headers = $headers;
    }

    protected function setPath($action) {
        $path = array();
        $parent = $this->getParent();
        if (isset($parent)) {
            $activity = $parent->activity;
            $path[] = array('text'=>sprintf(Kohana::lang("activity.main_title"), $activity->name), 'link'=>"activity/evaluations/$activity->id");
            $path[] = array('text'=>sprintf(Kohana::lang($this->parentControllerName.".main_title"), $parent->name), 'link'=>$this->parentControllerName."/categories/$parent->id");
        }
        $this->template->content->path = $path;
    }

    protected function setDescription($action) {
        $category = $this->data;
        parent::setDescription($action);
        if (($action != 'SHOW_ALL')&&($action != 'CREATE'))
            $this->template->content->title = sprintf(Kohana::lang('category.main_title', $category->name));
        $this->template->content->title_icon = Kohana::config("toucan.images_directory")."/category.png";
        $this->template->content->pathType = "path_activity";
    }

    protected function getParent() {
        if (isset ($this->data)) {
            $parentName = $this->parentName;
            return $this->data->$parentName;
        } else if (isset ($this->context[$this->parentIdField])) {
            return ORM::factory($this->parentName, $this->context[$this->parentIdField]);
        }
        return null;
    }

    
    
}
?>