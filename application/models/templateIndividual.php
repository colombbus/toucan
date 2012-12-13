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

class TemplateIndividual_Model extends Individual_Model implements Ajax_Model {

    protected $belongs_to = array("templateIndicator");
    protected $has_one = array("selection","variable");
    protected $table_name = "template_individuals";
    
    protected $foreign_key = array("templateIndicator"=>"template_indicator_id");
    
    public function getEditableData($access, & $user) {
        $editableData = array();
        $variables = $this->templateIndicator->template->getVariablesList();
        $editableData[] = array('type'=>'select', 'label'=>'individual.variable', 'name'=>'variable_id', 'values'=>$variables, 'value'=>$this->variable_id, 'required'=>1);
        $selections = Selection_Model::getTranslatedList();
        $editableData[] = array('type'=>'select', 'label'=>'individual.selection', 'name'=>'selection_id', 'values'=>$selections, 'value'=>$this->selection_id, 'required'=>1, 'id'=>'selection_id');
        if ($this->selection_id == 0) {
            $hidden = !(ORM::factory('selection', 1)->requires_value);
        } else {
            $hidden = !($this->selection->requires_value);
        }
        $editableData[] = array('type'=>'text', 'label'=>'individual.value', 'name'=>'value', 'value'=>$this->value, 'hidden'=>$hidden, 'id'=>'value');
        if ($this->loaded) {
            $editableData[] = array ('type' => 'hidden','name' => 'indicator_id', 'value' => $this->indicator_id);
        }
        return $editableData;
    }


}
?>