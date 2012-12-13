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

class InterviewSession_Model extends Session_Model {
    protected $has_many = array('interviewCopies');
    protected $sessionType = 2;
    protected $templateName = "interviewTemplate";
    protected $instanceName = "interviewInstance1";
    protected $sessionName = "interviewSession";
    protected $copyName = "interviewCopy";
    protected $templateIndicatorModel = "interviewTemplateIndicator";

    public function mayBeEditedByPublic() {
        return false;
    }

    public function delete() {
        // First : delete all corresponding copies
        if ($this->loaded) {
            $copies = $this->interviewCopies;
            foreach ($copies as $copy) {
                $copy->delete();
            }
        }

        // Second : delete the element itself
        parent::delete();
    }

    public function getCopies() {
        return $this->interviewCopies;
    }
    
    public function exportInDocument($includePrivate = false) {
        $evaluation = $this->evaluation;
        $logo = null;
        if ($evaluation->activity->logo_id>0)
            $logo = $evaluation->activity->logo->path;
        rtf::initDocument($evaluation->activity->name, $this->name, $logo);
        // description
        if (strlen($this->description)>0) {
            rtf::addParagraph($this->description);
        }
        // summary
        rtf::addTextQuestion(Kohana::lang('interviewCopy.summary'), " ", true);
        // questions
        $questions = $this->template->getQuestions($includePrivate);
        foreach ($questions as $question) {
            $question->exportInDocument();
        }
        // send document
        rtf::sendDocument(sprintf(Kohana::lang('session.export_document_name'), $this->name));
    }

}
?>