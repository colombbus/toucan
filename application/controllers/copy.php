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

abstract class Copy_Controller extends DataPage_Controller {
    protected $context=array();
    protected $sessionName = null;
    protected $controllerName = null;
    protected $publicControllerName = null;


    protected function controlAccess($action) {
        // Special case where user is not registered, cannot view, but session is open to public
        if ($this->testAccess(access::MAY_CONTRIBUTE, $this->getSession())&&(!$this->testAccess()&&!$this->getSession()->isViewableBy($this->user))) {
            url::redirect("$this->publicControllerName/form/".$this->getSession()->id);
        }
        switch ($action) {
            case 'CREATE' :
                $this->ensureAccess(access::MAY_CONTRIBUTE, $this->getSession());
                break;
            case 'SHOW' :
            case 'DOWNLOAD' :
                $this->ensureAccess(access::MAY_VIEW);
                break;
            case 'EDIT' :
            case 'DELETE' :
            case 'SET_STATE' :
                $this->ensureAccess(access::MAY_EDIT);
                if (!$this->data->isEditable()) {
                    $this->displayError('restricted_access');
                }
                break;
            default : // in case other cases not covered
                $this->ensureAccess(access::ADMIN);
                break;
        }
    }

    public function show($id) {
        parent::show($id, 'copy/view');
        $idPrevious = $this->data->getPreviousId();
        $idNext = $this->data->getNextId();
        if (isset($idPrevious)) {
            $this->template->content->previous = array('link'=>"$this->dataName/show/$idPrevious",'label'=>"$this->dataName.previous_copy");
        }
        if (isset($idNext)) {
            $this->template->content->next = array('link'=>"$this->dataName/show/$idNext",'label'=>"$this->dataName.next_copy");
        }
        if ($this->data->isEditableBy($this->user, false) && !($this->data->isEditable())) {
            if ($this->data->session->isEditable())
                // Not editable because session is over
                $this->template->content->info = Kohana::lang('copy.not_editable');
            else
                // Not editable because evaluation is over
                $this->template->content->info = Kohana::lang('copy.not_editable_2');
        }
    }

    public function create($sessionId) {
        $this->context['session_id'] = $sessionId;
        if (!$this->getSession()->isOpen())
        {
            $this->displayError('session_closed');
        }
        parent::create(array('session_id'=>$sessionId, 'url_next'=>"$this->dataName/stop/$sessionId/", "url_next_with_id"=>1),'copy/edit');
        $this->template->content->goingOnState = CopyState_Model::GOING_ON;
        if ($this->testAccess()) {
            // If user is logged, activate automatic save mechanism
            $this->template->content->automaticSaveUrl = "axCopy/save/$this->sessionName/$sessionId";
        }
    }

    public function edit($id) {
        $this->loadData($id);
        parent::edit($id, 'copy/edit', array('url_next'=>"$this->dataName/stop/".$this->getSession()->id."/", 'url_next_with_id'=>1));
        $this->template->content->goingOnState = CopyState_Model::GOING_ON;
        if ($this->testAccess()) {
            // If user is logged, activate automatic save mechanism
            $session = $this->getSession();
            $this->template->content->automaticSaveUrl = "axCopy/save/$this->sessionName/$session->id";
        }
    }

    public function delete($id) {
        // LOAD DATA
        $this->loadData($id);
        parent::delete($id, $this->sessionName.'/copies/'.$this->data->session->id);
    }

    public function stop($sessionId, $copyId = null) {
        if ($this->testAccess()) {
            // erase any automatically saved copy
            $session = ORM::factory($this->sessionName, $sessionId);
            if (isset($session)&& $session->loaded) {
                $session->clearAutomaticSave($this->user);
            }
        }
        // In case there is a message, keep it for next page
        $this->keepMessage();
        if (isset($copyId)) {
            // redirect to show
            url::redirect("$this->dataName/show/$copyId");
        } else {
            // redirect to session/showAll
            url::redirect("$this->sessionName/copies/$sessionId");
        }
    }

    public function download($id) {
        $this->loadData($id);
        // CONTROL ACCESS
        $this->controlAccess('DOWNLOAD');

        $this->data->exportInDocument($this->user);

        $this->auto_render = false;
    }
    
    public function setState($id, $stateId) {
        $this->loadData($id);
        // CONTROL ACCESS
        $this->controlAccess('SET_STATE');
        $this->data->state_id = $stateId;
        $this->data->save();
        $state = ORM::factory('CopyState', $stateId);
        $this->setMessage(sprintf(Kohana::lang($this->dataName.'.state_updated'), $state->getTranslatedName()));
        url::redirect("$this->dataName/show/$id");
    }
    
    protected function setHeaders($action) {

    }

    protected function setActions($action) {
        $copy = $this->data;
        $session = $this->getSession();
        $actions = array();
        $quickActions = array();
        $actions_back = array();
        switch ($action) {
            case 'CREATE' :
                $actions_back[] = array('type' => 'button','text' => 'button.cancel', 'url'=>"$this->dataName/stop/".$session->id);
                $actions[] = array('type' => 'button','js'=>'saveCopy()', 'text' => $this->dataName.'.temporary_save');
                $actions[] = array('type' => 'submit','text' => $this->dataName.'.save');
                break;
            case 'SHOW' :
                $actions_back[] = array('type' => 'button','text' => 'button.back', 'url'=>"$this->sessionName/copies/".$session->id);
                $actions[] = array('type' => 'button','text' => 'button.download','url' => $this->dataName.'/download/'.$copy->id);
                if ($copy->isEditableBy($this->user)) {
                    $actions[] = array('type' => 'button','text' => 'button.edit', 'url'=>$this->dataName.'/edit/'.$copy->id);
                    $actions[] = array('type' => 'button_confirm','text' => 'button.delete','confirm' => $this->dataName.'.delete_text','url' => $this->dataName.'/delete/'.$copy->id);
                    if ($copy->isPublished()) {
                        $quickActions[] = array('image' => Kohana::config('toucan.images_directory').'/page_white.png','text' => Kohana::lang($this->dataName.'.set_state_saved'),'url' => $this->dataName.'/setState/'.$copy->id.'/'.CopyState_Model::PUBLISHED);
                        $quickActions[] = array('image' => Kohana::config('toucan.images_directory').'/page_white_star.png','text' => Kohana::lang($this->dataName.'.set_state_marked'),'url' => $this->dataName.'/setState/'.$copy->id.'/'.CopyState_Model::MARKED);
                        $quickActions[] = array('image' => Kohana::config('toucan.images_directory').'/page_white_tick.png','text' => Kohana::lang($this->dataName.'.set_state_managed'),'url' => $this->dataName.'/setState/'.$copy->id.'/'.CopyState_Model::MANAGED);
                    }
                }
                break;
            case 'EDIT' :
                $actions_back[] = array('type' => 'button','text' => 'button.cancel', 'url'=>"$this->dataName/stop/$copy->session_id/".$this->data->id);
                if (!$copy->isPublished())
                    $actions[] = array('type' => 'button','js'=>'saveCopy()', 'text' => $this->dataName.'.temporary_save');
                $actions[] = array('type' => 'submit','text' => $this->dataName.'.save');
        }
        $tabs = array();
        $tabs[] = array('text'=>$this->sessionName.'.info', 'link' => $this->sessionName.'/show/'.$session->id, 'image'=>Kohana::config('toucan.images_directory')."/information.png");
        if ($this->testAccess(access::MAY_EDIT, $session)) {
            $tabs[] = array('text'=>$this->sessionName.'.questions', 'link' => $this->sessionName.'/questions/'.$session->id,'image'=>Kohana::config('toucan.images_directory')."/questions.png");
            $tabs[] = array('text'=>$this->sessionName.'.preview', 'link' => $this->sessionName.'/preview/'.$session->id,'image'=>Kohana::config('toucan.images_directory')."/preview.png");
        }
        $tabs[] = array('text'=>$this->sessionName.'.copies', 'link' => $this->sessionName.'/copies/'.$session->id, 'current' => 1, 'image'=>Kohana::config('toucan.images_directory')."/copies.png");
        $this->template->content->actions = $actions;
        $this->template->content->quickActions = $quickActions;
        $this->template->content->actions_back = $actions_back;
        $this->template->content->tabs = $tabs;
    }



    protected function setPath($action) {
        $path = array();
        $evaluation = $this->getEvaluation();
        if (isset($evaluation)) {
            $activity = $evaluation->activity;
            $path[] = array('text'=>sprintf(Kohana::lang('activity.main_title', $activity->name)), 'link'=>"activity/evaluations/$activity->id");
            $path[] = array('text'=>sprintf(Kohana::lang('evaluation.main_title', $evaluation->name)), 'link'=>"evaluation/".$this->controllerName."/$evaluation->id");
        }
        $this->template->content->path = $path;
    }

    protected function setDescription($action) {
        $copy = $this->data;
        parent::setDescription($action);
        $session = $this->getSession();
        $this->template->content->title = sprintf(Kohana::lang($this->sessionName.'.main_title', $session->name));
        $evaluation = $this->getEvaluation();
        if (isset($evaluation)) {
            $activity = $evaluation->activity;
            if (isset($activity)&&$activity->logo_id >0)
                $this->template->content->title_logo = $activity->logo->path;
        }
        $this->template->content->title_icon = Kohana::config("toucan.images_directory")."/$this->sessionName.png";
        $this->template->content->pathType = "path_activity";
    }

    protected function getSession() {
        if (isset($this->data)) {
            return $this->data->session;
        } else {
            $session = ORM::factory($this->sessionName,$this->context['session_id']);
            return $session;
        }
    }

    protected function getEvaluation() {
        $session = $this->getSession();
        if (isset ($session))
            return $session->evaluation;
        return null;
    }

}
?>