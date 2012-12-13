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

class AxQuestion_Controller extends Ajax_Controller {

    protected $dataName = "question";

    function edit($id, $creation=false, $separator = false) {
        if ($creation) {
            $this->data = Question_Model::getNewQuestion($id, $separator);
            $this->computeAccess();
        } else {
            $this->loadData($id);
        }
        $this->ensureAccess(access::MAY_EDIT);
        if (!$this->data->isEditable()) {
            $this->displayError('restricted_access');
        }

        // MANAGE FORM
        $formErrors= array();
        if (($post = $this->input->post())&&(isset($post["form_edit_question"])||isset($post["form_create_question"]))) {
            // make a copy of post in case there is an error (because $post can get modified by validation)
            $postCopy = $post;
            if ($creation) {
                $result = $this->data->validateCreation($post,$this->user, true);
                $message = Kohana::lang("$this->dataName.created");
            } else {
                $result = $this->data->validateEdition($post,$this->user, true);
                $message = Kohana::lang("$this->dataName.edited");
            }
            if ($result) {
                // data could be validated and saved
                $this->setMessage($message);
                if ($creation)
                    url::redirect("axQuestion/show/".$this->data->id."/1");
                else
                    url::redirect("axQuestion/show/".$this->data->id);
            } else {
                // errors when trying to validate data
                $formErrors = $this->data->getErrors("form_errors");
                // populate values in order to retrieve input data
                $this->data->setValues($postCopy);
            }
        }

        $item['order'] = $this->data->order;

        if ($creation) {
            $item['content'] = $this->data->getCreationData(access::MAY_EDIT, $this->user);
            $this->view=new View("question/new_question");
            $this->view->formId = "form_create_question";
        } else {
            $item['content'] = $this->data->getEditableData(access::MAY_EDIT, $this->user);
            $item['id'] = $this->data->id;
            $this->view=new View("question/edit");
            $this->view->formId = "form_edit_question";
        }
        if ($separator) {
            $item['color'] = Kohana::config('toucan.separator_color');
        }
        $this->view->item = $item;
        $this->view->errors = $formErrors;
        $this->view->cancel = "button.cancel";
        $this->view->save = "button.save";
        if ($this->data->isAdvanced()) {
            $this->view->displayChoices = true;
            $choices = $this->data->getEditableChoices(access::MAY_EDIT, $this->user);
            $this->view->displayValues = QuestionType_Model::getTypesWithChoicesIds();
            $this->view->choices = $choices;
            $this->view->addChoice = "choice.add";
            $this->view->deleteChoice = "choice.delete";
            $this->view->reorderChoices = "choice.reorder";
            $this->view->availableChoices = "choice.available";
            $this->view->showChoices = $this->data->type->choices;
        } else {
            $this->view->displayChoices = false;
        }
    }

    function show($id, $new = false) {
        $this->loadData($id);
        $this->ensureAccess(access::MAY_VIEW);
        $item['title'] = $this->data->text;
        $item['content'] = $this->data->getDisplayableData(access::MAY_VIEW);
        $item['order'] = $this->data->order;
        $item['actions'] = $this->data->getItemActions($this->user);
        $item['id'] = $id;
        if ($this->data->isSeparator()) {
            if ($this->data->isSubSeparator())
                $item['color'] = Kohana::config('toucan.sub_separator_color');
            else
                $item['color'] = Kohana::config('toucan.separator_color');
        }
        if ($this->data->isPrivate()) {
            $item['title'] = sprintf(Kohana::lang('template.private_question'), $item['title']);
        }
        $this->view=new View("data/view_item");
        $this->view->newItem = $new;
        $this->view->item = $item;
        $this->view->isDraggable = $this->testAccess(access::MAY_EDIT);
        $this->view->dragUpdateRequired = $this->view->isDraggable;
    }

    function create($templateId) {
        $this->edit($templateId, true);
    }

    function createSeparator($templateId) {
        $this->edit($templateId, true, true);
    }

    function addChoice($id, $count = null) {
        $this->data = ORM::factory("question");
        $this->view=new View("question/new_choice");
        $this->view->choice = $this->data->getCreationChoice(access::MAY_EDIT, $this->user, $count);
        $this->view->deleteChoice = "choice.delete";
        $this->view->id = $id;
    }

    public function triggers($id) {
        $this->loadData($id);
        $this->ensureAccess(access::MAY_EDIT);
        if (!$this->data->isEditable()) {
            $this->displayError('restricted_access');
        }


        // MANAGE FORM
        $errorMessage= null;
        if (($post = $this->input->post())&&isset($post["form_question_triggers"])) {
            $result = $this->data->validateTriggers($post,$this->user, true);
            $message = Kohana::lang("$this->dataName.edited");
            if ($result) {
                // data could be validated and saved
                $this->setMessage($message);
                url::redirect("axQuestion/show/$id");
            } else {
                // errors when trying to validate data
                $formErrors = $this->data->getErrors("form_errors");
                $errorMessage = $formErrors['triggers'];
                // populate values in order to retrieve input data
                $this->data->setTriggersValues($post);
            }
        }

        $this->view=new View("question/triggers");
        $this->view->formId = "form_question_triggers";
        $this->view->id = $id;
        $this->view->showContent = $this->data->hasTriggers();
        $this->view->data = $this->data->getTriggersEditableData(access::MAY_EDIT, $this->user);
        $this->view->errors = array();
        $this->view->errorMessage = $errorMessage;
        $this->view->cancel = "button.cancel";
        $this->view->save = "button.save";
        $this->view->choicesUrl = "axQuestion/triggerChoices";
    }

    public function triggerChoices($id, $triggerId) {
        $this->loadData($id);
        $this->ensureAccess(access::MAY_EDIT);
        if (!$this->data->isEditable()) {
            $this->displayError('restricted_access');
        }

        $this->view=new View("question/triggerChoices");
        $this->view->data = $this->data->getTriggerChoicesEditableData(access::MAY_EDIT, $this->user, $triggerId);
        $this->view->errors = array();
    }

    public function reorder($dataName,$id) {
        $this->dataName = $dataName;
        $this->loadData($id);
        $this->ensureAccess(access::MAY_EDIT);
        if (!$this->data->isEditable()) {
            $this->displayError('restricted_access');
        }

        if (!isset($_POST['data'])) {
            $this->displayError('no data provided');
        }
        parse_str($_POST['data']);
        $order = 1;
        foreach ($items as $item) {
            $question = ORM::factory("question", $item);
            $question->order = $order;
            $question->save();
            $order++;
        }
        $this->auto_render = false;
    }

}
?>