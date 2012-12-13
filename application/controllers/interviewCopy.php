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

class InterviewCopy_Controller extends Copy_Controller {

    protected $dataName = "interviewCopy";
    protected $sessionName = "interviewSession";
    protected $controllerName = "interviews";
    protected $type = 2;

    public function show($id) {
        parent::show($id);
        $this->template->content->joinFileUrl = "interviewCopy/join/$id";
        $this->template->content->deleteFileUrl = "axInterviewCopy/deleteFile/$id";
        $this->template->content->editFileUrl = "axInterviewCopy/editFile/$id";
        $this->template->content->showFileUrl = "axInterviewCopy/showFile/$id";
        $this->template->content->filesData = $this->data->getFilesDisplayableData(access::MAY_VIEW, $this->user);
        $this->template->content->newFileData = $this->data->getFileCreationData(access::MAY_EDIT, $this->user);
        $this->template->content->editFile = "interviewCopy.edit_file";
        $this->template->content->deleteFile = "interviewCopy.delete_file";
        $this->template->content->confirmFileDeletion = "interviewCopy.file_confirm_deletion";
        $this->template->content->cancelFile = "interviewCopy.file_cancel";
        $this->template->content->submitFile = "interviewCopy.file_submit";
        $this->template->content->mayEditFiles = $this->testAccess(access::MAY_EDIT);
    }

    protected function setActions($action, $evaluationId = null) {
        parent::setActions($action, $evaluationId);
        switch ($action) {
            case 'SHOW' :
                if ($this->data->isEditableBy($this->user)) {
                    $this->template->content->actions[] =  array('type' => 'button','text' => 'interviewCopy.join_file','js' => 'joinFile()');
                }
                break;
        }
    }

    public function join($id) {
        // LOAD DATA
        $this->loadData($id);

        // CONTROL ACCESS
        $this->ensureAccess(access::MAY_EDIT);

        
        $formErrors = array();

        if (($post = $this->input->post())&&isset($post["form_join_file"])) {
            if ($this->data->validateFileCreation($post,$this->user, true) !== FALSE ) {
                $this->show($id);
                $this->helpTopic = "show";
            } else {
                // errors when trying to validate data
                $formErrors = $this->data->getFileErrors("form_errors");
                $this->data->setFileValues($post);
                $actualFileData =  $this->data->getFileCreationData(access::MAY_EDIT, $this->user);
                $this->show($id);
                $this->template->content->newFileData = $actualFileData;
                $this->template->content->formErrors = $formErrors;
                $this->helpTopic = "show";
            }
        } else {
            url::redirect("interviewCopy/show/$id");
        }
    }

    public function getFile($id, $fileId) {
        // LOAD DATA
        $this->loadData($id);

        // FILE
        $file = ORM::factory('file', $fileId);

        // CONTROL ACCESS
        $this->ensureAccess(access::MAY_VIEW);
        if (!$this->data->has($file)) {
            $this->displayError("file not related to the copy");
        }

        download::force($file->path);
    }




}
?>