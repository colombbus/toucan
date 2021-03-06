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

class AxIndividual_Controller extends Ajax_Controller {

    protected $dataName = "individual";
    protected $parentDataName = "indicator";
    protected $controllerName = "axIndividual";

    public function create($indicatorId) {
        parent::create($indicatorId, array('indicator_id'=>$indicatorId),"individual/new_individual");
        $this->view->valuesUrl = $this->controllerName."/getValues/";
    }

    public function show($id, $new = false) {
        parent::show($id, $new);
        $this->view->isDraggable = false;
    }

    public function edit($id) {
        parent::edit($id, "individual/edit_individual");
        $this->view->valuesUrl = $this->controllerName."/getValues/";
    }

    public function getValues($variableId) {
        $variable = ORM::factory("variable", $variableId);
        $this->auto_render = false;
        if (isset($variable)&&$variable->loaded) {
            $question = $variable->question;
            if (isset($question)&&$question->isViewableBy($this->user)) {
                if (($question->type_id == QuestionType_Model::MULTIPLE_CHOICE) || ($question->type_id == QuestionType_Model::CHOICE)) {
                    $choices = $question->choices;
                    $possibleValues = array();
                    foreach ($choices as $choice) {
                        $possibleValues[] = $choice->getValue();
                    }
                    $this->view=new View("individual/values");
                    $this->view->label = Kohana::lang('individual.choose_value');
                    $this->view->possibleValues = $possibleValues;
                    $this->auto_render = true;
                }
            }
        }
    }
    
}
?>