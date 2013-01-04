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

class Template_Controller extends DataPage_Controller {

    protected $templateName = null;
    protected $sessionName = null;
    protected $indicatorControllerName = null;
    protected $context = array();

    protected function controlAccess($action) {
        switch ($action) {
            case 'CREATE' :
                if (isset($this->context['preview']))
                    // PREVIEW
                    $this->ensureAccess(access::MAY_VIEW);
                else
                    $this->ensureAccess();
                break;
            case 'SHOW_ALL' :
            case 'TOC' :
                $this->ensureAccess();
                break;
            case 'EDIT' :
            case 'MAKE' :
                $this->ensureAccess(access::MAY_EDIT);
                break;
            case 'COPY_INDICATORS' :
                $this->ensureAccess(access::OWNER);
                break;
            case 'DELETE' :
                $this->ensureAccess(access::OWNER);
                if (!$this->data->isEditable()) {
                    $this->displayError('restricted_access');
                }
                break;
            case 'OWNER' :
                $this->ensureAccess(access::ADMIN);
                break;
            case 'SHOW' :
            case 'QUESTIONS' :
            case 'COPY' :
            case 'PREVIEW' :
            case 'INDICATORS' :
            case 'EXPORT' :
                $this->ensureAccess(access::MAY_VIEW);
                break;
            case 'COPY_QUESTIONS' :
                $this->ensureAccess(access::OWNER);
                break;
            default : // in case other cases not covered
                $this->ensureAccess(access::ADMIN);
                break;
        }
    }

    public function showAll() {
        $fields = array('name_header'=>'name','description_header'=>'shortDescription');

        if ($this->testAdminAccess())
            $fields['owner_header']='owner->fullName';
        parent::showAll($fields);

        // Deal with search
        $filter = ListFilter::instance();
        $search = array();
        $search[0] = array('name'=>'name', 'text'=>$this->templateName.'.name');
        $search[1] = array('name'=>'description', 'text'=>$this->templateName.'.description');
        $this->template->content->showSearch = $filter->fillSearchFields($search);
        $this->template->content->search = $search;

        // Set default sorting to field "name"
        $filter->setDefaultSorting("name");
        $this->template->content->sortingName = $filter->getSortingName();
        $this->template->content->sortingOrder = $filter->getSortingOrderInt();
    }

    public function show($id) {
        parent::show($id);
        if ($this->data->isEditableBy($this->user)&&!$this->data->isDeletable()) {
            $this->template->content->info = Kohana::lang('template.not_deletable');
        }
    }

    public function copy($id, $newId = null) {
        if (isset ($newId)) {
            // template copied, we have to copy questions
            $this->loadData($newId);
            $this->controlAccess('COPY_QUESTIONS');
            $previousVersion = ORM::factory($this->dataName,$id);
            if (!$previousVersion->loaded) { // Data not found
                $this->displayError($this->dataName.'_unknown');
            } else {
                $this->ensureAccess(access::MAY_VIEW, $previousVersion);
                $variables = $this->data->copy($previousVersion);
                $indicatorIds = $previousVersion->getIndicatorIds($this->user);
                if (sizeof($indicatorIds)>0) {
                    // There are indicators that could be copied
                    $this->session->set('variables_mapping', $variables);
                    url::redirect("$this->dataName/copyIndicators/0/$id/$newId");
                } else {
                    // No indicator: we jump directly to show
                    $this->setMessage(Kohana::lang("$this->dataName.message_copied"));
                    url::redirect("$this->dataName/show/$newId");
                }
            }
        } else {
            // LOAD DATA
            $this->loadData($id);
            // CONTROL ACCESS
            $this->controlAccess('MAKE');
            parent::create(array('copy_id'=>$id, 'url_next'=>$this->templateName."/copy/$id/", 'url_next_with_id'=>1));
            if (!isset($this->template->content->errors)||(sizeof($this->template->content->errors)==0)) {                
                // no error occured
                $this->template->content->data[0]['value'] = $this->data->name.Kohana::lang('template.new_version');
                $this->template->content->data[1]['value'] = $this->data->description;
            }
            // PAGE INFOS
            $this->setPageInfo('COPY');
        }
    }
    
    public function make($instanceId, $newId = null) {
        if (isset ($newId)) {
            // template created, we have to copy questions
            $this->loadData($newId);
            $this->controlAccess('COPY_QUESTIONS');
            $previousVersion = ORM::factory($this->dataName,$instanceId);
            if (!$previousVersion->loaded) { // Data not found
                $this->displayError($this->dataName.'_unknown');
            } else {
                $this->ensureAccess(access::MAY_VIEW, $previousVersion);
                $variables = $this->data->copy($previousVersion);
                $session = $previousVersion->session;
                $indicatorIds = $previousVersion->session->getIndicatorIds($this->user);
                if ($previousVersion->session->evaluation_id > 0) {
                    // Session belongs to an Evaluation: we add manual indicators from this evaluation
                    $indicatorIds = array_merge($indicatorIds,$previousVersion->session->evaluation->getManualIndicatorsIds($this->user));
                }
                if (sizeof($indicatorIds)>0) {
                    // There are indicators that could be copied
                    $this->session->set('variables_mapping', $variables);
                    url::redirect("$this->dataName/copyIndicators/1/$instanceId/$newId");
                } else {
                    // No indicator: we jump directly to show
                    $this->setMessage(Kohana::lang("$this->dataName.message_made"));
                    url::redirect("$this->dataName/show/$newId");
                }
            }
        } else {
            // LOAD DATA
            $this->loadData($instanceId);
            // CONTROL ACCESS
            $this->controlAccess('MAKE');
            parent::create(array('copy_id'=>$instanceId, 'url_next'=>$this->templateName."/make/$instanceId/", 'url_next_with_id'=>1));
            if (!isset($this->template->content->errors)||(sizeof($this->template->content->errors)==0)) {                
                // no error occured
                $session = $this->data->session;
                if (isset($session)) {
                    $this->template->content->data[0]['value'] = $session->name;
                    $this->template->content->data[1]['value'] = $session->description;
                }
            }
            // PAGE INFOS
            $this->setPageInfo('MAKE');
        }
    }

    public function copyIndicators($useSession, $instanceId, $templateId) {
        // LOAD DATA
        $this->loadData($templateId);

        // CONTROL ACCESS
        $this->controlAccess('COPY_INDICATORS');

        // MANAGE FORM
        if (($post = $this->input->post())&&isset($post["form_copy_indicators_$this->dataName"])) {
            // $indicatorIds have been sent
            $indicatorIds = $this->input->post('indicator_ids', array());
            $variables = $this->session->get('variables_mapping', array());
            $this->data->copyIndicators($indicatorIds, $this->user, $variables);
            if ($useSession)
                $this->setMessage(Kohana::lang("$this->dataName.message_made"));
            else
                $this->setMessage(Kohana::lang("$this->dataName.message_copied"));
            url::redirect("$this->dataName/show/$templateId");
        }

        // TEMPLATE
        $this->template->content=new View('data/select_items');
        $this->template->content->formId="form_copy_indicators_$this->dataName";

        // DATA
        $instance = ORM::factory($this->dataName,$instanceId);
        if (!$instance->loaded) {
                $this->displayError($this->dataName.'_unknown');
        }
        $this->ensureAccess(access::MAY_VIEW, $instance);
        if ($useSession)
            $indicators = $instance->session->getIndicators($this->user);
        else 
            $indicators = $instance->getIndicators($this->user);
        $items = array();
        foreach($indicators as $indicator) {
            $item = array();
            $item['id'] = $indicator->id;
            $item['name'] = $indicator->name;
            $item['description'] = $indicator->description;
            $items[$indicator->order] = $item;
        }
        if ($useSession&&$instance->session->evaluation_id > 0) {
            // Session belongs to an Evaluation: we add manual indicators from this evaluation
            $manualIndicators = $instance->session->evaluation->getManualIndicators($this->user);
            foreach($manualIndicators as $indicator) {
                $item = array();
                $item['id'] = $indicator->id;
                $item['name'] = $indicator->name;
                $item['description'] = $indicator->description;
                $items[$indicator->order] = $item;
            }
        }
        $this->template->content->selectName = "indicator_ids";
        $this->template->content->items = $items;
        $this->template->content->selectAllItems = $this->templateName.'.select_all_indicators';
        $this->template->content->deselectAllItems = $this->templateName.'.deselect_all_indicators';

        // PAGE INFOS
        $this->setPageInfo('COPY_INDICATORS');
    }
    
    public function questions($templateId) {
        // LOAD DATA
        $this->loadData($templateId);
        // CONTROL ACCESS
        $this->controlAccess('QUESTIONS');
        
        $questions = $this->data->getDisplayableQuestions($this->user);

        $this->template->content=new View('question/view_all');

        $this->template->content->items = $questions;

        if (sizeof($questions)==0) {
            $this->template->content->noItems = "question.no_item";
        }

        $this->template->content->mayEdit = ($this->testAccess(access::MAY_EDIT)&&$this->data->isEditable());
        $this->template->content->isDraggable = ($this->testAccess(access::MAY_EDIT)&&$this->data->isEditable());
        $this->template->content->editUrl = "axQuestion/edit/";
        $this->template->content->displayUrl = "axQuestion/show/";
        $this->template->content->addUrl = "axQuestion/create/".$templateId;
        $this->template->content->addSeparatorUrl = "axQuestion/createSeparator/".$templateId;
        $this->template->content->deleteUrl = "axQuestion/delete/";
        $this->template->content->addChoiceUrl = "axQuestion/addChoice/";
        $this->template->content->triggersUrl = "axQuestion/triggers/";
        $this->template->content->reorderUrl = "axQuestion/reorder/".$this->dataName."/".$templateId."/";
        $this->template->content->templateId = $templateId;
        $this->template->content->confirmDeletion = "question.delete_confirm";
        $this->template->content->confirmSeparatorDeletion = "question.delete_separator_confirm";
        $this->template->content->alreadyEditing = "question.already_editing";
        $this->template->content->hideItems = "question.hide_all";
        $this->template->content->showItems = "question.show_all";

        if ($this->testAccess(access::MAY_EDIT)&&!$this->data->isEditable()) {
            $this->template->content->info = Kohana::lang('template.not_editable');
        }

        $this->setPageInfo('QUESTIONS');
    }

    public function preview($templateId) {
        // LOAD DATA
        $this->loadData($templateId);

        // CONTROL ACCESS
        $this->controlAccess('PREVIEW');

        //$this->context['sessionId'] = $id;
        $this->dataName = $this->copyName;
        $parameters = array('template_id'=>$templateId);
        $this->context['preview'] = true;
        $this->create($parameters,'copy/edit');
        $this->template->content->goingOnState = CopyState_Model::GOING_ON;
        // PAGE INFOS
        $this->setPageInfo('PREVIEW');
    }

    public function download($templateId) {
        // LOAD DATA
        $this->loadData($templateId);

        // CONTROL ACCESS
        $this->controlAccess('EXPORT');

        $this->data->exportInDocument();

        $this->auto_render =false;
    }

    public function toc() {
        // CONTROL ACCESS
        $this->controlAccess('TOC');

        // TEMPLATE
        $this->template->content=new View('select');

        $choices = array();
        $choices[] = array('text'=>Kohana::lang('template.formTemplates'), 'link'=>'formTemplate/showAll', 'image'=>Kohana::config('toucan.images_directory')."/formTemplate.png"); 
        $choices[] = array('text'=>Kohana::lang('template.interviewTemplates'), 'link'=>'interviewTemplate/showAll', 'image'=>Kohana::config('toucan.images_directory')."/interviewTemplate.png"); 
        $choices[] = array('text'=>Kohana::lang('template.styleTemplates'), 'link'=>'style/showAll', 'image'=>Kohana::config('toucan.images_directory')."/style.png"); 
        
        $this->template->content->choices  = $choices;
        $this->template->content->selectType = "select_template";
                
        // PAGE INFOS
        $this->setPageInfo('TOC');
    }

    public function indicators($templateId) {
        // LOAD DATA
        $this->loadData($templateId);

        // CONTROL ACCESS
        $this->controlAccess('INDICATORS');

        $indicators = $this->data->getDisplayableIndicators($this->user);

        $this->template->content=new View('data/view_items');

        $this->template->content->items = $indicators;

        if (sizeof($indicators)==0) {
            $this->template->content->noItems = "indicator.no_item";
        }

        $this->template->content->mayEdit = $this->testAccess(access::MAY_EDIT);
        $this->template->content->isDraggable = $this->testAccess(access::MAY_EDIT);
        $this->template->content->displayUrl = $this->indicatorControllerName."/show/";
        $this->template->content->deleteUrl = "axTemplateIndicator/delete/";
        $this->template->content->reorderUrl = "axTemplateIndicator/reorder/".$templateId;
        $this->template->content->confirmDeletion = "indicator.delete_confirm";
        $this->template->content->alreadyEditing = "indicator.already_editing";
        $this->template->content->showContent = true;

        $this->setPageInfo('INDICATORS');

    }

    protected function setActions($action) {
        $template = $this->data;
        $actions = array();
        $actions_back = array();
        switch ($action) {
            case 'SHOW_ALL' :
                if ($this->testAccess()) {
                    $actions[] = array('type' => 'button','url' => $this->templateName.'/create','text' => $this->templateName.'.create');
                }
                break;
            case 'SHOW' :
                if ($this->testAccess(access::MAY_EDIT)) {
                    $actions[] = array('type' => 'button','text' => 'button.edit','url' => $this->templateName.'/edit/'.$template->id);
                }
                if ($this->testAccess(access::OWNER)&&$template->isDeletable()) {
                    $actions[] = array('type' => 'button_confirm','text' => 'button.delete','confirm' => $this->templateName.'.delete_text','url' => $this->templateName.'/delete/'.$template->id);
                }
                if ($this->testAdminAccess()) {
                    $actions[] = array('type' => 'button','text' => $this->templateName.'.set_owner','url' => $this->templateName.'/owner/'.$template->id);
                }
                $actions[] = array('type' => 'button','text' => 'template.copy','url' => $this->templateName.'/copy/'.$template->id);
                break;
            case 'EDIT' :
                $actions_back[] = array('type' => 'button','text' => 'button.cancel','url' => $this->templateName.'/show/'.$template->id);
                $actions[] = array('type' => 'submit','text' => 'button.save');
                break;
            case 'CREATE' :
            case 'COPY' :
            case 'MAKE' :
                $actions_back[] = array('type' => 'cancel','text' => 'button.cancel');
                $actions[] = array('type' => 'submit','text' => 'button.save');
                break;
            case 'OWNER' :
                $actions_back[] = array('type' => 'cancel','text' => 'button.cancel');
                break;
            case 'QUESTIONS' :
                if ($this->testAccess(access::MAY_EDIT)&&$template->isEditable()) {
                    $actions[] = array('type' => 'button','text' => 'question.add','js' => 'addItem()');
                    $actions[] = array('type' => 'button','text' => 'question.add_separator','js' => 'addSeparator()');
                }
                break;
            case 'PREVIEW' :
                $actions[] = array('type' => 'button','text' => 'template.download','url' => $this->templateName.'/download/'.$template->id);
                break;
            case 'TOC' :
                break;
            case 'INDICATORS' :
                if ($this->testAccess(access::MAY_EDIT)) {
                    $actions[] = array('type' => 'button','text' => $this->templateName.'.add_indicator','url' => $this->indicatorControllerName.'/createStart/'.$template->id);
                }
                break;
            case 'COPY_INDICATORS' :
                $actions_back[] = array('type' => 'button','text' => $this->templateName.'.do_not_copy_indicators', 'url'=>$this->templateName.'/show/'.$template->id);
                $actions[] = array('type' => 'submit','text' => 'button.save');
                break;
        }
        $tabs = array();
        if (($action != 'SHOW_ALL')&&($action != 'CREATE')&&($action != 'COPY')&&($action != 'TOC')&&($action != 'MAKE')&&($action != 'COPY_INDICATORS')) {
            $tabs[] = array('text'=>$this->templateName.'.info', 'link' => $this->templateName.'/show/'.$template->id, 'image'=>Kohana::config('toucan.images_directory')."/information.png");
            $tabs[] = array('text'=>$this->templateName.'.questions', 'link' => $this->templateName.'/questions/'.$template->id, 'image'=>Kohana::config('toucan.images_directory')."/questions.png");
            $tabs[] = array('text'=>$this->templateName.'.preview', 'link' => $this->templateName.'/preview/'.$template->id, 'image'=>Kohana::config('toucan.images_directory')."/preview.png");
            $tabs[] = array('text'=>$this->templateName.'.indicators', 'link' => $this->templateName.'/indicators/'.$template->id, 'image'=>Kohana::config('toucan.images_directory')."/indicator.png");
        }
        switch ($action) {
            case 'SHOW' :
            case 'EDIT' :
            case 'OWNER' :
                $tabs[0]['current'] = 1;
                break;
            case 'QUESTIONS' :
                $tabs[1]['current'] = 1;
                break;
            case 'PREVIEW' :
                $tabs[2]['current'] = 1;
                break;
            case 'INDICATORS' :
                $tabs[3]['current'] = 1;
        }
        $this->template->content->actions = $actions;
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
        }
        $this->template->content->headers = $headers;
    }

    protected function setPath($action) {
        $template = $this->data;
        $path = array();
        $this->template->content->path = $path;
        $this->template->content->pathType = "path_template";
    }

    protected function setDescription($action) {
        $template = $this->data;
        parent::setDescription($action);
        if (($action != 'SHOW_ALL')&&($action != 'CREATE')&&($action != 'TOC')&&($action != 'MAKE')&&($action != 'COPY_INDICATORS'))
            $this->template->content->title = sprintf(Kohana::lang($this->templateName.'.main_title', $template->name));
        if ($action == 'TOC') {
            $this->template->content->title = Kohana::lang('template.main_title');
            $this->template->content->description = Kohana::lang('template.toc_description');
        }
    }


}
?>