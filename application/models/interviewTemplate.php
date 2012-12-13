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

class InterviewTemplate_Model extends Template_Model {

    public $templateType = self::INTERVIEW_TYPE;
    protected $instanceName = "interviewInstance";
    protected $indicatorModel = "interviewTemplateIndicator";


    public function questionAdvanced() {
        return false;
    }
    
    public function hasForms() {
        return false;
    }

    public function exportInDocument($includePrivate = true) {
        rtf::initDocument($this->name);
        // description
        if (strlen($this->description)>0) {
            rtf::addParagraph($this->description);
        }
        // summary
        rtf::addTextQuestion(Kohana::lang('interviewCopy.summary'), " ", true);
        // questions
        $questions = $this->getQuestions($includePrivate);
        foreach ($questions as $question) {
            $question->exportInDocument();
        }
        // send document
        rtf::sendDocument(sprintf(Kohana::lang('template.export_file_name'), $this->name));
    }

    
}
?>