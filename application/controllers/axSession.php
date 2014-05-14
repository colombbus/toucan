<?php
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

class AxSession_Controller extends Toucan_Controller {

    public $auto_render = TRUE;
    protected $view = null;


    public function __construct() {
        parent::__construct();
        if ($this->auto_render) {
            // Render the template immediately after the controller method
            Event::add('system.post_controller', array($this, '_render'));
        }

    }

    protected function displayError($message = false) {
        // DO NOTHING
    }

    public function export($sessionName, $sessionId) {
        // CHECK ACCESS
        $this->dataName = $sessionName;
        $this->loadData($sessionId);
        $this->ensureAccess(access::MAY_EDIT);

        // GET SESSION DATA
        $sessionPrefix = "EXPORT_session_{$sessionId}";
        $ids= $this->session->get($sessionPrefix."_copies", null);
        $count = $this->session->get($sessionPrefix."_count", null);
        $processed = $this->session->get($sessionPrefix."_processed", null);
        $parameters = $this->session->get($sessionPrefix."_parameters", array());
        $fileName = $this->session->get($sessionPrefix."_fileName", array());
        $copyName = $this->session->get($sessionPrefix."_copyName","");
        
        $separator = $parameters['field_separator'];
        $rowSeparator = $parameters['copy_separator'];
        $boundary = $parameters['field_boundary'];
        $answerSeparator = $parameters['answer_separator'];
        $screen = ($parameters['format']==0);
        $iso = ($parameters['encoding']==0);
        $escaped = $parameters['escaped'];
        
        $endReached = false;
        
        // OPEN FILE
        $file = fopen ($fileName, 'a');
        
        // EXPORT COPIES
        $exportCount = Kohana::config("toucan.items_per_export");
        $sliceIds = array_slice($ids, $processed, $exportCount);
        $toProcess = sizeof($sliceIds);
        if ($toProcess ==0) {
            $endReached = true;
        } else {
            $copies = ORM::factory($copyName)->in('id', $sliceIds)->find_all();
            foreach($copies as $copy) {
                $row = "";
                if ($parameters['add_author'])
                    $row.=$boundary.text::escape($copy->owner->fullName, $escaped).$boundary.$separator;
                if ($parameters['add_date'])
                    $row.=$boundary.text::escape($copy->translated_created, $escaped).$boundary.$separator;
                $row.=$copy->export($separator, $boundary, $answerSeparator, $escaped, $parameters['private']);
                if ($parameters['add_state']) {
                    $row.=$separator.$boundary.text::escape($copy->state->getTranslatedName(), $escaped).$boundary;
                }
                $row.=$rowSeparator;
                if ($screen) {
                    $row = str_replace("\n","<br>",$row);
                } else if ($iso) {
                    $row = str_replace("\n","\r\n",utf8_decode($row));
                }
                fwrite($file, $row);
            }
        }
        
        // CLOSE FILE
        fclose($file);
        
        // UPDATE SESSION DATA
        $processed+=$toProcess;
        $this->session->set_flash($sessionPrefix."_processed",$processed);
        $this->session->keep_flash();
        
        // SEND VIEW
        $this->view=new View("session/export_progress");
        $this->view->fetchRequired = !$endReached;
        $progress  = intval($processed*100/$count);
        $this->view->progress = $progress;
    }

    /**
    * Render the loaded template.
    */
    public function _render() {
        if ($this->auto_render) {
            // Render the template when the class is destroyed
            $this->view->render(TRUE);
        }
    }
}
?>

