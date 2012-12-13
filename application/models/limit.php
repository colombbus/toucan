<?php  defined('SYSPATH') or die('No direct script access.');
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

class Limit_Model extends Toucan_Model implements Ajax_Model {

    protected $belongs_to = array("indicator");
    protected $has_one = array("color");

    public function getCreationData($access, & $user, & $parameters = null) {
        return $this->getEditableData($access, $user);
    }

    public function getEditableData($access, & $user) {
        $editableData = array();
        $operators = array();
        for ($i=1;$i<=5;$i++) {
            $operators[$i] = Kohana::lang('limit.operator_'.$i);
        }
        $editableData[] = array('type'=>'text', 'label'=>'limit.value_min', 'name'=>'value_min', 'value'=>$this->value_min);
        $editableData[] = array('type'=>'text', 'label'=>'limit.value_max', 'name'=>'value_max', 'value'=>$this->value_max);
        $editableData[] = array('type'=>'check', 'label'=>'limit.inclusive', 'name'=>'inclusive', 'checked'=>$this->inclusive, 'value'=>1);
        $colors = Color_Model::getTranslatedColors();
        $editableData[] = array('type'=>'select', 'label'=>'limit.color', 'name'=>'color_id', 'values'=>$colors, 'value'=>$this->color_id, 'required'=>1);
        if ($this->loaded) {
            $editableData[] = array ('type' => 'hidden','name' => 'indicator_id', 'value' => $this->indicator_id);
        }
        return $editableData;
    }

    public function getDisplayableData($access, & $user = null) {
        $displayableData = array();
        //$displayableData[] = array('type'=>'text', 'label'=>'limit.name','value'=>$this->name);
        if ($this->inclusive) {
            $inclusive = Kohana::lang('limit.inclusive_yes');
        } else {
            $inclusive = Kohana::lang('limit.inclusive_no');
        }
        $displayableData[] = array('type'=>'text', 'label'=>'limit.inclusive', 'value'=>$inclusive);
        $displayableData[] = array('type'=>'color', 'label'=>'limit.color', 'value'=>$this->color->getTranslatedName(), 'code'=>$this->color->code);
        return $displayableData;
    }

    public function validateEdition(array & $array,& $user, $save = FALSE) {
        if (!isset($array['inclusive'])) {
            $array['inclusive'] = 0;
        }
        $this->validation = Validation::factory($array)
            ->pre_filter('trim')
            ->add_rules('value_min', 'valid::numeric')
            ->add_rules('value_max', 'valid::numeric')
            ->add_rules('inclusive', 'in_array[0,1]')
            ->add_rules('indicator_id', 'valid::numeric')
            ->add_rules('color_id', 'valid::numeric')
            ->add_callbacks('value_min', array($this, 'checkValueMin'))
            ->add_callbacks('value_max', array($this, 'checkValueMax'));
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
        return 0;
    }

    public function getItemActions(& $user) {
        $itemActions = array();
        if ($this->isEditableBy($user)) {
            $itemActions[] = array('function'=>"editItem", 'text'=>"limit.edit");
            $itemActions[] = array('function'=>"deleteItem", 'text'=>"limit.delete");
        }
        return $itemActions;
    }

    public function __get($column) {
        if ($column == 'name') {
            if (isset($this->value_min)) {
                if (isset($this->value_max)) {
                    if ($this->value_min == $this->value_max) {
                        return sprintf(Kohana::lang('limit.equals'), $this->value_max);
                    } else {
                        return sprintf(Kohana::lang('limit.two_limits_'.$this->inclusive), $this->value_min, $this->value_max);
                    }
                } else {
                    return sprintf(Kohana::lang('limit.superior_'.$this->inclusive), $this->value_min);
                }
            } else if (isset($this->value_max)) {
                return sprintf(Kohana::lang('limit.inferior_'.$this->inclusive), $this->value_max);
            } else {
                return "";
            }

        }  else if ($column == 'order') {
            return 0;
        }
        return parent::__get($column);
    }

    public function checkValueMin(Validation $valid) {
        if (array_key_exists('value_min', $valid->errors()))
            return;
    }


    public function checkValueMax(Validation $valid) {
        if (array_key_exists('value_min', $valid->errors()))
            return;
        if (array_key_exists('value_max', $valid->errors()))
            return;
        
        if (isset($valid->value_min)) {
            if (strlen(trim($valid->value_min))==0) {
                $valid->value_min = null;
            }
        }
        if (isset($valid->value_max)) {
            if (strlen(trim($valid->value_max))==0) {
                $valid->value_max = null;
            }
        }
        if (($valid->value_min===null)&&($valid->value_max===null)) {
            $valid->add_error( 'value_min', 'required');
            $valid->add_error( 'value_max', 'required');
        }
        if (($valid->value_min!==null)&&($valid->value_max!==null)) {
            if ($valid->value_max < $valid->value_min) {
                $valid->add_error( 'value_max', 'inferior');
            }
            if ($valid->value_min == $valid->value_max) {
                $valid->inclusive = 1;
            }
        }
    }

    public function contains($value) {
        if (isset($this->value_min)) {
            if ($this->inclusive) {
                if ($value<$this->value_min)
                    return false;
            } else {
                if ($value<=$this->value_min)
                    return false;
            }
        }
        if (isset($this->value_max)) {
            if ($this->inclusive) {
                if ($value>$this->value_max)
                    return false;
            } else {
                if ($value>=$this->value_max)
                    return false;
            }
        }
        return true;
    }
    
    public function updateValues($values) {
        foreach ($values as $key=>$value) {
            $this->$key = $value;
        }
    }
    
    public function copyTo(& $indicator) {
        $newLimit = ORM::factory('limit');
        $data = $this->as_array();
        unset($data['id']);
        $newLimit->updateValues($data);
        $newLimit->indicator_id = $indicator->id;
        $newLimit->save();
        return $newLimit;
    }
    

}
?>