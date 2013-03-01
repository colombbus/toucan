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

class Evaluation_Controller extends DataPage_Controller {

    protected $dataName = "evaluation";
    protected $context = array();

    protected function controlAccess($action) {
        switch ($action) {
            case 'CREATE' :
                $this->ensureAccess();
                if (!($this->testAccess(access::MAY_EDIT, $this->getActivity()))) {
                    $this->displayError('restricted_access');
                }
                break;
            case 'SELECT_ACTIVITY' :
                $this->ensureAccess();
                break;
            case 'SHOW_ALL' :
                break;
            case 'SHOW' :
            case 'FORMS' :
            case 'INTERVIEWS' :
            case 'INDICATORS' :
                $this->ensureAccess(access::MAY_VIEW);
                break;
            case 'EDIT' :
            case 'EXPORT' :
            case 'SET_STATE' :
            case 'CATEGORIES' :
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
    }

    public function create($activityId = null) {
        if (!isset($activityId))
            return $this->selectActivity();
        $this->context['activityId'] = $activityId;
        parent::create(array('activity_id'=>$activityId));
        $this->createConditions();
    }

    protected function selectActivity() {
        $activityController = new Activity_Controller();
        $activityController->showAll(0, 'evaluation/create/');
        $activityController->auto_render = false;
        $content = $activityController->template->content;
        $this->template->content = clone $content;
        $this->setPageInfo('SELECT_ACTIVITY');
    }
    
    public function edit($id) {
        parent::edit($id);
        $this->createConditions();
    }

    public function forms($evaluationId) {

        $this->loadData($evaluationId);

        $this->controlAccess('FORMS');

        $fields = array('name'=>'name', 'state_id'=>'state->translatedName');
        $icons = array();
        $icons[] = array('src'=>Kohana::config('toucan.images_directory').'/information.png', 'action'=>'formSession/show/', 'text'=>'formSession.info');
        $icons[] = array('src'=>Kohana::config('toucan.images_directory').'/copies.png', 'action'=>'formSession/copies/', 'text'=>'formSession.copies');
        $this->template->content=new View('data/list');
        $this->template->content->listUrl = List_Controller::initList($this->user, access::ANYBODY,"formSession","formSession/show/", $fields, array('evaluation_id'=>$evaluationId), false, $icons);
        $this->template->content->dataName = "formSession";
        $this->template->content->listIcons = 2;

        $this->setPageInfo('FORMS');
        $this->setHeaders('FORMS');

        // Set default sorting to field "state_id"
        $filter = ListFilter::instance();
        $filter->setDefaultSorting("state_id");
        $this->template->content->sortingName = $filter->getSortingName();
        $this->template->content->sortingOrder = $filter->getSortingOrderInt();

        // SEARCH
        $search = array();
        $search[0] = array('text'=>'session.name', 'name'=>'name');
        $this->template->content->showSearch = $filter->fillSearchFields($search);
        $this->template->content->search = $search;

        if ($this->data->isEditableBy($this->user, false) && ($this->data->isOver())) {
            $this->template->content->info = Kohana::lang('evaluation.closed');
        }

    }

    public function interviews($evaluationId) {

        $this->loadData($evaluationId);

        $this->controlAccess('INTERVIEWS');

        $fields = array('name'=>'name', 'state_id'=>'state->translatedName');
        $icons = array();
        $icons[] = array('src'=>Kohana::config('toucan.images_directory').'/information.png', 'action'=>'interviewSession/show/', 'text'=>'interviewSession.info');
        $icons[] = array('src'=>Kohana::config('toucan.images_directory').'/copies.png', 'action'=>'interviewSession/copies/', 'text'=>'interviewSession.copies');
        $this->template->content=new View('data/list');
        $this->template->content->listUrl = List_Controller::initList($this->user, access::ANYBODY,"interviewSession","interviewSession/show/", $fields, array('evaluation_id'=>$evaluationId), false, $icons);
        $this->template->content->dataName = "interviewSession";

        $this->setPageInfo('INTERVIEWS');
        $this->setHeaders('INTERVIEWS');

        // Set default sorting to field "state_id"
        $filter = ListFilter::instance();
        $filter->setDefaultSorting("state_id");
        $this->template->content->sortingName = $filter->getSortingName();
        $this->template->content->sortingOrder = $filter->getSortingOrderInt();
        $this->template->content->listIcons = 2;

        // SEARCH
        $search = array();
        $search[0] = array('text'=>'session.name', 'name'=>'name');
        $this->template->content->showSearch = $filter->fillSearchFields($search);
        $this->template->content->search = $search;

        if ($this->data->isEditableBy($this->user, false) && ($this->data->isOver())) {
            $this->template->content->info = Kohana::lang('evaluation.closed');
        }

    }

    public function delete($id) {
        // LOAD DATA
        $this->loadData($id);
        $activityId = $this->data->activity_id;
        parent::delete($id, urlencode("activity/evaluations/$activityId"));
    }

    public function showAll($category = null) {
        $constraints = null;
        if (!isset($category)) {
            // show all evaluations
            $fields = array('name'=>'name','activity'=>'activity->name', 'state_id'=>'state->translatedName');
        } else {
            if ($category == 1) {
                // Evaluations going on
                $constraints = array('state_id'=>EvaluationState_Model::GOING_ON);
                $fields = array('name'=>'name','activity'=>'activity->name');
                $this->context['goingOn'] = 1;
            } else {
                // Evaluations published
                $constraints = array('state_id'=>EvaluationState_Model::OVER);
                $fields = array('name'=>'name','activity'=>'activity->name');
                $this->context['published'] = 1;
            }
        }
        parent::showAll($fields, access::ANYBODY, null, $constraints);

        // Deal with search
        $filter = ListFilter::instance();
        $search = array();
        $search[0] = array('text'=>'evaluation.name', 'name'=>'name');
        $this->template->content->showSearch = $filter->fillSearchFields($search);
        $this->template->content->search = $search;

        // Set default filter to field "name"
        $filter->setDefaultSorting("name");
        $this->template->content->sortingName = $filter->getSortingName();
        $this->template->content->sortingOrder = $filter->getSortingOrderInt();
    }

    public function indicators($evaluationId, $categoryId = null) {
        // LOAD DATA
        $this->loadData($evaluationId);

        // CONTROL ACCESS
        $this->controlAccess('INDICATORS');
        
        // Get categories if any
        $categories = $this->data->getDisplayableCategories($this->user);
        
        if (count($categories)>0) {
            // In case no category is provided, try to retrieve last category used from session
            if (!isset($categoryId)) {
                $categoryId = $this->session->get('LAST_CATEGORY_EVALUATION_'.$evaluationId, null);
            }
            if (isset($categoryId)) {
                // check that $categoryId exists
                if (!isset($categories[$categoryId]))
                    $categoryId = null;
            }
            if (!isset($categoryId)) {
                //  lastly, take first category available
                $categoryId = key($categories);
            }
        } else {
            $categoryId = null;
        }
        
        $indicatorIds = $this->data->getIndicatorIds($this->user, $categoryId);
        
        $indicatorsCount = count($indicatorIds);
        
        $this->template->content=new View('indicator/view_items');

        if ($indicatorsCount==0) {
            $this->template->content->noItems = "indicator.no_item";
        } else {
            if (isset($categoryId)) {
                $this->template->content->fetchUrl= "axIndicator/fetch/$evaluationId/$categoryId";
                $sessionPrefix = "FETCH_evaluation_{$evaluationId}_{$categoryId}";
            } else {
                $this->template->content->fetchUrl= "axIndicator/fetch/$evaluationId";
                $sessionPrefix = "FETCH_evaluation_{$evaluationId}";
            }
            
            $this->session->set_flash($sessionPrefix."_ids",$indicatorIds);
            $this->session->set_flash($sessionPrefix."_current",0);
            $this->session->set_flash($sessionPrefix."_draggable",$this->testAccess(access::MAY_EDIT));
        }
     
        $this->template->content->mayEdit = $this->testAccess(access::MAY_EDIT);
        $this->template->content->isDraggable = $this->testAccess(access::MAY_EDIT);
        $this->template->content->displayUrl = "indicator/show/";
        $this->template->content->deleteUrl = "axIndicator/delete/";
        if (isset($categoryId)) 
            $this->template->content->reorderUrl = "axIndicator/reorder/$categoryId/1";
        else
            $this->template->content->reorderUrl = "axIndicator/reorder/$evaluationId";
        $this->template->content->confirmDeletion = "indicator.delete_confirm";
        $this->template->content->alreadyEditing = "indicator.already_editing";
        $this->template->content->showContent = true;

        if (count($categories)>0) {
            $this->template->content->header = new View('indicator/categories');
            $this->template->content->header->categories = $categories;
            $this->template->content->header->selectedCategory = $categoryId;
            $this->template->content->header->updateUrl = "evaluation/indicators/$evaluationId";
            if ($this->testAccess(access::MAY_EDIT)) {
                $this->template->content->header->showUrl = "category/show/";
            }
            // save category in session
            $this->session->set('LAST_CATEGORY_EVALUATION_'.$evaluationId, $categoryId);
        }
        
        $this->setPageInfo('INDICATORS');
    }
    
    public function categories($evaluationId) {
        $this->loadData($evaluationId);

        $this->controlAccess('CATEGORIES');

        $categories = $this->data->getDisplayableCategories($this->user);
        
        $this->template->content=new View('data/view_items');
        
        $this->template->content->items = $categories;
        if (sizeof($categories)==0) {
            $this->template->content->noItems = "category.no_item";
        }

        $this->template->content->mayEdit = $this->testAccess(access::MAY_EDIT);
        $this->template->content->isDraggable = $this->testAccess(access::MAY_EDIT);
        $this->template->content->displayUrl = "category/show/";
        $this->template->content->deleteUrl = "axCategory/delete/";
        $this->template->content->reorderUrl = "axCategory/reorder/$evaluationId";
        $this->template->content->confirmDeletion = "category.delete_confirm";
        $this->template->content->alreadyEditing = "category.already_editing";
        $this->template->content->showContent = true;

        $this->setPageInfo('CATEGORIES');
    }


    public function export($evaluationId) {
        // LOAD DATA
        $this->loadData($evaluationId);

        // CONTROL ACCESS
        $this->controlAccess('EXPORT');

        $this->data->exportIndicators();

        $this->auto_render = false;
    }

    public function setState($id, $stateId) {
        $this->loadData($id);
        $this->controlAccess('SET_STATE');
        $this->data->state_id = $stateId;
        $this->data->save();
        $state = ORM::factory('EvaluationState', $stateId);
        $this->setMessage(sprintf(Kohana::lang('evaluation.state_updated'), $state->getTranslatedName()));
        $this->show($id);
    }
    
    protected function setHeaders($action) {
        $headers = array();
        switch ($action) {
            case 'OWNER' :
                $headers[0] = array('text'=>'user.firstname', 'name'=>'firstname');
                $headers[1] = array('text'=>'user.name','name'=>'name');
                $headers[2] = array('text'=>'user.username','name'=>'username');
                break;
            case 'FORMS' :
            case 'INTERVIEWS' :
                $headers[0] = array('text'=>'session.name', 'name'=>'name');
                $headers[1] = array('text'=>'session.state','name'=>'state_id');
                break;
            case 'CATEGORIES' :
                $headers[0] = array('text'=>'category.name_header', 'name'=>'name');
                $headers[1] = array('text'=>'category.description_header','name'=>'description');
                break;
            case 'SHOW_ALL' :
                $headers[0] = array('text'=>'evaluation.name', 'name'=>'name');
                $headers[1] = array('text'=>'evaluation.activity','name'=>'activity_id');
                if (!isset($this->context['published'])&&!isset($this->context['goingOn'])) {
                    $headers[2] = array('text'=>'evaluation.state','name'=>'state_id');
                }
                break;
        }
        $this->template->content->headers = $headers;
    }

    protected function setActions($action) {
        $evaluation = $this->data;
        $actions = array();
        $quickActions = array();
        $actions_back = array();
        switch ($action) {
            case 'CREATE' :
                $actions_back[] = array('type' => 'cancel','text' => 'button.cancel');
                $actions[] = array('type' => 'submit','text' => 'button.save');
                break;
            case 'EDIT' :
                $actions_back[] = array('type' => 'button','text' => 'button.cancel','url' => 'evaluation/show/'.$evaluation->id);
                $actions[] = array('type' => 'submit','text' => 'button.save');
                break;
            case 'SHOW_ALL' :
                if ($this->testAccess()) {
                    $actions[] = array('type' => 'button','url' => 'evaluation/create','text' => 'evaluation.create');
                }
                $quickActions[] = array('image' => Kohana::config('toucan.images_directory').'/all.png','text' => Kohana::lang('evaluation.show_all_all'),'url' => 'evaluation/showAll');
                $quickActions[] = array('image' => Kohana::config('toucan.images_directory').'/going_on.png','text' => Kohana::lang('evaluation.show_all_going_on'),'url' => 'evaluation/showAll/1');
                $quickActions[] = array('image' => Kohana::config('toucan.images_directory').'/over.png','text' => Kohana::lang('evaluation.show_all_over'),'url' => 'evaluation/showAll/2');
                break;
            case 'SHOW' :
                if ($this->testAccess(access::MAY_EDIT)) {
                    $actions[] = array('type' => 'button','text' => 'button.edit','url' => 'evaluation/edit/'.$evaluation->id);
                    $quickActions[] = array('image' => Kohana::config('toucan.images_directory').'/under_construction.png','text' => Kohana::lang('evaluation.set_state_under_construction'),'url' => 'evaluation/setState/'.$evaluation->id.'/'.EvaluationState_Model::UNDER_CONSTRUCTION);
                    $quickActions[] = array('image' => Kohana::config('toucan.images_directory').'/going_on.png','text' => Kohana::lang('evaluation.set_state_going_on'),'url' => 'evaluation/setState/'.$evaluation->id.'/'.EvaluationState_Model::GOING_ON);
                    $quickActions[] = array('image' => Kohana::config('toucan.images_directory').'/under_analyse.png','text' => Kohana::lang('evaluation.set_state_under_analyse'),'url' => 'evaluation/setState/'.$evaluation->id.'/'.EvaluationState_Model::UNDER_ANALYSE);
                    $quickActions[] = array('image' => Kohana::config('toucan.images_directory').'/over.png','text' => Kohana::lang('evaluation.set_state_over'),'url' => 'evaluation/setState/'.$evaluation->id.'/'.EvaluationState_Model::OVER);
                    $quickActions[] = array('image' => Kohana::config('toucan.images_directory').'/cancelled.png','text' => Kohana::lang('evaluation.set_state_cancelled'),'url' => 'evaluation/setState/'.$evaluation->id.'/'.EvaluationState_Model::CANCELLED);
                }
                if ($this->testAccess(access::OWNER)) {
                    $actions[] = array('type' => 'button_confirm','text' => 'button.delete','confirm' => 'evaluation.delete_text','url' => 'evaluation/delete/'.$evaluation->id);
                }
                if ($this->testAdminAccess()) {
                    $actions[] = array('type' => 'button','text' => 'evaluation.set_owner','url' => 'evaluation/owner/'.$evaluation->id);
                }
                break;
            case 'OWNER' :
                $actions_back[] = array('type' => 'cancel','text' => 'button.cancel');
                break;
            case 'SELECT_ACTIVITY' :
                $actions_back[] = array('type' => 'cancel','text' => 'button.cancel');
                break;
            case 'FORMS' :
                if ($this->testAccess(access::MAY_EDIT)&&!$evaluation->isOver()) {
                    $actions[] = array('type' => 'button','text' => 'evaluation.add_form_session','url' => 'formSession/createStart/'.$evaluation->id);
                }
                break;
            case 'INTERVIEWS' :
                if ($this->testAccess(access::MAY_EDIT)&&!$evaluation->isOver()) {
                    $actions[] = array('type' => 'button','text' => 'evaluation.add_interview_session','url' => 'interviewSession/createStart/'.$evaluation->id);
                }
                break;
            case 'INDICATORS' :
                if ($this->testAccess(access::MAY_EDIT)) {
                    $actions_back[] = array('type' => 'button','text' => 'evaluation.categories','url' => 'evaluation/categories/'.$evaluation->id);
                    $actions[] = array('type' => 'button','text' => 'evaluation.add_indicator','url' => 'indicator/createStart/'.$evaluation->id);
                    $actions[] = array('type' => 'button','text' => 'evaluation.export','url' => 'evaluation/export/'.$evaluation->id);
                }
                break;
            case 'CATEGORIES' :
                $actions_back[] = array('type' => 'button','text' => 'evaluation.indicators','url' => 'evaluation/indicators/'.$evaluation->id);
                if ($this->testAccess(access::MAY_EDIT)) {
                    $actions[] = array('type' => 'button','url' => 'category/create/'.$evaluation->id,'text' => 'evaluation.add_category');
                }
                break;
        }
        $tabs = array();
        // TODO: moduler l'apparition des tabs en fonction du profil de l'utilisateur
        if ($action == 'CREATE') {
            $activity = $this->getActivity();
            $tabs[] = array('text'=>'activity.info', 'link' => 'activity/show/'.$activity->id, 'image'=>Kohana::config('toucan.images_directory')."/information.png");
            $tabs[] = array('text'=>'activity.evaluations', 'link' => 'activity/evaluations/'.$activity->id, 'image'=>Kohana::config('toucan.images_directory')."/evaluation.png", 'current'=>1);
            $tabs[] = array('text'=>'activity.surveys', 'link' => 'activity/surveys/'.$activity->id, 'image'=>Kohana::config('toucan.images_directory')."/survey.png");
            $tabs[] = array('text'=>'activity.children', 'link' => 'activity/showAll/'.$activity->id, 'image'=>Kohana::config('toucan.images_directory')."/activity.png");
        } else if ($action != 'SHOW_ALL'&&$action != 'SELECT_ACTIVITY') {
            $tabs[] = array('text'=>'evaluation.info', 'link' => 'evaluation/show/'.$evaluation->id, 'image' => Kohana::config('toucan.images_directory')."/information.png");
            $tabs[] = array('text'=>'evaluation.forms', 'link' => 'evaluation/forms/'.$evaluation->id, 'image' => Kohana::config('toucan.images_directory')."/formSession.png");
            $tabs[] = array('text'=>'evaluation.interviews', 'link' => 'evaluation/interviews/'.$evaluation->id, 'image' => Kohana::config('toucan.images_directory')."/interviewSession.png");
            $tabs[] = array('text'=>'evaluation.indicators', 'link' => 'evaluation/indicators/'.$evaluation->id, 'image' => Kohana::config('toucan.images_directory')."/indicator.png");
        
            switch($action) {
                case 'EDIT' :
                case 'SHOW' :
                case 'OWNER' :
                    $tabs[0]['current'] = 1;
                    break;
                case 'FORMS' :
                    $tabs[1]['current'] = 1;
                    break;
                case 'INTERVIEWS' :
                    $tabs[2]['current'] = 1;
                    break;
                case 'INDICATORS':
                case 'CATEGORIES':
                    $tabs[3]['current'] = 1;
                    break;
            }
        }
        $this->template->content->actions = $actions;
        $this->template->content->quickActions = $quickActions;
        $this->template->content->actions_back = $actions_back;
        $this->template->content->tabs = $tabs;
    }

    protected function setPath($action) {
        $path = array();
        $activity = $this->getActivity();
        if (isset($activity)) {
            $path[] = array('text'=>sprintf(Kohana::lang('activity.main_title', $activity->name)), 'link'=>"activity/evaluations/$activity->id");
        }
        $this->template->content->path = $path;
    }

    protected function setDescription($action) {
        $evaluation = $this->data;
        parent::setDescription($action);
        if (($action != 'SHOW_ALL')&&($action != 'CREATE')&&($action != 'SELECT_ACTIVITY')) {
            $this->template->content->title = sprintf(Kohana::lang('evaluation.main_title', $evaluation->name));
        } else if ($action == 'SHOW_ALL') {
            if (isset($this->context['published'])) {
                $this->template->content->description = Kohana::lang('evaluation.show_all_description_over');
            } else if (isset($this->context['goingOn'])) {
                $this->template->content->description = Kohana::lang('evaluation.show_all_description_going_on');
            }
        } else if ($action == 'SELECT_ACTIVITY') {
                $this->template->content->title = Kohana::lang('evaluation.select_activity_title');
                $this->helpTopic="selectActivity";
        } else {
            $this->template->content->title = null;
        }
        $activity = $this->getActivity();
        if (isset($activity)&&$activity->logo_id >0)
            $this->template->content->title_logo = $activity->logo->path;
        $this->template->content->pathType = "path_activity";
    }

    protected function createConditions() {
        $conditional[] = array('trigger'=>'inherit', 'triggered'=>'inherit_view');
        $conditional[] = array('trigger'=>'inherit', 'triggered'=>'inherit_edit');
        $conditional[] = array('trigger'=>'inherit', 'triggered'=>'view_id','reverse'=>true);
        $conditional[] = array('trigger'=>'inherit', 'triggered'=>'edit_id','reverse'=>true);
        $this->template->content->conditional = $conditional;
    }

    protected function getActivity() {
        if (isset ($this->data)) {
            return $this->data->activity;
        } else if (isset ($this->context['activityId'])) {
            return ORM::factory('activity', $this->context['activityId']);
        }
        return null;
    }

}
?>