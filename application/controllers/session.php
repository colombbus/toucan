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

abstract class Session_Controller extends DataPage_Controller {

    protected $sessionName = null;
    protected $templateName = null;
    protected $controllerName = null;
    protected $copyName = null;
    protected $context = array();
    protected $parentName = "evaluation";
    protected $parentIdField = "evaluationId";
    protected $parentIdName = "evaluation_id";
    protected $templateControllerName = null;
    protected $showAllAuthorized = false;

    
    abstract protected function mayBeEditedByPublic();

    protected function controlAccess($action) {
        // Special case where user is not registered, cannot view, but session is open to public
        if ($this->testAccess(access::MAY_CONTRIBUTE)&&(!$this->testAccess()&&!$this->data->isViewableBy($this->user))) {
            url::redirect("public/form/".$this->data->id);
        }
        switch ($action) {
            case 'CREATE' :
                $this->ensureAccess(access::MAY_EDIT, $this->getEvaluation());
                break;
            case 'SHOW' :
                $this->ensureAccess(access::MAY_VIEW);
                break;
            case 'COPIES' :
                $this->ensureAccess(access::MAY_VIEW);
                break;
            case 'EXPORT' :
                $this->ensureAccess(access::MAY_EDIT);
                break;
            case 'EDIT' :
            case 'SET_STATE' :
                $this->ensureAccess(access::MAY_EDIT);
                if (!$this->data->isEditable()) {
                    $this->displayError('restricted_access');
                }
                break;
            case 'DELETE' :
                $this->ensureAccess(access::OWNER);
                if (!$this->data->isEditable()) {
                    $this->displayError('restricted_access');
                }
                break;
            case 'DOWNLOAD_TEMPLATE' :
                $this->ensureAccess(access::MAY_CONTRIBUTE);
                break;
            case 'OWNER' :
                $this->ensureAccess(access::ADMIN);
                if (!$this->data->isEditable()) {
                    $this->displayError('restricted_access');
                }
                break;
            case 'COPY_INDICATORS' :
                $this->ensureAccess(access::OWNER);
                break;
            case 'QUESTIONS' :
            case 'PREVIEW' :
                $this->ensureAccess(access::MAY_EDIT);
                break;
            default : // in case other cases not covered
                $this->ensureAccess(access::ADMIN);
                break;
        }
    }

    public function create($parentId, $templateId=null) {
        $this->context[$this->parentIdField] = $parentId;
        $parameters = array($this->parentIdName=>$parentId);
        $handler = null;
        if (isset($templateId)) {
            $parameters['template_id'] = $templateId;
            $this->context['fromTemplate'] = true;
            $template = ORM::factory($this->templateName, $templateId);
            $indicatorIds = $template->getIndicatorIds($this->user);
            if (sizeof($indicatorIds)>0) {
                $parameters['url_next'] = $this->sessionName."/copyIndicators/$templateId/";
                $parameters['url_next_with_id'] = 1;
                $handler = 'saveVariablesMapping';
            }
        }
        parent::create($parameters, 'data/edit', $handler);
        $this->createConditions();
    }
    
    protected function saveVariablesMapping(& $item, & $parameters) {
        $templateId = $parameters['template_id'];
        $template = ORM::factory($this->templateName, $templateId);
        $variables = $template->getMapping();
        $this->session->set('variables_mapping', $variables);
    }

    public function createStart($parentId) {
        $this->context[$this->parentIdField] = $parentId;
        // CONTROL ACCESS
        $this->controlAccess('CREATE');

        // TEMPLATE
        $this->template->content=new View('select');

        $choices = array();
        $choices[] = array('text'=>Kohana::lang($this->sessionName.'.create_from_existing_template'), 'link'=>$this->sessionName.'/selectTemplate/'.$parentId, 'image'=>Kohana::config('toucan.images_directory')."/$this->templateName.png"); 
        $choices[] = array('text'=>Kohana::lang($this->sessionName.'.create_from_zero'), 'link'=>$this->sessionName.'/create/'.$parentId, 'image'=>Kohana::config('toucan.images_directory')."/new_".$this->templateName.".png"); 
        
        $this->template->content->choices  = $choices;
        $this->template->content->selectType = "select_session_creation";
                
        // PAGE INFOS
        $this->setPageInfo('CREATE_START');
    }
    
    public function selectTemplate($parentId) {
        $this->context[$this->parentIdField] = $parentId;
        // CONTROL ACCESS
        $this->controlAccess('CREATE');

        
        $evaluation = $this->getEvaluation();
        
        if (isset($evaluation)&&$evaluation->isOver())
        {
            $this->displayError('evaluation_closed');
        }

        $fields = array('name'=>'name','description'=>'description');
        $this->template->content=new View('data/list');
        $this->template->content->listUrl = List_Controller::initList($this->user, access::REGISTERED,$this->templateName,"$this->sessionName/create/$parentId/", $fields);
        $this->template->content->dataName = $this->templateName;

        $this->setPageInfo('SELECT_TEMPLATE');
        $this->setHeaders('SELECT_TEMPLATE');

        // Set default sorting to field "name"
        $filter = ListFilter::instance();
        $filter->setDefaultSorting("name");
        $this->template->content->sortingName = $filter->getSortingName();
        $this->template->content->sortingOrder = $filter->getSortingOrderInt();

        // SEARCH
        $search = array();
        $search[0] = array('text'=>$this->templateName.'.name', 'name'=>'name','default'=>1);
        $search[1] = array('text'=>$this->templateName.'.description','name'=>'description');
        $this->template->content->showSearch = $filter->fillSearchFields($search);
        $this->template->content->search = $search;
    }

    public function show($id) {
        parent::show($id);
        if ($this->data->isEditableBy($this->user, false) && !($this->data->isEditable())) {
            $this->template->content->info = Kohana::lang('session.not_editable');
        }
    }

    public function showAll($fields = null, $access = null, $action = null, $constraints= null) {
        if (!$this->showAllAuthorized)
            $this->displayError('restricted_access');
        else
            parent::showAll($fields, $access, $action, $constraints);
    }

    public function delete($id) {
        // LOAD DATA
        $this->loadData($id);
        $parentIdName = $this->parentIdName;
        $parentId = $this->data->$parentIdName;
        parent::delete($id, urlencode($this->parentName."/$this->controllerName/$parentId"));
    }

    public function edit($id) {
        parent::edit($id);
        $this->createConditions();
    }

    public function copies($id) {
        $this->loadData($id);

        $this->controlAccess('COPIES');

        $fields = array('owner'=>'owner_name', 'created'=>'translated_created', 'state_id'=>'state->translatedName');
        $this->template->content=new View('data/list');
        $this->template->content->listUrl = List_Controller::initList($this->user, access::ANYBODY,$this->copyName,$this->copyName."/show/", $fields, $id);
        $this->template->content->dataName = $this->copyName;

        $this->setPageInfo('COPIES');
        $this->setHeaders('COPIES');

        // Set default sorting to field "created"
        $filter = ListFilter::instance();
        $filter->setDefaultSorting('created',0);
        $this->template->content->sortingName = $filter->getSortingName();
        $this->template->content->sortingOrder = $filter->getSortingOrderInt();

        if ($this->data->isEditableBy($this->user, false)) {
            if (!($this->data->isOpen())) {
                $this->template->content->info = Kohana::lang('session.not_open');
            } else if (!($this->data->isParentOpen())) {
                $this->template->content->info = Kohana::lang('evaluation.not_open');
            }
        }
    }
    
    public function questions($id) {
        $this->loadData($id);
        $this->controlAccess('QUESTIONS');
        $templateController = new $this->templateControllerName();
        $templateController->questions($this->data->template_id);
        $templateController->auto_render = false;
        $content = $templateController->template->content;
        $this->template->content = clone $content;
        $this->setPageInfo('QUESTIONS');
        if ($this->data->isOpen()) {
            $this->template->content->info = Kohana::lang('session.questions_not_editable');
        }
    }

    public function preview($id, $style = null) {
        $this->loadData($id);
        $this->controlAccess('PREVIEW');
        if (isset($style) && $this->data->isPublic())
        {
            return $this->previewStyle();
        }
        $templateController = new $this->templateControllerName();
        $templateController->preview($this->data->template_id);
        $templateController->auto_render = false;
        $content = $templateController->template->content;
        $this->template->content = clone $content;
        $this->setPageInfo('PREVIEW');
    }

    protected function previewStyle() {
        $this->context['sessionId'] = $this->data->id;
        if ($this->data->style_id>0){
            $this->template = new TemplateView($this->data->style->getViewFile());
            $this->template->setStyle($this->data->style);
        } else {
            $this->template = new TemplateView(Style_Model::getDefaultViewFile());
        }
        
        // Set form language 
        if (isset($this->data->language))
            language::setCurrentLanguage($this->data->language);
        $this->template->setStep("form");
        $this->dataName = $this->copyName;
        $parameters = array('session_id'=>$this->data->id, 'public_access'=>true);
        if (($post = $this->input->post())&&isset($post["form_create_$this->dataName"])) {
            unset($_POST["form_create_$this->dataName"]);
        }
        parent::create($parameters,'copy/edit');
        $this->template->content->goingOnState = CopyState_Model::GOING_ON;
        $item = ORM::factory($this->copyName);
        $this->template->content->questionConditionals = $item->getConditionals();
        if ($item->getRequiredState())
            $this->template->content->requiredText = Kohana::lang($this->copyName.'.required_text');
        $this->setPageInfo('PREVIEW_STYLE');
    }
    
    public function setState($id, $stateId) {
        $this->loadData($id);
        $this->controlAccess('SET_STATE');
        $this->data->state_id = $stateId;
        $this->data->save();
        $state = ORM::factory('SessionState', $stateId);
        $this->setMessage(sprintf(Kohana::lang($this->sessionName.'.state_updated'), $state->getTranslatedName()));
        url::redirect("$this->sessionName/show/$id");
    }
    
    public function copyIndicators($templateId, $id) {
        // LOAD DATA
        $this->loadData($id);

        // CONTROL ACCESS
        $this->controlAccess('COPY_INDICATORS');
        
        $this->clearMessage();
        
        // MANAGE FORM
        if (($post = $this->input->post())&&isset($post["form_copy_indicators_$this->dataName"])) {
            // $indicatorIds have been sent
            $indicatorIds = $this->input->post('indicator_ids', array());
            $variables = $this->session->get('variables_mapping', array());
            $this->data->copyIndicators($indicatorIds, $this->user, $variables);
            $this->setMessage(Kohana::lang("$this->dataName.message_created"));
            url::redirect("$this->dataName/show/$id");
        }

        // TEMPLATE
        $this->template->content=new View('data/select_items');
        $this->template->content->formId="form_copy_indicators_$this->dataName";

        // DATA
        $template = ORM::factory($this->templateName,$templateId);
        if (!$template->loaded) {
                $this->displayError($this->templateName.'_unknown');
        }
        $this->ensureAccess(access::MAY_VIEW, $template);
        $indicators = $template->getIndicators($this->user);
        $items = array();
        foreach($indicators as $indicator) {
            $item = array();
            $item['id'] = $indicator->id;
            $item['name'] = $indicator->name;
            $item['description'] = $indicator->description;
            $items[] = $item;
        }
        $this->template->content->selectName = "indicator_ids";
        $this->template->content->items = $items;
        $this->template->content->selectAllItems = $this->templateName.'.select_all_indicators';
        $this->template->content->deselectAllItems = $this->templateName.'.deselect_all_indicators';

        // PAGE INFOS
        $this->setPageInfo('COPY_INDICATORS');
    }

    
    
    protected function setActions($action, $evaluationId = null) {
        $actions = array();
        $quickActions = array();
        $actions_back = array();
        $session = $this->data;
        switch ($action) {
            case 'SHOW_ALL' :
                $actions_back[] = array('type' => 'button','url' => 'home','text' => 'button.back');
                break;
            case 'SHOW' :
                if ($session->isEditableBy($this->user, true)) {
                    if ($this->testAccess(access::MAY_EDIT)) {
                        $actions[] = array('type' => 'button','text' => 'button.edit','url' => $this->sessionName.'/edit/'.$session->id);
                        $quickActions[] = array('image' => Kohana::config('toucan.images_directory').'/under_construction.png','text' => Kohana::lang($this->sessionName.'.set_state_under_construction'),'url' => $this->sessionName.'/setState/'.$session->id.'/'.SessionState_Model::UNDER_CONSTRUCTION);
                        $quickActions[] = array('image' => Kohana::config('toucan.images_directory').'/going_on.png','text' => Kohana::lang($this->sessionName.'.set_state_going_on'),'url' => $this->sessionName.'/setState/'.$session->id.'/'.SessionState_Model::GOING_ON);
                        $quickActions[] = array('image' => Kohana::config('toucan.images_directory').'/over.png','text' => Kohana::lang($this->sessionName.'.set_state_over'),'url' => $this->sessionName.'/setState/'.$session->id.'/'.SessionState_Model::OVER);
                        $quickActions[] = array('image' => Kohana::config('toucan.images_directory').'/cancelled.png','text' => Kohana::lang($this->sessionName.'.set_state_cancelled'),'url' => $this->sessionName.'/setState/'.$session->id.'/'.SessionState_Model::CANCELLED);
                    }
                    if ($this->testAccess(access::OWNER)) {
                        $actions[] = array('type' => 'button_confirm','text' => 'button.delete','confirm' => $this->sessionName.'.delete_text','url' => $this->sessionName.'/delete/'.$session->id);
                    }
                    if ($this->testAdminAccess()) {
                        $actions[] = array('type' => 'button','text' => $this->sessionName.'.set_owner','url' => $this->sessionName.'/owner/'.$session->id);
                    }
                }
                break;
            case 'EDIT' :
                $actions_back[] = array('type' => 'button','text' => 'button.cancel','url' => $this->sessionName.'/show/'.$session->id);
                $actions[] = array('type' => 'submit','text' => 'button.save');
                break;
            case 'CREATE' :
                if (isset($this->context['fromTemplate']))
                    $actions_back[] = array('type' => 'cancel','text' => 'button.step_back');
                else {
                    $parent = $this->getParent();
                    $actions_back[] = array('type' => 'button','text' => 'button.cancel','url' => $this->parentName.'/'.$this->controllerName.'/'.$parent->id);
                }
                $actions[] = array('type' => 'submit','text' => 'button.save');
                break;
            case 'OWNER' :
                $actions_back[] = array('type' => 'cancel','text' => 'button.cancel');
                break;
            case 'CREATE_START' :
                $actions_back[] = array('type' => 'cancel','text' => 'button.cancel');
                break;
            case 'SELECT_TEMPLATE' :
                $parent = $this->getParent();
                $actions_back[] = array('type' => 'button','text' => 'button.cancel','url' => $this->parentName.'/'.$this->controllerName.'/'.$parent->id);
                break;
            case 'EXPORT' :
                $actions_back[] = array('type' => 'cancel','text' => 'button.cancel');
                $actions[] = array('type' => 'submit','text' => $this->sessionName.'.launch_export');
                break;
            case 'LAUNCH_EXPORT' :
                $actions_back[] = array('type' => 'cancel','text' => 'button.cancel');
                break;
            case 'EXPORT_RESULT' :
                $actions_back[] = array('type' => 'button','text' => 'button.terminate', 'url'=>$this->sessionName.'/copies/'.$session->id);
                break;
            case 'DOWNLOAD_TEMPLATE' :
                $actions_back[] = array('type' => 'cancel','text' => 'button.cancel');
                $actions[] = array('type' => 'submit','text' => $this->sessionName.'.download_template');
                break;
            case 'COPIES' :
                if ($this->testAccess(access::MAY_CONTRIBUTE)&&($session->isOpen())&&($session->isParentOpen())) {
                    $actions[] = array('type' => 'button','text' => $this->sessionName.'.new_copy','url' => $this->copyName.'/create/'.$session->id);
                }
                if ($this->testAccess(access::MAY_CONTRIBUTE)) {
                    if ($this->testAccess(access::MAY_EDIT)) {
                        $currentTab = 3;
                    } else {
                        $currentTab = 1;
                    }
                    $actions[] = array('type' => 'button','text' => $this->sessionName.'.download_template','url' => $this->sessionName.'/downloadTemplate/'.$currentTab.'/'.$session->id);
                }
                if ($this->testAccess(access::MAY_EDIT)) {
                    $actions[] = array('type' => 'button','text' => $this->sessionName.'.export','url' => $this->sessionName.'/export/'.$session->id);
                }
                break;
            case 'QUESTIONS' :
                if ($this->testAccess(access::MAY_EDIT)&&$session->isEditable()&&$session->template->isEditable()) {
                    $actions[] = array('type' => 'button','text' => 'question.add','js' => 'addItem()');
                    $actions[] = array('type' => 'button','text' => 'question.add_separator','js' => 'addSeparator()');
                }
                $actions[] = array('type'=>'button', 'text'=> $this->sessionName.'.create_template', 'url' => $this->templateName.'/make/'.$session->template_id);
                break;
            case 'PREVIEW' :
                $actions[] = array('type' => 'button','text' => $this->sessionName.'.download_template','url' => $this->sessionName.'/downloadTemplate/2/'.$session->id);
                if ($this->data->isPublic()) {
                    // public formSession
                    $actions[] = array('type' => 'button','text' => $this->sessionName.'.preview_style','url' => $this->sessionName.'/preview/'.$session->id.'/1', 'newWindow'=>1);
                }
                break;
            case 'COPY_INDICATORS' :
                $actions_back[] = array('type' => 'button','text' => $this->templateName.'.do_not_copy_indicators', 'url'=>$this->sessionName.'/show/'.$session->id);
                $actions[] = array('type' => 'submit','text' => 'button.save');
                break;
            case 'PREVIEW_STYLE' : 
                $actions[] = array('type'=>'submit', 'text'=>'button.send');
                break;
        }
        $tabs = array();
        if (($action != 'CREATE_START')&&($action != 'CREATE')&&($action != 'SELECT_TEMPLATE')&&($action != 'COPY_INDICATORS')&&($action != 'SHOW_ALL')&&($action != 'PREVIEW_STYLE')) {
            $tabs[] = array('text'=>$this->sessionName.'.info', 'link' => $this->sessionName.'/show/'.$session->id, 'image'=>Kohana::config('toucan.images_directory')."/information.png");
            if ($this->testAccess(access::MAY_EDIT)) {
                $tabs[] = array('text'=>$this->sessionName.'.questions', 'link' => $this->sessionName.'/questions/'.$session->id,'image'=>Kohana::config('toucan.images_directory')."/questions.png");
                $tabs[] = array('text'=>$this->sessionName.'.preview', 'link' => $this->sessionName.'/preview/'.$session->id,'image'=>Kohana::config('toucan.images_directory')."/preview.png");
            }
            $tabs[] = array('text'=>$this->sessionName.'.copies', 'link' => $this->sessionName.'/copies/'.$session->id,'image'=>Kohana::config('toucan.images_directory')."/copies.png");
        }
        switch ($action) {
            case 'SHOW' :
            case 'EDIT' :
            case 'OWNER' :
                $tabs[0]['current'] = 1;
                break;
            case 'COPIES' :
            case 'EXPORT' :
            case 'LAUNCH_EXPORT' :
            case 'EXPORT_RESULT' :
                $tabs[sizeof($tabs)-1]['current'] = 1;
                break;
            case 'QUESTIONS' :
                $tabs[1]['current'] = 1;
                break;
            case 'PREVIEW'  :
                $tabs[2]['current'] = 1;
                break;
            case 'DOWNLOAD_TEMPLATE'  :
                $tabs[$this->context['current']]['current'] = 1;
                break;
        }

        $this->template->content->actions = $actions;
        $this->template->content->quickActions = $quickActions;
        $this->template->content->actions_back = $actions_back;
        $this->template->content->tabs = $tabs;
    }

    protected function setHeaders($action) {
        $headers = array();
        switch ($action) {
            case 'SHOW_ALL' :
                $headers[0] = array('name'=>'name', 'text'=>$this->templateName.'.name');
                $headers[1] = array('text'=>$this->templateName.'.description');
                if ($this->testAdminAccess()) {
                    $headers[2] = array('text'=>$this->templateName.'.owner');
                }
                break;
            case 'OWNER' :
                $headers[0] = array('text'=>'user.firstname', 'name'=>'firstname');
                $headers[1] = array('text'=>'user.name','name'=>'name');
                $headers[2] = array('text'=>'user.username','name'=>'username');
                break;
            case 'SELECT_TEMPLATE' :
                $headers[0] = array('text'=>$this->templateName.'.name', 'name'=>'name');
                $headers[1] = array('text'=>$this->templateName.'.description','name'=>'description');
                break;
            case 'COPIES' :
                $headers[0] = array('text'=>$this->sessionName.'.contributor', 'name'=>'owner_id');
                $headers[1] = array('text'=>$this->sessionName.'.created','name'=>'created');
                $headers[2] = array('text'=>$this->sessionName.'.copy_state','name'=>'state_id',);
                break;
        }
        $this->template->content->headers = $headers;
    }

    protected function setPath($action) {
        $path = array();
        if ($action != 'PREVIEW_STYLE') {
            $evaluation = $this->getEvaluation();
            if (isset($evaluation)) {
                $activity = $evaluation->activity;
                $path[] = array('text'=>sprintf(Kohana::lang('activity.main_title', $activity->name)), 'link'=>"activity/evaluations/$activity->id");
                $path[] = array('text'=>sprintf(Kohana::lang('evaluation.main_title', $evaluation->name)), 'link'=>"evaluation/".$this->controllerName."/$evaluation->id");
            }
        }
        $this->template->content->path = $path;
    }

    protected function setDescription($action) {
        $session = $this->data;
        parent::setDescription($action);
        if ($action != 'CREATE_START'&&$action != 'SELECT_TEMPLATE'&&$action != 'CREATE'&&$action != 'COPY_INDICATORS'&&$action != 'SHOW_ALL'&&$action!='PREVIEW_STYLE') {
            $this->template->content->title = sprintf(Kohana::lang($this->sessionName.'.main_title', $session->name));
        } else if ($action == 'SELECT_TEMPLATE') {
            $this->template->content->title_steps = array('max'=>3, 'current'=>1);
        } else if ($action == 'COPY_INDICATORS') {
            $this->template->content->title_steps = array('max'=>3, 'current'=>3);
        } else if ($action == 'CREATE'&&isset($this->context['fromTemplate'])) {
            $this->template->content->title_steps = array('max'=>3, 'current'=>2);
        } else if ($action == 'PREVIEW_STYLE') {
            $session = ORM::factory($this->sessionName, $this->context['sessionId']);
            $this->template->title = $session->name;
            $description = $session->description;
            if (strlen(trim($description))>0) {
                $this->template->description = str_replace("\n", "<br/>", htmlspecialchars($description,ENT_QUOTES, "UTF-8"));
            } else {
                $this->template->description = Kohana::lang('public.form_description');
            }
            $this->template->content->title = null;
            $this->template->content->description = null;
            $this->template->content->title_icon = null;
            $this->template->content->title_logo = null;
        }
        if ($action != 'PREVIEW_STYLE') {
            $this->template->content->title_icon = Kohana::config('toucan.images_directory')."/$this->sessionName.png";
            $evaluation = $this->getEvaluation();
            if (isset($evaluation)) {
                $activity = $evaluation->activity;
                if (isset($activity)&&$activity->logo_id >0)
                    $this->template->content->title_logo = $activity->logo->path;
            }
            $this->template->content->pathType = "path_activity";
        }
    }

    protected function getEvaluation() {
        if ((isset ($this->data))&&($this->data->evaluation_id>0)) {
            return $this->data->evaluation;
        } else if (isset ($this->context['evaluationId'])) {
            return ORM::factory('evaluation', $this->context['evaluationId']);
        }
        return null;
    }

    protected function createConditions() {
        if ($this->mayBeEditedByPublic()) {
            $conditional[] = array('trigger'=>'public_access', 'triggered'=>'contribute_id','reverse'=>true);
            $conditional[] = array('trigger'=>'public_access', 'triggered'=>'password_flag');
            $conditional[] = array('trigger'=>'public_access', 'triggered'=>'password');
            $conditional[] = array('trigger'=>'public_access', 'triggered'=>'style_id');
            $conditional[] = array('trigger'=>'public_access', 'triggered'=>'language');
            $conditional[] = array('trigger'=>'password_flag','triggered'=>'password', 'enable'=>true);
        }
        $conditional[] = array('trigger'=>'inherit', 'triggered'=>'inherit_view');
        $conditional[] = array('trigger'=>'inherit', 'triggered'=>'inherit_edit');
        $conditional[] = array('trigger'=>'inherit', 'triggered'=>'view_id','reverse'=>true);
        $conditional[] = array('trigger'=>'inherit', 'triggered'=>'edit_id','reverse'=>true);
        $conditional[] = array('trigger'=>'notification', 'triggered'=>'email', 'value'=>notification::NOTIFY_OTHER);
        $this->template->content->conditional = $conditional;
    }
    
    
    public function downloadTemplate($where,$id) {
        $this->loadData($id);
        $this->controlAccess('DOWNLOAD_TEMPLATE');
        
        $this->context['current'] = $where;
        
        if ($this->data->hasPrivateQuestions($this->user)) {
            if (($post = $this->input->post())&&isset($post["form_download_template_$this->dataName"])) {
                $private = $this->data->getDownloadTemplateOption($post);
                return $this->launchDownload($id, $private);
            } 
            // TEMPLATE
            $this->template->content=new View('data/edit');
            $this->template->content->formId="form_download_template_$this->dataName";

            // PAGE INFOS
            $this->setPageInfo('DOWNLOAD_TEMPLATE');
            $formErrors= array();
            $this->template->content->errors = $formErrors;
        
            // DATA
            $this->template->content->data = $this->data->getDownloadTemplateParameters();

        } else {
            $this->launchDownload($id);
        }
        
    }
    
    public function export($id) {
        $this->loadData($id);
        $this->controlAccess('EXPORT');

        $formErrors= array();

        if (($post = $this->input->post())&&isset($post["form_export_$this->dataName"])) {
            $this->data->setExportParameters($post);
            if ($this->data->validateExportParameters($post)) {
                return $this->launchExport($this->data->getExportParameters());
            } else {
                $formErrors = $this->data->getErrors("form_errors");
            }
        } else {
            $post = array();
            $this->data->setExportParameters($post);
        }

        // TEMPLATE
        $this->template->content=new View('data/edit');
        $this->template->content->formId="form_export_$this->dataName";

        // PAGE INFOS
        $this->setPageInfo('EXPORT');

        $this->template->content->errors = $formErrors;

        // DATA
        $this->template->content->data = $this->data->getExportEditableParameters($this->user);

        $conditional = array();
        $conditional[] = array('trigger'=>'date', 'triggered'=>'start_date');
        $conditional[] = array('trigger'=>'date', 'triggered'=>'end_date');
        $conditional[] = array('trigger'=>'format', 'triggered'=>'encoding', 'values'=>array(1,2));
        $this->template->content->conditional = $conditional;
    }

    protected function launchDownload($id, $private = false) {
        $this->loadData($id);
        $this->data->exportInDocument($private);
        $this->auto_render = false;
    }
    
    protected function launchExport(& $parameters) {
        // SET UP LIST OF COPIES
        $sessionId = $this->data->id;
        if ($parameters['date']) {
            // select a period
            if (isset($parameters['start_date'])&&(strlen(trim($parameters['start_date']))>0))
                $this->data->where('created>=', $parameters['start_date']);
            if (isset($parameters['end_date'])&&(strlen(trim($parameters['end_date']))>0))
                $this->data->where('created<=', $parameters['end_date']);
        }
        if (!$parameters['unpublished'])
            $this->data->in('copies.state_id',CopyState_Model::getPublishedStates());
        else
            $this->data->in('copies.state_id', CopyState_Model::getAllStates());
        $copies = $this->data->getCopies();
        $copies_id = $copies->primary_key_array();
        // SET PARAMETERS
        $parameters['field_separator'] = stripslashes($parameters['field_separator']);
        $parameters['copy_separator'] = str_replace("\\n", "\n", $parameters['copy_separator']);
        if ($parameters['format']==1)
            $parameters['escaped'] = array('"'=>'""');
        else
            $parameters['escaped'] = array();

        $separator = $parameters['field_separator'];
        $rowSeparator = $parameters['copy_separator'];
        $boundary = $parameters['field_boundary'];
        $screen = ($parameters['format']==0);
        $iso = ($parameters['encoding']==0);
        $escaped = $parameters['escaped'];
                
        // CREATE TEMP FILE
        $fileName = tempnam("/tmp", "toucan_exp_");
        
        if ($parameters['add_headers']) {
            $row = "";
            if ($parameters['add_author']) {
                $row.=$boundary.text::escape(Kohana::lang($this->sessionName.".contributor"), $escaped).$boundary.$separator;
            }
            if ($parameters['add_date']) {
                $row.=$boundary.text::escape(Kohana::lang($this->sessionName.".created"), $escaped).$boundary.$separator;
            }
            $template = $this->data->template;
            $questions = $template->getQuestions($parameters['private']);
            if ($template->questionAdvanced()) {
                // Put variables instead of question texts
                foreach ($questions as $question) {
                    if (!$question->isSeparator())
                        $row.=$boundary.text::escape($question->variable->name, $escaped).$boundary.$separator;
                }
            } else {
                // Put summary, then Put questions directly
                $row.=$boundary.text::escape(Kohana::lang($this->copyName.".summary"), $escaped).$boundary.$separator;
                foreach ($questions as $question) {
                    if (!$question->isSeparator())
                        $row.=$boundary.text::escape($question->text, $escaped).$boundary.$separator;
                }
            }
            $row = substr($row, 0, strlen($row)-1);
            if ($parameters['add_state']) {
                $row.=$separator.$boundary.text::escape(Kohana::lang($this->sessionName.".copy_state"), $escaped).$boundary;
            }
            $row.=$rowSeparator;
            if ($screen) {
                $row = str_replace("\n","<br>",$row);
            } else if ($iso) {
                $row = str_replace("\n","\r\n",utf8_decode($row));
            }
            $file = fopen ($fileName, 'w');
            fwrite($file, $row);
            fclose($file);
        }
        
        // SET SESSION DATA
        $sessionPrefix = "EXPORT_session_{$sessionId}";
        $this->session->set_flash($sessionPrefix."_copies",$copies_id);
        $this->session->set_flash($sessionPrefix."_count",$copies->count());
        $this->session->set_flash($sessionPrefix."_processed",0);
        $this->session->set_flash($sessionPrefix."_parameters",$parameters);
        $this->session->set_flash($sessionPrefix."_fileName",$fileName);
        $this->session->set_flash($sessionPrefix."_copyName",$this->copyName);

        
        // SET TEMPLATE
        $this->template->content=new View('session/export');
        $this->template->content->fetchUrl= "axSession/export/{$this->dataName}/$sessionId";
        $this->template->content->resultUrl= $this->sessionName.'/exportResult/'.$sessionId;
        // PAGE INFOS
        $this->setPageInfo('LAUNCH_EXPORT');
    }
    
    public function exportResult($id) {
        $this->loadData($id);
        $this->controlAccess('EXPORT');
        
        $sessionPrefix = "EXPORT_session_$id";
        $tmpFileName = $this->session->get($sessionPrefix."_fileName");
        $parameters = $this->session->get($sessionPrefix."_parameters", null);
        $download = $this->session->get($sessionPrefix."_download", false);
        
        if (!isset($parameters)) {
            url::redirect($this->sessionName."/export/".$id);
        }
        
        $screen = ($parameters['format']==0);
        
        if ($screen) {
            $buffer = file_get_contents($tmpFileName);
            unlink($tmpFileName);
            $this->template->content=new View('data/text');
            $this->template->content->text = $buffer;
            $this->setPageInfo('EXPORT_RESULT');
        } else {
            if (!$download) {
                // first update display
                $this->template->content=new View('session/download');
                $this->setPageInfo('EXPORT_RESULT');
                // set download to true for next call
                $this->session->set_flash($sessionPrefix."_download",true);
                $this->session->keep_flash();
            } else {
                $buffer = file_get_contents($tmpFileName);
                unlink($tmpFileName);
                $fileName = sprintf(Kohana::lang('session.export_filename'), Utils::translateTimestampForFilename(time()));
                if ($parameters['format']==1) {
                   $fileName .= ".csv";
                } else {
                   $fileName .= ".txt";
                }
                download::force($fileName, $buffer);
                $this->auto_render = false;
            }
        }
    }
    
    protected function getParent() {
        return $this->getEvaluation();
    }

}
?>