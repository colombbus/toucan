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

class Choice_Model extends Toucan_Model {

    protected $belongs_to = array('question');
    protected $ignored_columns = array('index');

    public function getCreationData($access, & $user, & $parameters = null) {
        if (!isset($parameters))
            $index = "";
        else
            $index = $parameters;
        $creationData = array();
        $creationData[] = array ('type' => 'text','name' => 'choice_text_'.$index, 'label' => 'choice.text');
        $creationData[] = array ('type' => 'text','name' => 'choice_value_'.$index, 'label' => 'choice.value');
        return $creationData;
    }

    public function getEditableData($access, & $user){
        $editableData = array();
        $editableData[] = array ('type' => 'text','name' => 'choice_text_'.$this->order,'value' => $this->text, 'label' => 'choice.text');
        $editableData[] = array ('type' => 'text','name' => 'choice_value_'.$this->order,'value' => $this->value,'label' => 'choice.value');
        return $editableData;
    }

    public function getDisplayableData($access, & $user = null) {
        $displayableData = array();
        $displayableData[] = array ('type' => 'text','name' => 'choice_text_'.$this->order, 'label' => 'choice.text', 'value' => $this->text);
        $displayableData[] = array ('type' => 'text','name' => 'choice_value_'.$this->order,'label' => 'choice.value','value' => $this->value);
        return $displayableData;
    }

    protected function buildValidation(& $array) {
        $this->validation = Validation::factory($array)
        ->pre_filter('trim')
        ->add_rules('text', 'required', 'length[1,127]')
        ->add_rules('value', 'length[0,50]');
    }

    public function validateEdition(array & $array,& $user, $save = FALSE) {
        $this->buildValidation($array);
        return parent::validate($this->validation, $save);
    }

    public function validateCreation(array & $array,& $user, $save = FALSE) {
        return $this->validationEdition($array, $user, $save);
    }

    public function isViewableBy(& $user) {
        return $this->question->isViewableBy($user);
    }

    public function isEditableBy(& $user) {
        return $this->question->isEditableBy($user);
    }

    public function count(& $filter , & $user, $constraintId = null) {
        // not implemented
    }

    public function getItems(& $filter,& $user,$offset = 0, $number = null, $constraintId = null) {
        // not implemented
    }

    public function copy($choice, $question) {
        $this->text = $choice->text;
        $this->order = $choice->order;
        $this->value = $choice->value;
        $this->question_id = $question->id;
        $this->save();
    }

}
?>