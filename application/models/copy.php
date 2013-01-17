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

abstract class Copy_Model extends Toucan_Model {

    const SHORT_VALUE_MAX_LENGTH = 127;
    const LONG_VALUE_MAX_LENGTH = 500;
    const TEXT_MAX_LENGTH = 65536;

    protected $table_name = "copies";
    protected $belongs_to = array('session', 'owner' => 'user');
    protected $has_many = array('answers');
    protected $has_one = array('state'=>'copyState','summary'=>'textValue');
    protected $values = array();
    protected $errors = array();
    protected $valuesSetFromPost = false;
    protected $has_and_belongs_to_many = array('files');

    protected abstract function saveAnswers();
    protected abstract function validateAnswers(array & $array, &$user, $sessionId = null);

    public function count(& $filter , & $user, $constraintId = null) {
        return $this->countVisibleItems($filter , $user, array('session_id' => $constraintId));
    }

    public function getItems(& $filter,& $user,$offset = 0, $number = null, $constraintId = null) {
        return $this->getVisibleItems($filter , $user, $offset, $number, array('session_id' => $constraintId));
    }

    public function isEditable() {
        // a copy is editable if the corresponding session is editable and is not in state "over"
        return ($this->session->isEditable()&&($this->session->state_id != SessionState_Model::OVER));
    }

    public function isEditableBy(& $user, $testEditable = true) {
        if ($this->loaded) {
            if ($testEditable&&!$this->isEditable())
                return false;
            $session = $this->session;
            if ($session->isEditableBy($user))
                return true;
            if (isset($user)) {
                if ($this->isOwner($user) && ($this->state_id ==  CopyState_Model::GOING_ON))
                    return true;
                if ($user->isAdmin())
                    return true;
            }
        }
        return false;
    }

    public function isViewableBy(& $user) {
        if ($this->loaded) {
            $session = $this->session;
            return $session->isViewableBy($user);
        }
        return false;
    }

    public function mayBeContributedBy(& $user) {
        if ($this->loaded) {
            $session = $this->session;
            return $session->mayBeContributedBy($user);
        }
        return false;
    }

    public function __get($column) {
        if ($column == 'session') {
            // retrieve a formSession or an InterviewSession
            if (isset($this->session_id)) {
                $session_id = $this->session_id;
                $result = $this->db->query("SELECT type from sessions WHERE id = $session_id");
                $result->result(false,MYSQL_NUM);
                switch ($result[0][0]) {
                    case 1:
                        return new FormSession_Model($session_id);
                        break;
                    case 2:
                        return new InterviewSession_Model($session_id);
                        break;
                }
            }
            return null;
        } else if ($column == 'translated_created') {
            return Utils::translateTimestamp($this->created);
        } else if ($column == 'owner_name') {
            if (isset($this->owner_id)&&$this->owner_id>0)
                return $this->owner->fullName;
            else {
                if (isset($this->ip_address)&&strlen($this->ip_address>0))
                    return sprintf(Kohana::lang('copy.public_with_ip'), $this->ip_address);
                else 
                    return Kohana::lang('copy.public');
            }
        }
        return parent::__get($column);
    }

    protected function countVisibleItems(& $filter , & $user, $constraints = null) {
        if (isset($user)&&$user->isAdmin()) {
            return parent::countVisibleItems($filter , $user, $constraints);
        } else {
            $publicGroup = Group_Model::SPECIAL_GROUP_PUBLIC;
            $registeredGroup = Group_Model::SPECIAL_GROUP_REGISTERED;
            if (isset($user)) {
                $userGroups = $user->getGroups();
                if (strlen($userGroups)>0)
                    $userGroups = ",".$userGroups;
                $query = "select distinct $this->table_name.id from $this->table_name, sessions where (sessions.id = $this->table_name.session_id) and (((sessions.view_id in ($publicGroup, $registeredGroup $userGroups) or sessions.owner_id=$user->id) and ($this->table_name.state_id IN (".CopyState_Model::PUBLISHED.", ".CopyState_Model::MANAGED.", ".CopyState_Model::MARKED."))) or ($this->table_name.owner_id = $user->id))";
            } else {
                $query = "select distinct $this->table_name.id from $this->table_name, sessions where ((sessions.id = $this->table_name.session_id) and (sessions.view_id=$publicGroup) and ($this->table_name.state_id IN (".CopyState_Model::PUBLISHED.", ".CopyState_Model::MANAGED.", ".CopyState_Model::MARKED.")))";
            }
            // Add constraints
            if (isset($constraints)) {
                foreach ($constraints as $constraint => $value)
                    $query.="and ($this->table_name.$constraint='$value') ";
            }

            // Add Where filter
            if (isset ($filter)) {
                if ($query2 = $filter->getSQLWhere($this->table_name)) {
                    $query .= " and $query2";
                }
            }
            $result = $this->db->query($query);
            return $result->count();
        }
    }

    protected function getVisibleItems($filter , $user, $offset, $number, $constraints = null) {
        if (isset($user)&&$user->isAdmin()) {
            return parent::getVisibleItems($filter , $user, $offset, $number, $constraints);
        } else {
            $publicGroup = Group_Model::SPECIAL_GROUP_PUBLIC;
            $registeredGroup = Group_Model::SPECIAL_GROUP_REGISTERED;
            if (isset($user)) {
                $userGroups = $user->getGroups();
                if (strlen($userGroups)>0)
                    $userGroups = ",".$userGroups;
                $query = "select distinct $this->table_name.* from $this->table_name, sessions where (sessions.id = $this->table_name.session_id) and (((sessions.view_id in ($publicGroup, $registeredGroup $userGroups) or sessions.owner_id=$user->id) and ($this->table_name.state_id IN (".CopyState_Model::PUBLISHED.", ".CopyState_Model::MANAGED.", ".CopyState_Model::MARKED."))) or ($this->table_name.owner_id = $user->id))";
            } else {
                $query = "select distinct $this->table_name.* from $this->table_name, sessions where ((sessions.id = $this->table_name.session_id) and (sessions.view_id=$publicGroup) and ($this->table_name.state_id IN (".CopyState_Model::PUBLISHED.", ".CopyState_Model::MANAGED.", ".CopyState_Model::MARKED.")))";
            }

            // Add constraints
            if (isset($constraints)) {
                foreach ($constraints as $constraint => $value)
                    $query.="and ($this->table_name.$constraint='$value') ";
            }

            // Add filters
            if (isset($filter)) {
                if ($query2 = $filter->getSQLWhere($this->table_name)) {
                    $query .= "and $query2";
                }
                $query.=$filter->getSQLOrder($this->table_name);
            }
            if (isset($number))
                $query.=" limit $offset, $number";
            $result = $this->db->query($query);
            if ($result->count()>0)
                return new ORM_Iterator($this,$result);
            else
                return array(); // empty array
        }
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
            $this->session->evaluation->clearCache();
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
            $this->session->evaluation->clearCache();
            // notification
            if ($this->state_id == CopyState_Model::PUBLISHED) {
                $this->session->notify($this->copyName, $this->id);
            }
        }
        return true;
    }

    protected function getQuestions($sessionId = null, &$user = null, $includePrivate = null) {
        if (isset($sessionId)) {
            $session = ORM::factory($this->sessionName, $sessionId);
        } else {
            $session = $this->session;
        }
        $template = $session->template;
        if (!isset($includePrivate))
            $includePrivate = $session->isEditableBy($user);
        return $template->getQuestions($includePrivate);
    }

    protected function getAnswers($questionId) {
        return ORM::factory('answer')->where('question_id', $questionId)->where('copy_id', $this->id)->find_all();
    }

    protected function getAnswer($questionId, $choiceId = null) {
        if (isset ($choiceId))
            $answers = ORM::factory('answer')->where('question_id', $questionId)->where('copy_id', $this->id)->where('choice_id', $choiceId)->find_all();
        else
            $answers  = $this->getAnswers($questionId);
        if ($answers->valid()) {
            return $answers->current();
        }
        $answer = ORM::factory('answer');
        $answer->question_id = $questionId;
        $answer->copy_id = $this->id;
        if (isset($choiceId)) {
            $answer->choice_id = $choiceId;
        }
        return $answer;
    }

    public function getErrors($lang_file=null) {
        $result = array();
        $prefix = "";
        if (isset($lang_file)) {
            $prefix = $lang_file.".";
        }
        foreach ($this->errors as $key=>$value) {
            $result[$key] = Kohana::lang($prefix.$value);
        }
        return $result;
    }

    public function delete() {
        // first: delete answers
        $questions = $this->getQuestions();
        foreach ($questions as $question) {
            $answers = $this->getAnswers($question->id);
            foreach ($answers as $answer) {
                $answer->delete();
            }
        }
        // second: delete session cache
        $this->session->clearCache();
        
        // third: delete copy
        parent::delete();
    }


    public function setValues(& $array) {
        $sessionId = null;
        if (isset ($array['session_id'])) {
            $sessionId = $array['session_id'];
        } else if (!$this->loaded) {
            return false;
        }
        
        $questions = $this->getQuestions($sessionId);
        $this->values = array();
        foreach ($questions as $question) {
            if (isset($array['question_'.$question->id])) {
                $this->values[$question->id] = $array['question_'.$question->id];
            }
        }
        $this->valuesSetFromPost = true;
    }

    public function getPreviousId() {
        $query = "select id from $this->table_name where created < $this->created and session_id = $this->session_id order by created desc limit 0,1";
        $result = $this->db->query($query);
        if ($result->count()>0) {
            return $result[0]->id;
        }
        return null;
    }

    public function getNextId() {
        $query = "select id from $this->table_name where created > $this->created and session_id = $this->session_id order by created asc limit 0,1";
        $result = $this->db->query($query);
        if ($result->count()>0) {
            return $result[0]->id;
        }
        return null;
    }

    public function export($separator, $boundary, $answerSeparator,& $escaped, $includePrivate) {
        $fakeUser = null;
        $questions = $this->getQuestions(null, $fakeUser, $includePrivate);
        if (isset($this->summary_id)) {
            $buffer = $boundary.text::escape($this->summary->value, $escaped).$boundary.$separator;
        } else {
            $buffer = "";
        }
        foreach ($questions as $question) {
            if (!$question->isSeparator()) {
                $questionBuffer = "";
                $answers = $this->getAnswers($question->id);
                if ($answers->count()>0) {
                    foreach ($answers as $answer) {
                        $questionBuffer .= $answer->getValue().$answerSeparator;
                    }
                    $questionBuffer = substr($questionBuffer, 0, strlen($questionBuffer)-1);
                }
                $buffer .= $boundary.text::escape($questionBuffer, $escaped).$boundary.$separator;
            }
        }
        return substr($buffer, 0, strlen($buffer)-1);
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
    
    public function isPublished() {
        if (!isset($this->state_id)) {
            return false;
        }
        return in_array($this->state_id, CopyState_Model::getPublishedStates());
    }

}
?>