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

class AxInterviewCopy_Controller extends Ajax_Controller {

    public $auto_render = TRUE;
    protected $view = null;
    protected $dataName = "interviewCopy";


    public function showFile($copyId, $fileId) {
        // LOAD DATA
        $this->loadData($copyId);

        // FILE
        $file = ORM::factory('file', $fileId);

        // CHECK ACCESS
        $this->ensureAccess(access::MAY_VIEW);
        if (!$this->data->has($file)) {
            $this->displayError("file not related to the copy");
        }

        // LOAD FILE
        $file = ORM::factory('file', $fileId);

        $this->view=new View("copy/view_file");
        $this->view->fileData = $this->data->getFileDisplayableData(access::MAY_VIEW, $this->user, $fileId);
        $this->view->fileId = $fileId;
        $this->view->editFile = "interviewCopy.edit_file";
        $this->view->deleteFile = "interviewCopy.delete_file";
        
    }

    public function editFile($copyId, $fileId) {
        $this->loadData($copyId);

        // FILE
        $file = ORM::factory('file', $fileId);

        // CHECK ACCESS
        $this->ensureAccess(access::ADMIN);
        if (!$this->data->has($file)) {
            $this->displayError("file not related to the copy");
        }

        $formErrors = array();

        if (($post = $this->input->post())&&isset($post["form_edit_file_$fileId"])) {
            if ($this->data->validateFileEdition($post,$fileId) !== FALSE ) {
                self::showFile($copyId, $fileId);
                return;
            } else {
                // errors when trying to validate data
                $formErrors = $this->data->getFileErrors("form_errors");
                $this->data->setFileValues($post);
            }
        }

        $this->view=new View("copy/edit_file");
        $this->view->formId = "form_edit_file_$fileId";
        $this->view->formErrors = $formErrors;
        $this->view->fileData = $this->data->getFileEditableData(access::MAY_EDIT, $this->user, $fileId);
        $this->view->cancel = "button.cancel";
        $this->view->submit = "button.save";
        $this->view->fileId = $fileId;
    }

    public function deleteFile($copyId, $fileId) {
        // LOAD FILE DATA
        $this->loadData($copyId);

        // FILE
        $file = ORM::factory('file', $fileId);

        // CHECK ACCESS
        $this->ensureAccess(access::MAY_EDIT);
        if (!$this->data->has($file)) {
            $this->displayError("file not related to the copy");
        }

        // REMOVE LINK
        $this->data->remove($file);
        $this->data->save();

        // DELETE FILE
        $file->delete();

        $this->auto_render = false;
    }

}
?>