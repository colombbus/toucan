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

class InterviewCopy_Model extends Copy_Model {

    protected $sessionName = "InterviewSession";
    protected $templateName = "interviewTemplate";
    protected $copyName = "interviewCopy";
    protected $previousFileTitle = null;

    public function getCreationData($access, & $user, & $parameters = null) {
        if (isset($parameters)) {
            if (isset($parameters['session_id'])) {
                $sessionId = $parameters['session_id'];
                $questions = $this->getQuestions($sessionId);
            } else if (isset($parameters['template_id'])) {
                $template = ORM::factory($this->templateName, $parameters['template_id']);
                $questions = $template->questions;
            }
            $creationData = array();
            // Summary
            if (isset($this->values['summary']))
                $creationData[] = array('label'=>'interviewCopy.summary','type'=>'long_text', 'name'=>'summary', 'value'=>$this->values['summary'], 'class'=>'edit_copy');
            else
                $creationData[] = array('label'=>'interviewCopy.summary','type'=>'long_text', 'name'=>'summary', 'class'=>'edit_copy');
            $creationData[] = array('type'=>'separator', 'label'=>'interviewCopy.answers', 'name'=>'answers');
            // Answers
            foreach ($questions as $question) {
                $item = array();
                $item['translated_label'] = $question->text;
                if (strlen(trim($question->description))>0)
                    $item['translated_description'] = $question->description;
                $item['name'] = 'question_'.$question->id;
                if ($question->isSeparator()) {
                    $item['type'] = 'form_separator';
                    $item['sub_separator'] = $question->isSubSeparator();
                    $item['value'] = $question->text;
                } else {
                    $item['type'] = 'long_text';
                    $item['class'] = 'edit_copy';
                    if (isset($this->values[$question->id])) {
                        $item['value'] = $this->values[$question->id];
                    }
                }

                $creationData[] = $item;
            }
            $creationData[] = array('type'=>'hidden', 'name'=>'state_id', 'id'=>'state_id', 'value'=>CopyState_Model::PUBLISHED);
        }
        return $creationData;
    }

    public function getDisplayableData($access, & $user = null) {
        $displayableData = array();
        $displayableData[] = array('type'=>'text','label'=>'interviewCopy.owner','value'=>$this->owner->fullName);
        $displayableData[] = array('type'=>'text','label'=>'interviewCopy.creation_date','value'=>Utils::translateTimestamp($this->created));
        $displayableData[] = array('type'=>'text','label'=>'interviewCopy.state','value'=>$this->state->translatedName);
        $displayableData[] = array('type'=>'separator');
        // SUMMARY
        if (isset($this->summary_id)) {
            $displayableData[] = array('type'=>'long_text','label'=>'interviewCopy.summary','value'=>$this->summary->value, 'class'=>'view_copy');
        }
        // ANSWERS
        $questions = $this->getQuestions();
        foreach ($questions as $question) {
            $item = array();
            $item['translated_label'] = $question->text;//.Kohana::lang('copy.colons');
            if ($question->isSeparator()) {
                $item['type'] = 'form_separator';
                $item['sub_separator'] = $question->isSubSeparator();
                $item['value'] = $question->text;
            } else {
                $item['class'] = 'view_copy';
                $item['type'] = 'long_text';
                $answers = $this->getAnswers($question->id);
                if ($answers->valid()) {
                    $answer = $answers->current();
                    $item['value'] = $answer->getValue();
                } else
                    $item['value'] = "";
            }
            $displayableData[] = $item;
        }
        return $displayableData;
    }

    public function getEditableData($access, & $user, $name="", $description="") {
        $questions = $this->getQuestions();
        if (!$this->valuesSetFromPost) {
            // Read values from database
            $this->values = array();
            // summary
            if (isset($this->summary_id)) {
                $this->values['summary'] = $this->summary->value;
            }
            // answers
            foreach ($questions as $question) {
                $answers = $this->getAnswers($question->id);
                if ($answers->valid()) {
                    $answer = $answers->current();
                    $this->values[$question->id] = $answer->getValue();
                }
            }
        }
        $parameters = array('session_id'=>$this->session_id);
        $data = $this->getCreationData($access, $user, $parameters);
        return $data;
    }

    protected function validateAnswers(array & $array,& $user, $sessionId = null) {
        $questions = $this->getQuestions($sessionId, $user);
        $this->values = array();
        // Summary
        if (isset($array['summary'])&&strlen(trim($array['summary']))>0) {
            $value = $array['summary'];
            if (strlen($value)>self::TEXT_MAX_LENGTH) {
                $this->errors['summary'] = "text_too_long";
            } else {
                $this->values['summary'] = $value;
            }
        } else {
            // set the value empty
            $this->values['summary'] = '';
        }
        // Answers
        foreach ($questions as $question) {
            if (isset($array['question_'.$question->id])) {
                if (strlen(trim($array['question_'.$question->id]))==0) {
                    unset ($array['question_'.$question->id]);
                }
            }
            if (isset($array['question_'.$question->id])) {
                $value = $array['question_'.$question->id];
                if (strlen($value)>self::TEXT_MAX_LENGTH) {
                    $this->errors['question_'.$question->id] = "text_too_long";
                } else {
                    $this->values[$question->id] = $value;
                }
            } else {
                // set the value empty
                $this->values[$question->id] = '';
            }
        }
    }

    protected function saveAnswers() {
        // Summary
        if (isset($this->values['summary'])) {
            if (isset($this->summary_id))
                $summary = $this->summary;
            else
                $summary = ORM::factory('textValue');
            $summary->value = $this->values['summary'];
            $summary->save();
            $this->summary_id = $summary->id;
            $this->save();
            unset($this->values['summary']);
        }

        // Answers
        foreach ($this->values as $id=>$value) {
            $question = ORM::factory('question', $id);
            $answer = $this->getAnswer($id);
            if ($answer->loaded) {
                $textValue = ORM::factory('textValue', $answer->value_id);
                $saveRequired = false;
            } else {
                $textValue = ORM::factory('textValue');
                $saveRequired = true;
            }
            $textValue->value = $value;
            $textValue->save();
            if ($saveRequired) {
                $answer->value_id = $textValue->id;
                $answer->save();
            }
        }
    }

    public function delete() {
        if (isset($this->summary_id)) {
            $this->summary->delete();
        }
        parent::delete();
    }

    public function setValues(& $array) {
        parent::setValues($array);
        if (isset($array['summary'])) {
            $this->values['summary'] = $array['summary'];
        }
    }

    public function setFileValues(& $array) {
        if (isset($array['file_title'])) {
            $this->previousFileTitle = $array['file_title'];
        }
    }

    public function getFileCreationData($access, & $user = null) {
        $creationData = array();
        $creationData[] = array('type' => 'file','name' => 'file', 'label' => 'interviewCopy.file_name', 'required'=>1);
        if (isset ($this->previousFileTitle)) {
            $creationData[] = array('type' => 'text','name' => 'file_title', 'label' => 'interviewCopy.file_title', 'value'=>$this->previousFileTitle);
        } else {
            $creationData[] = array('type' => 'text','name' => 'file_title', 'label' => 'interviewCopy.file_title');
        }
        return $creationData;
    }

    public function getFileEditableData($access, & $user = null, $fileId) {
        if (isset($this->previousFileTitle)) {
            $title = $this->previousFileTitle;
        } else {
            $file = ORM::factory('file', $fileId);
            $title = $file->title;
        }
        $editionData = array();
        $editionData[] = array('type' => 'text','name' => 'file_title', 'label' => 'interviewCopy.file_title', 'value'=>$title);
        return $editionData;
    }

    public function getFileDisplayableData($access, & $user = null, $fileId) {
        $file = ORM::factory('file', $fileId);
        $displayableData = array();
        $displayableData[] = array('type' => 'file','value' => $file->title, 'path'=>'interviewCopy/getFile/'.$this->id.'/'.$file->id);
        return $displayableData;
    }

    public function getFilesDisplayableData($access, & $user = null) {
        $filesData = array();
        foreach ($this->files as $file) {
            $data = array();
            $data['id'] = $file->id;
            $data['data'] = $this->getFileDisplayableData($access, $user, $file->id);
            $filesData[] = $data;
        }
        return $filesData;
    }

    public function validateFileEdition(array & $array, $fileId) {
        $this->validation = Validation::factory($array);
        $this->validation->pre_filter('trim')
             ->add_rules('file_title', 'length[1,127]');
        if ($this->validation->validate()) {
            $file = ORM::factory('file', $fileId);
            if (isset($array['file_title'])&&(strlen($array['file_title'])>0)) {
                $file->title = $array['file_title'];
            } else {
                $file->title = $file->name;
            }
            $file->save();
            return true;
        }
        return false;
    }

    public function validateFileCreation(array & $array) {
        $this->validation = Validation::factory($_FILES)
             ->add_rules('file', 'upload::valid', 'upload::size[2M]');
        if ($this->validation->validate()) {
            $this->validation = Validation::factory($array)
                ->pre_filter('trim')
                 ->add_rules('file_title', 'length[1,127]');
            if ($this->validation->validate()) {
                if (upload::required($_FILES['file'])) {
                    $originalName = $_FILES['file']['name'];
                    $dotIndex = strrpos($originalName, ".");
                    $extension = "";
                    if ($dotIndex !== FALSE) {
                        $extension = substr($originalName, $dotIndex);
                    }
                    $file = File_Model::newFile('interviews',$extension);
                    upload::save('file', $file->name, $file->getAbsoluteDirectory(), Kohana::config('toucan.public_file_mode'));
                    if (isset($array['file_title'])&&(strlen($array['file_title'])>0)) {
                        $file->title = $array['file_title'];
                    } else {
                        $file->title = $originalName;
                    }
                    $file->save();
                    $this->add($file);
                    $this->save();
                    return $file->id;
                }
            }
        }
        return false;
    }

    public function getFileErrors($lang_file=null) {
        if (!isset($this->validation)) {
            return null;
        } else {
            if (isset($lang_file)) {
                return $this->validation->errors($lang_file);
            } else {
                return $this->validation->errors();
            }
        }
    }
    
    public function exportInDocument(& $user) {
        $session = $this->session;
        $evaluation = $session->evaluation;
        $template = $session->template;
        $logo = null;
        if ($evaluation->activity->logo_id>0)
            $logo = $evaluation->activity->logo->path;
        rtf::initDocument($evaluation->activity->name, $session->name, $logo);
        // description
        if (strlen($session->description)>0) {
            rtf::addParagraph($session->description);
        }
        // summary
        $summary = null;
        if (isset($this->summary_id)) {
                $summary = $this->summary->value;
        }
        rtf::addTextQuestion(Kohana::lang('interviewCopy.summary'), " ", true, $summary);
        // questions
        $questions = $template->getQuestions($session->isEditableBy($user));
        foreach ($questions as $question) {
            $question->exportInDocument($this->getAnswers($question->id));
        }
        // information
        $information = array();
        $information[] = sprintf(Kohana::lang($this->copyName.'.export_owner_name'),$this->owner_name);
        $information[] = sprintf(Kohana::lang($this->copyName.'.export_creation_date'),$this->translated_created);
        $information[] = sprintf(Kohana::lang($this->copyName.'.export_state'),$this->state->translatedName);
        rtf::addInformation($information);

        // send document
        rtf::sendDocument(sprintf(Kohana::lang($this->copyName.'.export_document_name'), $session->name, $this->translated_created));
    }


}
?>