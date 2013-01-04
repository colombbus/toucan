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

class Individual_Model extends Toucan_Model implements Ajax_Model {

    protected $belongs_to = array("indicator");
    protected $has_one = array("selection","variable");
    protected static $conditionals = array();
    protected $ignored_columns = array('conditionals', 'selection_id_simple','selection_id_simple_numerical', 'selection_id_multiple');

    public function getCreationData($access, & $user, & $parameters = null) {
        if (isset($parameters)&&isset($parameters['indicator_id'])) {
            $this->indicator_id = $parameters['indicator_id'];
        }
        return $this->getEditableData($access, $user);
    }

    public function getEditableData($access, & $user) {
        $editableData = array();
        $variables = $this->indicator->getVariablesInfo();
        $names = array();
        $simpleNotNumerical = array();
        $simpleNumerical = array();
        $multiple = array();
        foreach($variables as $id=>$data) {
            $names[$id] = $data['name'];
            if ($data['simple']) {
                if ($data['numerical'])
                    $simpleNumerical[] = $id;
                else
                    $simpleNotNumerical[] = $id;
            } else {
                $multiple[] = $id;
            }
        }
        $editableData[] = array('type'=>'select', 'label'=>'individual.variable', 'name'=>'variable_id', 'values'=>$names, 'value'=>$this->variable_id, 'required'=>1);
        $selectionsSimple = Selection_Model::getTranslatedList(true, false);
        $selectionsSimpleNumerical = Selection_Model::getTranslatedList(true, true);
        $selectionsMultiple = Selection_Model::getTranslatedList(false);

        $hideSimple = true;
        $hideSimpleNumerical = true;
        $hideMultiple = true;
        
        if ($this->variable_id == 0) {
            $firstVariableId = current(array_keys($names));
            $firstVariableInfo = $variables[$firstVariableId];
            if ($firstVariableInfo['simple']) {
                if ($firstVariableInfo['numerical'])
                    $hideSimpleNumerical = false;
                else
                    $hideSimple = false;
            } else {
                $hideMultiple = false;
            }
        } else {
            $variable = $this->variable;
            if ($variable->isMultiple()) {
                $hideMultiple = false;
            } else {
                if ($variable->numerical)
                    $hideSimpleNumerical = false;
                else
                    $hideSimple = false;
            }
        }
        
        $editableData[] = array('type'=>'select', 'label'=>'individual.selection', 'name'=>'selection_id_simple', 'values'=>$selectionsSimple, 'value'=>$this->selection_id, 'required'=>1, 'id'=>'selection_id_simple', 'hidden'=>$hideSimple);
        $editableData[] = array('type'=>'select', 'label'=>'individual.selection', 'name'=>'selection_id_simple_numerical', 'values'=>$selectionsSimpleNumerical, 'value'=>$this->selection_id, 'required'=>1, 'id'=>'selection_id_simple_numerical', 'hidden'=>$hideSimpleNumerical);
        $editableData[] = array('type'=>'select', 'label'=>'individual.selection', 'name'=>'selection_id_multiple', 'values'=>$selectionsMultiple, 'value'=>$this->selection_id, 'required'=>1, 'id'=>'selection_id_multiple', 'hidden'=>$hideMultiple);

        self::$conditionals[] = array('trigger'=>'variable_id', 'triggered'=>'selection_id_simple', 'values'=>$simpleNotNumerical);
        self::$conditionals[] = array('trigger'=>'variable_id', 'triggered'=>'selection_id_simple_numerical', 'values'=>$simpleNumerical);
        self::$conditionals[] = array('trigger'=>'variable_id', 'triggered'=>'selection_id_multiple', 'values'=>$multiple);
        
        if ($this->selection_id == 0) {
            $selections = array();
            if (!$hideSimple)
                $selections = $selectionsSimple;
            else if (!$hideSimpleNumerical)
                $selections = $selectionsSimpleNumerical;
            else
                $selections = $selectionsMultiple;
            $firstId = current(array_keys($selections));
            $hidden = !(ORM::factory('selection', $firstId)->requires_value);
        } else {
            $hidden = !($this->selection->requires_value);
        }
        $editableData[] = array('type'=>'text', 'label'=>'individual.value', 'name'=>'value', 'value'=>$this->value, 'hidden'=>$hidden, 'id'=>'value');

        self::$conditionals[] = array('trigger'=>'selection_id_simple', 'triggered'=>'value','values'=>Selection_Model::getIdsWithValue());
        self::$conditionals[] = array('trigger'=>'selection_id_simple_numerical', 'triggered'=>'value','values'=>Selection_Model::getIdsWithValue());
        self::$conditionals[] = array('trigger'=>'selection_id_multiple', 'triggered'=>'value','values'=>Selection_Model::getIdsWithValue());

        if ($this->loaded) {
            $editableData[] = array ('type' => 'hidden','name' => 'indicator_id', 'value' => $this->indicator_id);
        }
        return $editableData;
    }

    public function getDisplayableData($access, & $user = null) {
        $displayableData = array();
        $displayableData[] = array('type'=>'text', 'label'=>'individual.variable', 'value'=>$this->variable->name);
        $displayableData[] = array('type'=>'text', 'label'=>'individual.selection', 'value'=>$this->selection->getTranslatedName());
        if ($this->selection->requires_value)
            $displayableData[] = array('type'=>'text', 'label'=>'individual.value', 'value'=>$this->value);
        return $displayableData;
    }

    public function validateEdition(array & $array,& $user, $save = FALSE) {
        if (isset($array['variable_id'])&&is_numeric($array['variable_id'])) {
            $variable = ORM::factory('variable', $array['variable_id']);
            if ($variable->isMultiple()) {
                if (isset($array['selection_id_multiple']))
                    $array['selection_id'] = $array['selection_id_multiple'];
            } else {
                if ($variable->numerical) {
                    if (isset($array['selection_id_simple_numerical']))
                        $array['selection_id'] = $array['selection_id_simple_numerical'];
                } else {
                    if (isset($array['selection_id_simple']))
                        $array['selection_id'] = $array['selection_id_simple'];
                }
            }                
        }
        $this->validation = Validation::factory($array)
            ->pre_filter('trim')
            ->add_rules('variable_id', 'required', 'valid::numeric')
            ->add_rules('selection_id', 'required', 'valid::numeric')
            ->add_rules('indicator_id', 'required','valid::numeric')
            ->add_rules('value', 'length[0,50]')
            ->add_callbacks('selection_id', array($this, 'checkSelection'))
            ->add_callbacks('value', array($this, 'checkValue'));
        $result = parent::validate($this->validation, $save);
        if ($result) {
            $this->indicator->clearCache();
        }
        return $result;
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

    public function checkSelection(Validation $valid) {
        if (array_key_exists('selection_id', $valid->errors()))
            return;
        if (array_key_exists('variable_id', $valid->errors()))
            return;
        $variable = ORM::factory('variable', $valid->variable_id);
        $selection = ORM::factory('selection', $valid->selection_id);
        if (!isset($variable)) {
            $valid->add_error( 'variable_id', 'default');
            return;
        }
        if (!isset($selection)) {
            $valid->add_error( 'variable_id', 'default');
            return;
        }
        if ($variable->isMultiple()) {
            if (!$selection->isMultiple()) {
                if ($variable->numerical)
                    $valid->add_error( 'selection_id_simple_numerical', 'multiple');
                else
                    $valid->add_error( 'selection_id_simple', 'multiple');
            }
        } else {
            if (! $selection->isSimple()) {
                $valid->add_error( 'selection_id_multiple', 'simple');
            }
        }
        if (($selection->numerical)&&(!$variable->numerical)) {
            if ($selection->isSimple())
                $valid->add_error( 'selection_id_simple', 'numerical');
            else
                $valid->add_error( 'selection_id_multiple', 'numerical');
        }
    }

    public function checkValue(Validation $valid) {
        if (array_key_exists('selection_id', $valid->errors()))
            return;
        if (array_key_exists('value', $valid->errors()))
            return;
        $selection = ORM::factory('selection', $valid->selection_id);
        if (!isset($selection)) {
            $valid->add_error( 'selection_id', 'default');
            return;
        }
        if ($selection->requires_value) {
            if (!isset($valid->value)||(strlen(trim($valid->value))==0)) {
                $valid->add_error( 'value', 'required');
            }
        }
    }

    public function __get($column) {
        if ($column == 'name') {
            if ($this->selection->requires_value)
                return sprintf(Kohana::lang('individual.summary_with_value'), $this->variable->name, $this->selection->translatedName, $this->value);
            else
                return sprintf(Kohana::lang('individual.summary'), $this->variable->name, $this->selection->translatedName);
        } else if ($column == 'order') {
            return 0;
        }
        return parent::__get($column);
    }

    public function getNextOrder($id) {
        // not implemented
        return 0;
    }

    public function getItemActions(& $user) {
        $itemActions = array();
        if ($this->isEditableBy($user)) {
            $itemActions[] = array('function'=>"editItem", 'text'=>"individual.edit");
            $itemActions[] = array('function'=>"deleteItem", 'text'=>"individual.delete");
        }
        return $itemActions;
    }

    public function getConditional() {
        return self::$conditionals;
    }

    public function isIn(& $copy) {
        $methodName = $this->selection->name;
        if ($this->selection->requires_value)
            return selection::$methodName($this->variable->getValue($copy), $this->value);
        else
            return selection::$methodName($this->variable->getValue($copy));
    }
    
    public function updateValues($values) {
        foreach ($values as $key=>$value) {
            $this->$key = $value;
        }
    }

    public function copyTo(& $indicator, $variables = array()) {
        $newIndividual = ORM::factory('individual');
        $data = $this->as_array();
        unset($data['id']);
        $newIndividual->updateValues($data);
        $newIndividual->indicator_id = $indicator->id;
        if (isset($variables)&&isset($variables[$this->variable_id])) {
            $newIndividual->variable_id = $variables[$this->variable_id];
        } else {
            // a problem occured
            return null;
        }
        $newIndividual->save();
        return $newIndividual;
    }

}
?>