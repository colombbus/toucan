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

class TemplateIndicator_Model extends Indicator_Model implements Ajax_Model {

    protected $table_name = "indicators";
    protected $accessParent = "template";
    protected $controllerName = "templateIndicator";
    protected $parentName = "template";
    protected $parentId = "template_id";
    protected $displaySessionLink = false;

    public function getItemActions(& $user) {
        $itemActions = array();
        $itemActions[] = array('link'=>$this->controllerName."/show/", 'text'=>"indicator.details");
        if ($this->isEditableBy($user)) {
            $itemActions[] = array('function'=>"deleteItem", 'text'=>"indicator.delete");
        }
        return $itemActions;
    }

    protected function computeGraphic() {   
        return null;
    }

    public function getValue() {
        return array();
    }
    
    protected function getSessions() {
        return array($this->template);
    }

}
?>