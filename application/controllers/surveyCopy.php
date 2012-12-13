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

class SurveyCopy_Controller extends FormCopy_Controller {

    protected $dataName = "surveyCopy";
    protected $sessionName = "survey";
    protected $controllerName = "surveys";
    protected $publicControllerName = "publicSurvey";


    protected function setPath($action) {
        $path = array();
        $activity = $this->getActivity();
        if (isset($activity)) {
            $path[] = array('text'=>sprintf(Kohana::lang('activity.main_title', $activity->name)), 'link'=>"activity/surveys/$activity->id");
        }
        $this->template->content->path = $path;
    }

    protected function setDescription($action) {
        $copy = $this->data;
        parent::setDescription($action);
        $session = $this->getSession();
        $this->template->content->title = sprintf(Kohana::lang($this->sessionName.'.main_title', $session->name));
        $activity = $this->getActivity();
        if (isset($activity)&&$activity->logo_id >0) {
            $this->template->content->title_logo = $activity->logo->path;
        }
        $this->template->content->title_icon=null;
    }
    
    protected function getActivity() {
        $session = $this->getSession();
        if (isset ($session))
            return $session->activity;
        return null;
    }

    protected function getEvaluation() {
        return null;
    }

    protected function setActions($action) {
        parent::setActions($action);
        $survey = $this->getSession();
        $this->template->content->tabs[] = array('text'=>'survey.indicators', 'link' => 'survey/indicators/'.$survey->id, 'image'=>Kohana::config('toucan.images_directory')."/indicator.png");
    }
    
}
?>