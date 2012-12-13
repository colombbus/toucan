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

class TemplateIndicator_Controller extends Indicator_Controller {
    
    protected $dataName = "templateIndicator";
    protected $parentName = "template";
    protected $parentIdName = "template_id";
    protected $parentIdField = "templateId";
    protected $controllerName = "templateIndicator";
    protected $parentControllerName = "formTemplate";


    
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
    
    protected function setPath($action) {
        $path = array();
        $template = $this->getParent();
        $path[] = array('text'=>sprintf(Kohana::lang($this->parentControllerName.".main_title", $template->name)),'link'=>$this->parentControllerName.'/indicators/'.$template);
        $this->template->content->path = $path;
        $this->template->content->pathType = "path_template";
    }

    protected function setActions($action) {
        parent::setActions($action);
        if ($action == 'CALCULATION' || $action == 'GRAPHIC') {
            unset ($this->template->content->actions[1]);
        }
        if (isset ($this->data)&&substr($action, 0, 6) != 'CREATE' && $this->data->type == Indicator_Model::TYPE_MANUAL && $this->testAccess(Access::MAY_CONTRIBUTE) ) {
            unset ($this->template->content->tabs[2]);
        }
    }

    protected function setDescription($action) {
        $template = $this->getParent();
        parent::setDescription($action);
    }
    
    protected function getActivity() {
        return null;
    }

    protected function getParent() {
        if (isset ($this->data)) {
            $parentName = $this->parentName;
            return $this->data->$parentName;
        } else if (isset ($this->context[$this->parentIdField])) {
            return Template_Model::getTemplate($this->context[$this->parentIdField]);
        }
        return null;
    }

}
?>