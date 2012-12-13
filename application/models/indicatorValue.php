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

class IndicatorValue_Model extends Toucan_Model implements Ajax_Model {

    protected $table_name = "indicator_values";
    protected $belongs_to = array("indicator");
    protected $has_one = array("color");

    public function getCreationData($access, & $user, & $parameters = null) {
        return $this->getEditableData($access, $user);
    }

    public function getEditableData($access, & $user) {
        $editableData = array();
        $editableData[] = array('type'=>'text', 'label'=>'indicatorValue.name', 'name'=>'name', 'value'=>$this->name, 'required'=>1);
        $editableData[] = array('type'=>'long_text', 'label'=>'indicatorValue.description', 'name'=>'description', 'value'=>$this->description);
        $colors = array(0 => " ") + Color_Model::getTranslatedColors();
        $editableData[] = array('type'=>'select', 'label'=>'indicatorValue.color', 'name'=>'color_id', 'values'=>$colors, 'value'=>$this->color_id, );
        if ($this->loaded) {
            $editableData[] = array ('type' => 'hidden','name' => 'indicator_id', 'value' => $this->indicator_id);
            $editableData[] = array ('type' => 'hidden','name' => 'order', 'value' => $this->order);
        }
        return $editableData;
    }

    public function getDisplayableData($access, & $user = null) {
        $displayableData = array();
        $displayableData[] = array('type'=>'text', 'label'=>'indicatorValue.name', 'value'=>$this->name);
        if (strlen(trim($this->description))>0)
            $displayableData[] = array('type'=>'long_text', 'label'=>'indicatorValue.description', 'value'=>$this->description);
        if ($this->color_id>0)
            $displayableData[] = array('type'=>'color', 'label'=>'indicatorValue.color', 'value'=>$this->color->getTranslatedName(), 'code'=>$this->color->code);
        return $displayableData;
    }

    public function validateEdition(array & $array,& $user, $save = FALSE) {
        $this->validation = Validation::factory($array)
            ->pre_filter('trim')
            ->add_rules('name', 'required', 'length[1,127]')
            ->add_callbacks('name', array($this, 'uniqueNameByIndicator'))
            ->add_rules('description', 'length[0,500]')
            ->add_rules('indicator_id', 'valid::numeric')
            ->add_rules('color_id', 'valid::numeric')
            ->add_rules('order', 'valid::numeric');
        return parent::validate($this->validation, $save);
    }

    public function validateCreation(array & $array,& $user, $save = FALSE) {
        return $this->validateEdition($array, $user, $save);
    }

    public function count(& $filter , & $user, $constraintId = null) {
        // not implemented
    }

    public function getItems(& $filter,& $user,$offset = 0, $number = null, $constraintId = null) {
        // not implemented
    }

    public function isEditableBy(& $user) {
        if ($this->loaded) {
            $indicator = $this->indicator;
            return $indicator->isEditableBy($user);
        }
        return false;
    }

    public function getNextOrder($indicatorId) {
        $result = $this->db->query("SELECT max(`order`)+1 from indicator_values WHERE indicator_id = $indicatorId");
        $result->result(false,MYSQL_NUM);
        if ($result[0][0] === null)
            return 1;
        else
            return $result[0][0];
    }

    public function uniqueNameByIndicator(Validation $valid) {
        if (array_key_exists('name', $valid->errors()))
            return;
        if (isset ($valid->indicator_id)) {
            $escapedName = addslashes($valid->name);
            $indicatorId = $valid->indicator_id;
            if ($this->loaded) {
                $other = $this->db->query("SELECT id from ".$this->table_name." WHERE id != $this->id AND indicator_id = '$indicatorId' AND name = '$escapedName'");
            } else {
                $other = $this->db->query("SELECT id from ".$this->table_name." WHERE indicator_id = '$indicatorId' AND name = '$escapedName'");
            }
            if ($other->count() > 0) {
                $valid->add_error( 'name', 'uniqueName');
            }
        }
    }

    public function getItemActions(& $user) {
        $itemActions = array();
        $itemActions[] = array('function'=>"displayItem", 'text'=>"indicatorValue.display");
        if ($this->isEditableBy($user)) {
            $itemActions[] = array('function'=>"editItem", 'text'=>"indicatorValue.edit");
            $itemActions[] = array('function'=>"deleteItem", 'text'=>"indicatorValue.delete");
        }
        return $itemActions;
    }
    
    public function updateValues($values) {
        foreach ($values as $key=>$value) {
            $this->$key = $value;
        }
    }

    public function copyTo(& $indicator) {
        $newValue = ORM::factory('indicatorValue');
        $data = $this->as_array();
        unset($data['id']);
        $newValue->updateValues($data);
        $newValue->indicator_id = $indicator->id;
        $newValue->save();
        return $newValue;
    }



}
?>