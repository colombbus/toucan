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

class Survey_Controller extends FormSession_Controller {

    protected $sessionName = "survey";
    protected $dataName = "survey";
    protected $parentName = "activity";
    protected $parentIdField = "activityId";
    protected $parentIdName = "activity_id";
    protected $controllerName = "surveys";
    protected $copyName = "surveyCopy";
    protected $showAllAuthorized = true;

    protected function controlAccess($action) {
        // Special case where user is not registered, cannot view, but session is open to public
        if ($this->testAccess(access::MAY_CONTRIBUTE)&&(!$this->testAccess()&&!$this->data->isViewableBy($this->user))) {
            url::redirect("publicSurvey/form/".$this->data->id);
        }
        switch ($action) {
            case 'CREATE' :
            case 'SELECT_CREATE' :
                $this->ensureAccess(access::MAY_EDIT, $this->getActivity());
                break;
            case 'SHOW_ALL' :
                break;
            case 'SELECT_ACTIVITY' :
                $this->ensureAccess();
                break;
            case 'INDICATORS' :
                $this->ensureAccess(access::MAY_VIEW);
                break;
            case 'EXPORT_INDICATORS' :
                $this->ensureAccess(access::MAY_EDIT);
                break;
            default :
                parent::controlAccess($action);
                break;
        }
    }

    public function showAll($category = null) {
        $constraints = null;
        if (!isset($category)) {
            // show all surveyss
            $fields = array('name'=>'name','activity'=>'activity->name', 'state_id'=>'state->translatedName');
        } else {
            if ($category == 1) {
                // Surveys going on
                $constraints = array('state_id'=>EvaluationState_Model::GOING_ON);
                $fields = array('name'=>'name','activity'=>'activity->name');
                $this->context['goingOn'] = 1;
            } else {
                // Surveys published
                $constraints = array('state_id'=>EvaluationState_Model::OVER);
                $fields = array('name'=>'name','activity'=>'activity->name');
                $this->context['published'] = 1;
            }
        }
        parent::showAll($fields, access::ANYBODY, null, $constraints);

        // Deal with search
        $filter = ListFilter::instance();
        $search = array();
        $search[0] = array('text'=>'survey.name', 'name'=>'name');
        $this->template->content->showSearch = $filter->fillSearchFields($search);
        $this->template->content->search = $search;

        // Set default filter to field "name"
        $filter->setDefaultSorting("name");
        $this->template->content->sortingName = $filter->getSortingName();
        $this->template->content->sortingOrder = $filter->getSortingOrderInt();
    }
    
    public function indicators($surveyId) {

        // LOAD DATA
        $this->loadData($surveyId);

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
        $this->template->content->displayUrl = "surveyIndicator/show/";
        $this->template->content->deleteUrl = "axSurveyIndicator/delete/";
        $this->template->content->reorderUrl = "axSurveyIndicator/reorder/".$surveyId;
        $this->template->content->confirmDeletion = "indicator.delete_confirm";
        $this->template->content->itemAlreadyCreated = "indicator.already_created";
        $this->template->content->showContent = true;

        $this->setPageInfo('INDICATORS');

    }
    
    public function exportIndicators($surveyId) {
        // LOAD DATA
        $this->loadData($surveyId);

        // CONTROL ACCESS
        $this->controlAccess('EXPORT_INDICATORS');

        $this->data->exportIndicators($this->user);

        $this->auto_render = false;
    }
    
    public function create($parentId = null, $templateId = null) {
        if (!isset($parentId))
            return $this->selectActivity();
        parent::create($parentId, $templateId);
    }
    
    protected function selectActivity() {
        $activityController = new Activity_Controller();
        $activityController->showAll(0, 'survey/createStart/');
        $activityController->auto_render = false;
        $content = $activityController->template->content;
        $this->template->content = clone $content;
        $this->setPageInfo('SELECT_ACTIVITY');
    }
    
    protected function setHeaders($action) {
        if ($action == 'SHOW_ALL') {
            $headers = array();
            $headers[0] = array('text'=>'evaluation.name', 'name'=>'name');
            $headers[1] = array('text'=>'evaluation.activity','name'=>'activity_id');
            if (!isset($this->context['published'])&&!isset($this->context['goingOn'])) {
                $headers[2] = array('text'=>'evaluation.state','name'=>'state_id');
            }
            $this->template->content->headers = $headers;
        } else
            parent::setHeaders($action);
    }

    protected function setPath($action) {
        $path = array();
        if ($action != 'PREVIEW_STYLE') {
            $activity = $this->getActivity();
            if (isset($activity)) {
                $path[] = array('text'=>sprintf(Kohana::lang('activity.main_title', $activity->name)), 'link'=>"activity/surveys/$activity->id");
            }
        }
        $this->template->content->path = $path;
    }

    protected function setDescription($action) {
        $survey = $this->data;
        switch ($action) {
            case 'SHOW_ALL':
                parent::setDescription($action);
                if (isset($this->context['published'])) {
                    $this->template->content->description = Kohana::lang('survey.show_all_description_over');
                } else if (isset($this->context['goingOn'])) {
                    $this->template->content->description = Kohana::lang('survey.show_all_description_going_on');
                }
                break;
            case 'SELECT_ACTIVITY':
                $this->template->content->title = Kohana::lang('survey.select_activity_title');
                $this->template->content->description = Kohana::lang('survey.select_activity_description');
                $this->helpTopic = "selectActivity";
                break;
            case 'CREATE':
            case 'CREATE_START':
            case 'SELECT_TEMPLATE':
            case 'COPY_INDICATORS':
            case 'PREVIEW_STYLE':
                parent::setDescription($action);
                break;
            default:
                parent::setDescription($action);
                $this->template->content->title = sprintf(Kohana::lang('survey.main_title', $survey->name));
                break;
        }
        if ($action != 'PREVIEW_STYLE') {
            $activity = $this->getActivity();
            if (isset($activity)&&$activity->logo_id >0)
                $this->template->content->title_logo = $activity->logo->path;
        }
        $this->template->content->title_icon = null;

    }

    protected function getEvaluation() {
        return null;
    }
    
    protected function getActivity() {
        if (isset ($this->data)) {
            return $this->data->activity;
        } else if (isset ($this->context['activityId'])) {
            return ORM::factory('activity', $this->context['activityId']);
        }
        return null;
    }
    
    protected function getParent() {
        return $this->getActivity();
    }
    
    protected function setActions($action, $id = null) {
        switch ($action) {
            case 'INDICATORS':
                $survey = $this->data;
                $actions = array();
                $actions_back = array();
                $tabs = array();
                if ($this->testAccess(access::MAY_EDIT)) {
                    $actions[] = array('type' => 'button','text' => 'survey.add_indicator','url' => 'surveyIndicator/createStart/'.$survey->id);
                    $actions[] = array('type' => 'button','text' => 'survey.export','url' => 'survey/exportIndicators/'.$survey->id);
                }
                $tabs[] = array('text'=>'survey.info', 'link' => 'survey/show/'.$survey->id, 'image'=>Kohana::config('toucan.images_directory')."/information.png");
                if ($this->testAccess(access::MAY_EDIT)) {
                    $tabs[] = array('text'=>'survey.questions', 'link' => 'survey/questions/'.$survey->id,'image'=>Kohana::config('toucan.images_directory')."/questions.png");
                    $tabs[] = array('text'=>'survey.preview', 'link' => 'survey/preview/'.$survey->id,'image'=>Kohana::config('toucan.images_directory')."/preview.png");
                }
                $tabs[] = array('text'=>'survey.copies', 'link' => 'survey/copies/'.$survey->id, 'image'=>Kohana::config('toucan.images_directory')."/copies.png");
                $tabs[] = array('text'=>'survey.indicators', 'link' => 'survey/indicators/'.$survey->id, 'image'=>Kohana::config('toucan.images_directory')."/indicator.png", 'current' => 1);
                $this->template->content->actions = $actions;
                $this->template->content->actions_back = $actions_back;
                $this->template->content->tabs = $tabs;
                break;
             case 'SHOW_ALL' :
                if ($this->testAccess()) {
                    $actions[] = array('type' => 'button','url' => 'survey/create','text' => 'survey.create');
                    $this->template->content->actions = $actions;
                }
                    $quickActions[] = array('image' => Kohana::config('toucan.images_directory').'/all.png','text' => Kohana::lang('survey.show_all_all'),'url' => 'survey/showAll');
                    $quickActions[] = array('image' => Kohana::config('toucan.images_directory').'/going_on.png','text' => Kohana::lang('survey.show_all_going_on'),'url' => 'survey/showAll/1');
                    $quickActions[] = array('image' => Kohana::config('toucan.images_directory').'/over.png','text' => Kohana::lang('survey.show_all_over'),'url' => 'survey/showAll/2');
                $this->template->content->quickActions = $quickActions;
                break;
            case 'SELECT_ACTIVITY' :
                $actions = array();
                $actions_back[] = array('type' => 'cancel','text' => 'button.cancel');
                $this->template->content->actions = $actions;
                $this->template->content->actions_back = $actions_back;
                break;
            case 'PREVIEW_STYLE' :
                parent::setActions($action, $id);
                break;
            default:
                parent::setActions($action, $id);
                if (isset($this->data)&&$action!='COPY_INDICATORS') {
                    $this->template->content->tabs[] = array('text'=>'survey.indicators', 'link' => 'survey/indicators/'.$this->data->id, 'image'=>Kohana::config('toucan.images_directory')."/indicator.png");
                }
                break;
       }
    }


}
?>