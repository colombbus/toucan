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

class SurveyCopy_Model extends FormCopy_Model {
    protected $sessionName = "Survey";
    protected $templateName = "FormTemplate";
    protected $copyName = "surveyCopy";

    
    public function __get($column) {
        if ($column == 'session') {
            // retrieve a survey from session id
            if (isset($this->session_id)) {
                return new Survey_Model($this->session_id);
            }
            return null;
        }
        return parent::__get($column);
    }


    public function validateEdition(array & $array,& $user, $save = FALSE, $saveAnyway = FALSE) {
        $alreadyPublished = $this->isPublished();
        $this->validateAnswers($array, $user);
        if ((sizeof($this->errors)>0)&&!$saveAnyway) {
            return false;
        }
        if (isset($array['state_id']))
            $this->state_id = $array['state_id'];
        else
            $this->state_id = CopyState_Model::PUBLISHED;
        if ($save) {
            $this->save();
            $this->saveAnswers();
            $this->session->clearCache();
            // notification
            if (!$alreadyPublished && $this->isPublished()) {
                $this->session->notify($this->copyName, $this->id);
            }
        }
        return true;
    }

    public function validateCreation(array & $array,& $user, $save = FALSE) {
        if (!isset ($array['session_id'])) {
            return false;
        }
        $sessionId = $array['session_id'];
        $this->validateAnswers($array,$user, $sessionId);
        if (sizeof($this->errors)>0) {
            return false;
        }
        if (isset($user))
            $this->setOwner($user, false);
        else {
            // record ip address
            $this->ip_address = Utils::getRemoteIp();
        }
        $this->session_id = $sessionId;
        $this->created = time();
        if (isset($array['state_id']))
            $this->state_id = $array['state_id'];
        else
            $this->state_id = CopyState_Model::PUBLISHED;
        if ($save) {
            $this->save();
            $this->saveAnswers();
            $this->session->clearCache();
            // notification
            if ($this->state_id == CopyState_Model::PUBLISHED) {
                $this->session->notify($this->copyName, $this->id);
            }
        }
        return true;
    }

    public function exportInDocument(& $user) {
        $survey = $this->session;
        $activity = $survey->activity;
        $template = $survey->template;
        $logo = null;
        if ($activity->logo_id>0)
            $logo = $activity->logo->path;
        rtf::initDocument($activity->name, $survey->name, $logo);
        // description
        if (strlen($survey->description)>0) {
            rtf::addParagraph($survey->description);
        }
        // questions
        $questions = $template->getQuestions($survey->isEditableBy($user));
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
        rtf::sendDocument(sprintf(Kohana::lang($this->copyName.'.export_document_name'), $survey->name, $this->translated_created));
    }

    
}
?>