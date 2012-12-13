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

class Style_Controller extends DataPage_Controller {

    protected $dataName = "style";
    protected $context = array();
    protected $xssFiltering = false;
	protected $cleanInput = true;

    protected function controlAccess($action) {
        switch ($action) {
            case 'CREATE' :
            case 'SHOW_ALL' :
                $this->ensureAccess();
                break;
            case 'SHOW' :
            case 'USAGE' :
                $this->ensureAccess(access::MAY_VIEW);
                break;
            case 'EDIT' :
            case 'FILES' :
            case 'EDIT_FILES' :
                $this->ensureAccess(access::MAY_EDIT);
                break;
            case 'DELETE' :
                $this->ensureAccess(access::OWNER);
                break;
            case 'OWNER' :
                $this->ensureAccess(access::ADMIN);
                break;
            default : // in case other cases not covered
                $this->ensureAccess(access::ADMIN);
                break;
        }
        if ($this->cleanInput) {
            $this->cleanInputs();
        }
    }

    public function usage($styleId) {
        // LOAD DATA
        $this->loadData($styleId);

        // CONTROL ACCESS
        $this->controlAccess('USAGE');

        // Test if the template is used
        if ($this->data->formSessions->count()>0) {
            // TEMPLATE
            $this->template->content=new View('data/list');
            $fields = array('name'=>'name', 'activityName'=>'activity->name');

            $action = "formSession/show/";
            $constraints = array('style_id'=>$this->data->id);

            $this->template->content->listUrl = List_Controller::initList($this->user, access::REGISTERED,"formSession",$action, $fields, $constraints);
            $this->template->content->dataName = "formSession";

            // HEADERS
            $this->setHeaders('USAGE');

            // Set default sorting to field "name"
            $filter = ListFilter::instance();
            $filter->setDefaultSorting("name");
            $this->template->content->sortingName = $filter->getSortingName();
            $this->template->content->sortingOrder = $filter->getSortingOrderInt();

        } else {
            // TEMPLATE
            $this->template->content=new View('data/text');
            $this->template->content->text = Kohana::lang('style.not_used');
        }
        // PAGE INFOS
        $this->setPageInfo('USAGE');
    }

    public function showAll() {
        $fields = array('name_header'=>'name','description_header'=>'description');
        
        if ($this->testAdminAccess())
            $fields['owner_header']='owner->fullName';
        parent::showAll($fields);

        // Deal with search
        $filter = ListFilter::instance();
        $search = array();
        $search[0] = array('name'=>'name', 'text'=>'style.name');
        $search[1] = array('name'=>'description', 'text'=>'style.description');
        $this->template->content->showSearch = $filter->fillSearchFields($search);
        $this->template->content->search = $search;

        // Set default sorting to field "name"
        $filter->setDefaultSorting("name");
        $this->template->content->sortingName = $filter->getSortingName();
        $this->template->content->sortingOrder = $filter->getSortingOrderInt();
    }
    
    public function files($styleId) {
        // LOAD DATA
        $this->loadData($styleId);

        // CONTROL ACCESS
        $this->controlAccess('FILES');
        
        // UPDATE FILES
        $this->data->updateFiles();

        // TEMPLATE
        $this->template->content=new View('data/list');

        $access = access::REGISTERED;
        
        $fileIds= $this->data->files->primary_key_array();
        $fields = array('name_header'=>'name');
        $action = "style/editFile/$styleId/";

        $icons = array();
        $icons[] = array('src'=>Kohana::config('toucan.images_directory').'/edit.png', 'action'=>'style/editFile/'.$styleId.'/', 'text'=>'style.edit_file');
        $icons[] = array('src'=>Kohana::config('toucan.images_directory').'/delete.png', 'action'=>'style/deleteFile/'.$styleId.'/', 'text'=>'style.delete_file', 'confirm'=>'style.delete_file_confirm');

        $this->template->content->listUrl = List_Controller::initList($this->user, $access,"file",$action, $fields, $fileIds, false, $icons);
        $this->template->content->dataName = "file";
        $this->template->content->listIcons = 2;

        $filter = ListFilter::instance();

        // Deal with search
        $search = array();
        $search[0] = array('text'=>'style.fileName','name'=>'name');
        $this->template->content->showSearch = $filter->fillSearchFields($search);
        $this->template->content->search = $search;

        // Set default sorting to field "name"
        $filter->setDefaultSorting("name");
        $this->template->content->sortingName = $filter->getSortingName();
        $this->template->content->sortingOrder = $filter->getSortingOrderInt();
        
        // PAGE INFOS
        $this->setPageInfo('FILES');

        // HEADERS
        $this->setHeaders('FILES');
    }
    
    public function deleteFile($styleId, $fileId) {
        // LOAD DATA
        $this->loadData($styleId);

        // CONTROL ACCESS
        $this->controlAccess('FILES');
        
        if ($this->data->deleteFile($fileId))
            $this->setMessage(Kohana::lang("style.file_deleted"));
        url::redirect("style/files/$styleId");
    }

    public function newFile($styleId) {
        // LOAD DATA
        $this->loadData($styleId);

        // CONTROL ACCESS
        $this->controlAccess('FILES');
        
        $this->context['style_id'] = $styleId;

        $parameters = array();
        $parameters['style_id'] = $styleId;
        $parameters['directory'] = $this->data->getDirectory();
        $parameters['url_next'] = "style/files/$styleId";
        $this->dataName = "file";
        parent::create($parameters);
        
        // PAGE INFOS
        $this->setPageInfo('NEW_FILE');
    }

    public function editFile($styleId, $fileId) {
        // LOAD DATA
        $this->loadData($styleId);

        // Do not clean input
		$this->cleanInput = false;
        
		// CONTROL ACCESS
        $this->controlAccess('EDIT_FILES');
        
        $this->context['style_id'] = $styleId;
        
        $parameters = array();
        $parameters['url_next'] = "style/files/$styleId";
        $this->dataName = "file";
		parent::edit($fileId,'data/edit', $parameters);
        
        // PAGE INFOS
        $this->setPageInfo('EDIT_FILE');
    }
    
    public function sendFile($styleId) {
        // LOAD DATA
        $this->loadData($styleId);

        // CONTROL ACCESS
        $this->controlAccess('FILES');
        
        // CREATE ITEM
        $item = ORM::factory('file');

        // MANAGE FORM
        $formErrors= array();
        if (($post = $this->input->post())&&isset($post["form_send_file"])) {
            // form submitted
            if ($item->validateUpload($post,$this->user,true)) {
                // data could be validated and saved
               $this->setMessage(Kohana::lang("$this->dataName.message_sent"));
                url::redirect("style/files/$styleId");
            } else {
                // errors while trying to validate creation
                $formErrors = $item->getErrors("form_errors");
                // populate values in order to retrieve input data
                $item->setValues($post);
            }
        }

        // DATA
        $creationData = $item->getFileUploadData($this->access, $this->user);
        $creationData[] = array ('type' => 'hidden','name' => 'style_id', 'value' => $styleId);
        $creationData[] = array ('type' => 'hidden','name' => 'directory', 'value' => $this->data->getDirectory());

        // TEMPLATE
        $this->template->content=new View('data/edit');
        $this->template->content->data = $creationData;
        $this->template->content->formId = "form_send_file";

        // PAGE INFOS
        $this->setPageInfo('SEND_FILE');

        $this->template->content->errors = $formErrors;
    }
    
    protected function setHeaders($action) {
        $headers = array();
        switch ($action) {
            case 'SHOW_ALL' :
                $headers[0] = array('name'=>'name', 'text'=>'style.name_header');
                $headers[1] = array('text'=>'style.description_header');
                if ($this->testAdminAccess()) {
                    $headers[2] = array('text'=>'style.owner');
                }
                break;
            case 'OWNER' :
                $headers[0] = array('text'=>'user.firstname', 'name'=>'firstname');
                $headers[1] = array('text'=>'user.name','name'=>'name');
                $headers[2] = array('text'=>'user.username','name'=>'username');
                break;
            case 'USAGE' :
                $headers[0] = array('text'=>'style.sessionName', 'name'=>'name');
                $headers[1] = array('text'=>'style.activityName','name'=>'activity_id');
                break;
            case 'FILES' :
                $headers[0] = array('text'=>'style.fileName', 'name'=>'name');
                break;

        }
        $this->template->content->headers = $headers;
    }

    protected function setActions($action) {
        $style = $this->getStyle();
        $actions = array();
        $actions_back = array();
        switch ($action) {
            case 'SHOW_ALL' :
                if ($this->testAccess()) {
                    $actions[] = array('type' => 'button','url' => 'style/create','text' => 'style.create');
                }
                break;
            case 'SHOW' :
                if ($this->testAccess(access::MAY_EDIT)) {
                    $actions[] = array('type' => 'button','text' => 'button.edit','url' => 'style/edit/'.$style->id);
                }
                if ($this->testAccess(access::OWNER)) {
                    $actions[] = array('type' => 'button_confirm','text' => 'button.delete','confirm' => 'style.delete_text','url' => 'style/delete/'.$style->id);
                }
                if ($this->testAdminAccess()) {
                    $actions[] = array('type' => 'button','text' => 'style.set_owner','url' => 'style/owner/'.$style->id);
                }
                break;
            case 'EDIT' :
                $actions_back[] = array('type' => 'button','text' => 'button.cancel','url' => 'style/show/'.$style->id);
                $actions[] = array('type' => 'submit','text' => 'button.save');
                break;
            case 'CREATE' :
                $actions_back[] = array('type' => 'cancel','text' => 'button.cancel');
                $actions[] = array('type' => 'submit','text' => 'button.save');
                break;
            case 'OWNER' :
                $actions_back[] = array('type' => 'cancel','text' => 'button.cancel');
                break;
            case 'USAGE' :
                break;
            case 'FILES' :
                $actions[] = array('type' => 'button','text' => 'style.new_file','url' => 'style/newFile/'.$style->id);
                $actions[] = array('type' => 'button','text' => 'style.send_file','url' => 'style/sendFile/'.$style->id);
                break;
            case 'NEW_FILE' :
            case 'EDIT_FILE' :
                $actions_back[] = array('type' => 'cancel','text' => 'button.cancel');
                $actions[] = array('type' => 'submit','text' => 'button.save');
                break;
            case 'SEND_FILE' :
                $actions_back[] = array('type' => 'cancel','text' => 'button.cancel');
                $actions[] = array('type' => 'submit','text' => 'button.send');
                break;
        }
        $tabs = array();
        if (($action != 'SHOW_ALL')&&($action != 'CREATE')) {
            $tabs[] = array('text'=>'style.info', 'link' => 'style/show/'.$style->id, 'image'=>Kohana::config('toucan.images_directory')."/information.png");
            if ($this->testAccess(access::MAY_EDIT))
                $tabs[] = array('text'=>'style.files', 'link' => 'style/files/'.$style->id, 'image'=>Kohana::config('toucan.images_directory')."/files.png");
            $tabs[] = array('text'=>'style.usage', 'link' => 'style/usage/'.$style->id, 'image'=>Kohana::config('toucan.images_directory')."/usage.png");
        }
        switch ($action) {
            case 'SHOW' :
            case 'EDIT' :
            case 'OWNER' :
                $tabs[0]['current'] = 1;
                break;
            case 'FILES' :
            case 'NEW_FILE' :
            case 'EDIT_FILE' :
            case 'SEND_FILE' :
                $tabs[1]['current'] = 1;
                break;
            case 'USAGE' :
                if ($this->testAccess(access::MAY_EDIT))
                    $tabs[2]['current'] = 1;
                else 
                    $tabs[1]['current'] = 1;
                break;
        }
        $this->template->content->actions = $actions;
        $this->template->content->actions_back = $actions_back;
        $this->template->content->tabs = $tabs;
    }


    protected function setPath($action) {
        $path = array();
        $this->template->content->path = $path;
        $this->template->content->pathType = "path_template";
    }

    protected function setDescription($action) {
        $style = $this->getStyle();
        parent::setDescription($action);
        if (($action != 'SHOW_ALL')&&($action != 'CREATE'))
            $this->template->content->title = sprintf(Kohana::lang('style.main_title', $style->name));
    }

    protected function getStyle() {
        if (isset($this->context['style_id']))
            return ORM::factory('style', $this->context['style_id']);
        else if (isset($this->data))
            return $this->data;
        return null;
    }
    
    protected function cleanInputs() {
        foreach ($_GET as $key=>$val) {
            // Sanitize $_GET
            $_GET[$key] = $this->input->xss_clean($val);
        }
        foreach ($_POST as $key => $val) {
            // Sanitize $_POST
            $_POST[$key] = $this->input->xss_clean($val);
        }
    }
}
?>