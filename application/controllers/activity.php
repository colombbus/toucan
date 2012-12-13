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

class Activity_Controller extends DataPage_Controller {

    protected $dataName = "activity";

    protected function controlAccess($action) {
        switch ($action) {
            case 'CREATE' :
                $this->ensureAccess();
                break;
            case 'SHOW_ALL' :
                // public access
                break;
            case 'SHOW' :
            case 'EVALUATIONS' :
            case 'SURVEYS' :
            case 'SHOW_ALL_CHILDREN' :
                $this->ensureAccess(access::MAY_VIEW);
                break;
            case 'EDIT' :
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


    public function show($id) {
        parent::show($id);
        if ($this->testAccess(access::OWNER)&&$this->data->hasActiveEvaluations()) {
            $this->template->content->info = Kohana::lang('activity.not_deleteable');
        }
    }

    public function showAll($parentId=0, $action = 'activity/show/') {
        if ($parentId>0) {
            $this->loadData($parentId);
            $this->controlAccess('SHOW_ALL_CHILDREN');
        } else {
            $this->controlAccess('SHOW_ALL');
        }

        // Set default filter to field "name"
        $filter = Filter::instance();
        $filter->setSorting("name");

        $model = ORM::factory('activity');
        $activities = $model->getItems($filter, $parentId);
        $displayableActivities = array();
        foreach ($activities as $activity) {
            if ($this->testAccess(access::MAY_VIEW,$activity))
                $displayableActivities[] = $this->recHandleActivity($activity, $action);
        }

        $this->template->content=new View('activity/list');
        if (sizeof($displayableActivities)==0) {
            if ($parentId == 0)
                $this->template->content->noItems = 'activity.no_items';
            else
                $this->template->content->noItems = 'activity.no_subitems';
        }
        $this->template->content->items = $displayableActivities;
        $this->template->content->itemIcon = Kohana::config('toucan.images_directory')."/activity.png";
        if ($parentId>0) {
            $this->setPageInfo('SHOW_ALL_CHILDREN');
            $this->setHeaders('SHOW_ALL_CHILDREN');
        } else {
            $this->setPageInfo('SHOW_ALL');
            $this->setHeaders('SHOW_ALL');
        }
    }


    protected function recHandleActivity($activity, $action, $level=0, $admin = false){
        $item = array();
        if ($admin) {
            $item['info'] = array('name' => $activity->name, 'owner' => $activity->owner->firstname." ".$activity->owner->name);
        } else {
            $item['info'] = array('name' => $activity->name);
        }
        $item['link'] = $action.$activity->id;
        $children = $activity->children->as_array();
        if (sizeof($children)>0) {
            foreach($children as $child) {
                if ($this->testAccess(access::MAY_VIEW,$child))
                    $item['sub_items'][] = $this->recHandleActivity($child, $action, $level+1);
            }
        }
        return $item;
    }

    public function create($parentId = null) {
        if (isset($parentId)) {
            parent::create(array('parentId'=>$parentId));
        } else {
            parent::create();
        }
    }

    public function evaluations($activityId) {
        // CONTROL ACCESS
        $this->loadData($activityId);
        $access = $this->controlAccess('EVALUATIONS');

        // TEMPLATE
        $this->template->content=new View('data/list');

        $fields = array('name'=>'name', 'start_date'=>'start_date', 'end_date'=>'end_date', 'state_id'=>'state->translatedName');
        $icons = array();
        $icons[] = array('src'=>Kohana::config('toucan.images_directory').'/information.png', 'action'=>'evaluation/show/', 'text'=>'evaluation.info');
        $icons[] = array('src'=>Kohana::config('toucan.images_directory').'/formSession.png', 'action'=>'evaluation/forms/', 'text'=>'evaluation.forms');
        $icons[] = array('src'=>Kohana::config('toucan.images_directory').'/interviewSession.png', 'action'=>'evaluation/interviews/', 'text'=>'evaluation.interviews');
        $icons[] = array('src'=>Kohana::config('toucan.images_directory').'/indicator.png', 'action'=>'evaluation/indicators/', 'text'=>'evaluation.indicators');

        $access = access::ANYBODY;

        $action = "evaluation/show/";

        $this->template->content->listUrl = List_Controller::initList($this->user, $access,"evaluation",$action, $fields, $activityId, false, $icons);
        $this->template->content->dataName = "evaluation";
        $this->template->content->listIcons = 4;

        // FILTER
        $filter = ListFilter::instance();

        // Deal with search
        $search = array();
        $search[0] = array('text'=>'evaluation.name', 'name'=>'name');
        $this->template->content->showSearch = $filter->fillSearchFields($search);
        $this->template->content->search = $search;

        // Set default sorting to state_id
        $filter->setDefaultSorting('state_id');
        $this->template->content->sortingName = $filter->getSortingName();
        $this->template->content->sortingOrder = $filter->getSortingOrderInt();

        // PAGE INFOS
        $this->setPageInfo('EVALUATIONS');

        // HEADERS
        $this->setHeaders('EVALUATIONS');

    }

    public function surveys($activityId) {
        // CONTROL ACCESS
        $this->loadData($activityId);
        $access = $this->controlAccess('SURVEYS');

        // TEMPLATE
        $this->template->content=new View('data/list');

        $fields = array('name'=>'name', 'state_id'=>'state->translatedName');
        $icons = array();
        $icons[] = array('src'=>Kohana::config('toucan.images_directory').'/information.png', 'action'=>'survey/show/', 'text'=>'survey.info');
        $icons[] = array('src'=>Kohana::config('toucan.images_directory').'/copies.png', 'action'=>'survey/copies/', 'text'=>'survey.copies');
        $icons[] = array('src'=>Kohana::config('toucan.images_directory').'/indicator.png', 'action'=>'survey/indicators/', 'text'=>'survey.indicators');

        $access = access::ANYBODY;

        $action = "survey/show/";

        $this->template->content->listUrl = List_Controller::initList($this->user, $access,"survey",$action, $fields, array('activity_id'=>$activityId), false, $icons);
        $this->template->content->dataName = "survey";
        $this->template->content->listIcons = 3;

        // FILTER
        $filter = ListFilter::instance();

        // Deal with search
        $search = array();
        $search[0] = array('text'=>'survey.name', 'name'=>'name');
        $this->template->content->showSearch = $filter->fillSearchFields($search);
        $this->template->content->search = $search;

        // Set default sorting to state_id
        $filter->setDefaultSorting('state_id');
        $this->template->content->sortingName = $filter->getSortingName();
        $this->template->content->sortingOrder = $filter->getSortingOrderInt();

        // PAGE INFOS
        $this->setPageInfo('SURVEYS');

        // HEADERS
        $this->setHeaders('SURVEYS');

    }

    
    protected function setActions($action) {
        $activity = $this->data;
        $actions = array();
        $actions_back = array();
        switch ($action) {
            case 'SHOW_ALL' :
                if ($this->testAccess()) {
                    $actions[] = array('type' => 'button','url' => 'activity/create','text' => 'activity.create');
                }
                break;
            case 'SHOW_ALL_CHILDREN' :
                $actions[] = array('type' => 'button','url' => 'activity/create/'.$activity->id,'text' => 'activity.createChildren');
                break;
            case 'SHOW' :
                //$actions[] = array('type' => 'button','url' => 'activity/evaluations/'.$activity->id,'text' => 'activity.evaluations');
                if ($this->testAccess(access::MAY_EDIT)) {
                    $actions[] = array('type' => 'button','text' => 'button.edit','url' => 'activity/edit/'.$activity->id);
                }
                if ($this->testAccess(access::OWNER)&&!$activity->hasActiveEvaluations()) {
                    $actions[] = array('type' => 'button_confirm','text' => 'button.delete','confirm' => 'activity.delete_text','url' => 'activity/delete/'.$activity->id);
                }
                if ($this->testAdminAccess()) {
                    $actions[] = array('type' => 'button','text' => 'activity.set_owner','url' => 'activity/owner/'.$activity->id);
                }
                break;
            case 'EDIT' :
                $actions_back[] = array('type' => 'button','text' => 'button.cancel','url' => 'activity/show/'.$activity->id);
                $actions[] = array('type' => 'submit','text' => 'button.save');
                break;
            case 'CREATE' :
                $actions_back[] = array('type' => 'cancel','text' => 'button.cancel');
                $actions[] = array('type' => 'submit','text' => 'button.save');
                break;
            case 'OWNER' :
                $actions_back[] = array('type' => 'cancel','text' => 'button.cancel');
                break;
            case 'EVALUATIONS' :
                if ($this->testAccess(access::MAY_EDIT)) {
                    $actions[] = array('type' => 'button','text' => 'activity.add_evaluation','url' => 'evaluation/create/'.$activity->id);
                }
                break;
            case 'SURVEYS' :
                if ($this->testAccess(access::MAY_EDIT)) {
                    $actions[] = array('type' => 'button','text' => 'activity.add_survey','url' => 'survey/createStart/'.$activity->id);
                }
                break;
        }
        $tabs = array();
        if (($action != 'SHOW_ALL')&&($action!='CREATE')) {
            $tabs[] = array('text'=>'activity.info', 'link' => 'activity/show/'.$activity->id, 'image'=>Kohana::config('toucan.images_directory')."/information.png");
            $tabs[] = array('text'=>'activity.evaluations', 'link' => 'activity/evaluations/'.$activity->id, 'image'=>Kohana::config('toucan.images_directory')."/evaluation.png");
            $tabs[] = array('text'=>'activity.surveys', 'link' => 'activity/surveys/'.$activity->id, 'image'=>Kohana::config('toucan.images_directory')."/survey.png");
            $tabs[] = array('text'=>'activity.children', 'link' => 'activity/showAll/'.$activity->id, 'image'=>Kohana::config('toucan.images_directory')."/activity.png");
            switch($action) {
                case 'SHOW' :
                case 'EDIT' :
                case 'OWNER' :
                    $tabs[0]['current'] = 1;
                    break;
                case 'EVALUATIONS' :
                    $tabs[1]['current'] = 1;
                    break;
                case 'SURVEYS' :
                    $tabs[2]['current'] = 1;
                    break;
                case 'SHOW_ALL_CHILDREN' :
                    $tabs[3]['current'] = 1;
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
                $headers[0] = array('name'=>'name', 'text'=>'activity.name');
                $headers[1] = array('text'=>'activity.description');
                if ($this->testAdminAccess()) {
                    $headers[2] = array('text'=>'activity.owner');
                }
                break;
            case 'OWNER' :
                $headers[0] = array('text'=>'user.firstname', 'name'=>'firstname');
                $headers[1] = array('text'=>'user.name','name'=>'name','default'=>1);
                $headers[2] = array('text'=>'user.username','name'=>'username');
                break;
            case 'EVALUATIONS' :
                $headers[0] = array('text'=>'evaluation.name', 'name'=>'name');
                $headers[1] = array('text'=>'evaluation.start_date','name'=>'start_date');
                $headers[2] = array('text'=>'evaluation.end_date','name'=>'end_date');
                $headers[3] = array('text'=>'evaluation.state','name'=>'state_id','default'=>1);
                break;
            case 'SURVEYS' :
                $headers[0] = array('text'=>'survey.name', 'name'=>'name');
                $headers[1] = array('text'=>'survey.state','name'=>'state_id','default'=>1);
                break;
        }
        $this->template->content->headers = $headers;
    }

    protected function setPath($action) {
        $activity = $this->data;
        $path = array();
        $this->template->content->path = $path;
        $this->template->content->pathType = "path_activity";
    }

    protected function setDescription($action) {
        $activity = $this->data;
        parent::setDescription($action);
        if (($action != 'SHOW_ALL')&&($action != 'CREATE')) {
            $this->template->content->title = sprintf(Kohana::lang('activity.main_title', $activity->name));
            if ($activity->logo_id >0)
                $this->template->content->title_logo = $activity->logo->path;
        }

    }
}
?>