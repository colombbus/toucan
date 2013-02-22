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

class SurveyCategory_Controller extends Category_Controller {
    

    protected $dataName = "surveyCategory";
    protected $parentControllerName = "survey";
    protected $parentName = "survey";
    protected $parentIdField = "surveyId";
    protected $parentIdName = "session_id";
    protected $controllerName = "surveyCategory";
    protected $indicatorControllerName = "surveyIndicator";
    
    protected function setPath($action) {
        $path = array();
        $parent = $this->getParent();
        if (isset($parent)) {
            $activity = $parent->activity;
            $path[] = array('text'=>sprintf(Kohana::lang("activity.main_title"), $activity->name), 'link'=>"activity/surveys/$activity->id");
            $path[] = array('text'=>sprintf(Kohana::lang($this->parentControllerName.".main_title"), $parent->name), 'link'=>$this->parentControllerName."/categories/$parent->id");
        }
        $this->template->content->path = $path;

    }

}
?>