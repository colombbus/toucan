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

class SurveyIndicator_Controller extends Indicator_Controller {
    
    protected $dataName = "surveyIndicator";
    protected $parentName = "survey";
    protected $parentIdName = "session_id";
    protected $parentIdField = "surveyId";
    protected $controllerName = "surveyIndicator";
    protected $parentControllerName = "survey";

    protected function setPath($action) {
        $path = array();
        $parent = $this->getParent();
        if (isset($parent)) {
            $activity = $this->getActivity();
            $path[] = array('text'=>sprintf(Kohana::lang("activity.main_title"), $activity->name), 'link'=>"activity/surveys/$activity->id");
            $path[] = array('text'=>sprintf(Kohana::lang($this->parentControllerName.".main_title"), $parent->name), 'link'=>$this->parentControllerName."/indicators/$parent->id");
        }
        $this->template->content->path = $path;

    }
    
    protected function createConditions($fullTriggers = true) {
        $conditional = array();
        if ($fullTriggers) {
            $conditional[] = array('trigger'=>'type', 'triggered'=>'contribute_id','value'=>Indicator_Model::TYPE_MANUAL);
        }
        $conditional[] = array('trigger'=>'inherit', 'triggered'=>'inherit_view');
        $conditional[] = array('trigger'=>'inherit', 'triggered'=>'inherit_edit');
        $conditional[] = array('trigger'=>'inherit', 'triggered'=>'view_id','reverse'=>true);
        $conditional[] = array('trigger'=>'inherit', 'triggered'=>'edit_id','reverse'=>true);
        $this->template->content->conditional = $conditional;
    }
    
}
?>