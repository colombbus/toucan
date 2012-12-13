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

abstract class DataPage_Controller extends Page_Controller {

    abstract protected function setHeaders($action);
    abstract protected function setActions($action);
    abstract protected function controlAccess($action);
    abstract protected function setPath($action);


    public function index () {
        $this->showAll();
    }

    protected function setDescription($action) {
        $this->template->content->title = Kohana::lang($this->dataName.".".strtolower($action)."_title");
        $this->template->content->description = Kohana::lang($this->dataName.".".strtolower($action)."_description");
        $this->template->content->help = Kohana::lang($this->dataName.".".strtolower($action)."_help");
    }

    public function setPageInfo($action) {
        $this->setDescription($action);
        $this->setActions($action);
        $this->setPath($action);
    }

    public function showAll($fields = null, $access = null, $action = null, $constraints= null) {
        // CONTROL ACCESS
        $this->controlAccess('SHOW_ALL');

        // TEMPLATE
        $this->template->content=new View('data/list');

        if (! isset ($fields)) {
            if ($this->testAdminAccess())
                $fields = array('name'=>'name','owner'=>'owner->fullName');
            else
                $fields = array('name'=>'name');
        }
        if (!isset ($access)) {
            $access = access::REGISTERED;
        }
        if (!isset ($action)) {
            $action = $this->dataName."/show/";
        }
        $this->template->content->listUrl = List_Controller::initList($this->user, $access,$this->dataName,$action, $fields, $constraints);
        $this->template->content->dataName = $this->dataName;

        // PAGE INFOS
        $this->setPageInfo('SHOW_ALL');

        // HEADERS
        $this->setHeaders('SHOW_ALL');
    }

    public function show($id=null, $template = 'data/display') {
        // If id is not provided, jump to showAll
        if ($id === null) {
            $this->showAll();
            return;
        }

        // LOAD DATA
        $this->loadData($id);
        
        // CONTROL ACCESS
        $this->controlAccess('SHOW');
        
        // TEMPLATE
        $this->template->content=new View($template);
        $this->template->content->data = $this->data->getDisplayableData($this->access, $this->user);

        // PAGE INFOS
        $this->setPageInfo('SHOW');
    }

    public function edit($id, $template = 'data/edit', $parameters=null) {
        // LOAD DATA
        $this->loadData($id);

        // CONTROL ACCESS
        $this->controlAccess('EDIT');

        // MANAGE FORM
        $formErrors= array();
        if (($post = $this->input->post())&&isset($post["form_edit_$this->dataName"])) {
			if ($this->data->validateEdition($post,$this->user, true)) {
                // data could be validated and saved
                $this->setMessage(Kohana::lang("$this->dataName.message_edited"));
                if (isset($post['url_next'])) {
                    if (isset($post['url_next_with_id'])) {
                        url::redirect(urldecode($post['url_next']).$id);
                    } else {
                        url::redirect(urldecode($post['url_next']));
                   }
                }
                url::redirect("$this->dataName/show/$id");
            } else {
                // errors when trying to validate data
                $formErrors = $this->data->getErrors("form_errors");
                // populate values in order to retrieve input data
                $this->data->setValues($post);
            }
        }


        // TEMPLATE
        $this->template->content=new View($template);
        $this->template->content->formId="form_edit_$this->dataName";

        // PAGE INFOS
        $this->setPageInfo('EDIT');

        $this->template->content->errors = $formErrors;

        // DATA
        $editionData = $this->data->getEditableData($this->access, $this->user);

        if (isset($parameters)) {
            foreach ($parameters as $key=>$value) {
                $editionData[] = array ('type' => 'hidden','name' => $key, 'value' => $value);
            }
        }
       $this->template->content->data = $editionData;

    }

    public function create($parameters=null, $template = 'data/edit', $createdHandler=null) {
        // CONTROL ACCESS
        $this->controlAccess('CREATE');

        // CREATE ITEM
        $item = ORM::factory($this->dataName);

        // MANAGE FORM
        $formErrors= array();
        if (($post = $this->input->post())&&isset($post["form_create_$this->dataName"])) {
            // form submitted
            if ($item->validateCreation($post,$this->user,true)) {
                // data could be validated and saved
               $this->setMessage(Kohana::lang("$this->dataName.message_created"));
               if (isset($createdHandler)) {
                   $this->$createdHandler($item, $parameters);
               }
               if (isset($post['url_next'])) {
                   if (isset($post['url_next_with_id'])) {
                    url::redirect(urldecode($post['url_next']).$item->id);
                   } else {
                    url::redirect(urldecode($post['url_next']));
                   }
               }
                else
                    url::redirect("$this->dataName/show/$item->id");
            } else {
                // errors while trying to validate creation
                $formErrors = $item->getErrors("form_errors");
                // populate values in order to retrieve input data
                $item->setValues($post);
            }
        }

        // DATA
        $creationData = $item->getCreationData($this->access, $this->user, $parameters);
        if (isset($parameters)) {
            foreach ($parameters as $key=>$value) {
                $creationData[] = array ('type' => 'hidden','name' => $key, 'value' => $value);
            }
        }

        // TEMPLATE
        $this->template->content=new View($template);
        $this->template->content->data = $creationData;
        $this->template->content->formId = "form_create_$this->dataName";

        // PAGE INFOS
        $this->setPageInfo('CREATE');

        $this->template->content->errors = $formErrors;
    }

    public function delete($id, $url=false) {
        // LOAD DATA
        $this->loadData($id);

        // CONTROL ACCESS
        $this->controlAccess('DELETE');

        // DELETION
        $this->data->delete();
        
        $this->setMessage(Kohana::lang("$this->dataName.message_deleted"));

        // REDIRECTION
        if (!$url)
            url::redirect("$this->dataName/showAll");
        else
            url::redirect(urldecode($url));
    }

    public function owner($id, $ownerId=null) {

        $this->loadData($id);

        $this->controlAccess('OWNER');

        if (isset($ownerId)) {
            // ownerId submitted
            $user = ORM::factory('user',$ownerId);
            if (!$user->loaded) {
                // user does not exist
                $this->displayError('user_unknown');
            }
            $this->data->setOwner($user,true);
            $this->setMessage(Kohana::lang("$this->dataName.message_edited"));
            url::redirect($this->dataName."/show/".$id);
        }

        $fields = array('firstname'=>'firstname', 'name'=>'name', 'username'=>'username');
        $this->template->content=new View('data/list');
        $this->template->content->listUrl = List_Controller::initList($this->user, access::ADMIN,"user",$this->dataName."/owner/$id/", $fields);
        $this->template->content->dataName = "user";

        // Deal with search
        $filter = ListFilter::instance();
        $search = array();
        $search[0] = array('text'=>'user.firstname', 'name'=>'firstname');
        $search[1] = array('text'=>'user.name','name'=>'name');
        $search[2] = array('text'=>'user.username','name'=>'username');
        $this->template->content->showSearch = $filter->fillSearchFields($search);
        $this->template->content->search = $search;

        // Set default filter to field "name"
        $filter->setDefaultSorting("name");
        $this->template->content->sortingName = $filter->getSortingName();
        $this->template->content->sortingOrder = $filter->getSortingOrderInt();

        $this->setPageInfo('OWNER');
        $this->setHeaders('OWNER');
    }

}
?>