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

abstract class Session_Model extends Toucan_Model {

    protected $table_name = "sessions";
    protected $belongs_to = array('owner'=>'user', 'evaluation','style', 'activity');
    protected $has_one = array('view' => 'group', 'edit' => 'group', 'contribute' => 'group', 'state' =>'sessionState','template');
    protected $templateName = null;
    protected $instanceName = null;
    protected $ignored_columns = array('templateName','public_access','original_template_id');
    protected $indicatorModel = "indicator";
    protected $exportParameters = array();
    protected $copyName = "";
    protected $accessParent = "evaluation";
    protected $parentId = "evaluation_id";
    protected $accessPrefix = "session";
    protected $templateIndicatorModel = null;

    abstract function mayBeEditedByPublic();
    abstract function getCopies();

    public function getCreationData($access, & $user, & $parameters = null) {
        $admin = ($access == access::ADMIN);
        $owner = ($access == access::OWNER);
        if (isset ($parameters['template_id'])) {
            $original_template = ORM::factory($this->templateName, $parameters['template_id']);
            if (strlen($this->name)==0)
                $this->name = $original_template->name;
            if (strlen($this->description)==0)
                $this->description = $original_template->description;
        }
        $parentId = $this->parentId;
        if (isset ($parameters[$parentId])) {
            // Initialize inherited access control
            $this->$parentId = $parameters[$this->parentId];
            if (!$this->valuesSet) {
                $this->inherit = 1;
                $accessParent = $this->accessParent;
                $parent = $this->$accessParent;
                $this->view_id = $parent->getDisplayGroupId();
                $this->edit_id = $parent->getEditGroupId();
            }
        }
        if (!$this->valuesSet)
            $this->language = language::getCurrentLanguage();
        $this->template_id = 0;
        return $this->getEditableData(access::OWNER, $user);
    }

    public function getEditableData($access, & $user) {
        $admin = ($access == access::ADMIN);
        $owner = ($access == access::OWNER);
        $editableData = array();
        $editableData[] = array ('type' => 'text','name' => 'name','label' => 'session.name','required'=>'1', 'value' => $this->name);
        $editableData[] = array ('type' => 'long_text','name' => 'description','label' => 'session.description','value' => $this->description);
        $states = SessionState_Model::getTranslatedStates();
        $editableData[] = array ('type' => 'select','name' => 'state_id','label' => 'session.state','required'=>'1', 'values' => $states, 'value'=>$this->state_id);
        if ($owner|$admin) {
            $editableData[] = array ('type' => 'separator');
            $this->addEditableGroups($editableData, $this->accessPrefix);
            if ($this->mayBeEditedByPublic()) {
                $editGroups = end($editableData);
                $currentIndex = sizeOf($editableData)-1;
                $editableData[$currentIndex] = array ('type' => 'check','name' => 'public_access','label' => $this->sessionName.'.public_access','required'=>'1', 'checked'=>($this->contribute_id==1));
                $editGroups['hidden'] = ($this->contribute_id==1);
                $editableData[] = $editGroups;
                $editableData[] = array ('type' => 'check','name' => 'password_flag','label' => $this->sessionName.'.password_flag','checked'=>$this->password_flag,'hidden'=>($this->contribute_id!=1));
                $editableData[] = array ('type' => 'text','name' => 'password','label' => $this->sessionName.'.password','value'=>$this->password, 'disabled'=>!$this->password_flag, 'hidden'=>($this->contribute_id!=1));
                $styles = ORM::factory("style");
                $filter = null;
                if ($styles->count($filter, $user)>0) {
                    $styles = $styles->getVisibleItems($filter, $user);
                    $templates = array();
                    $templates[0] = Kohana::lang('session.defaultStyle');
                    foreach ($styles as $item) {
                        $templates[$item->id] = $item->name;
                    }
                    $editableData[] = array ('type' => 'select','name' => 'style_id','label' => 'session.style','values' => $templates, 'value'=>$this->style_id, 'hidden'=>($this->contribute_id!=1));
                } else {
                    $editableData[] = array ('type' => 'hidden','name' => 'style_id','value'=>$this->style_id);
                }
                $editableData[] = array ('type' => 'select','name' => 'language','label' => $this->sessionName.'.language','values'=>language::getAvailableLanguages(), 'value'=>$this->language, 'hidden'=>($this->contribute_id!=1));
            } 
        }
        if ($this->loaded) {
            $ownerUser = $this->owner;
        } else {
            $ownerUser = $user;
        }
        $editableData[] = array ('type' => 'separator');
        $editableData[] = array ('type' => 'select','name' => 'notification','label' => 'session.notification','values' => notification::getSelection($ownerUser->fullName), 'value'=>$this->notification);
        $editableData[] = array ('type' => 'text','name' => 'email','label' => 'session.notification_address','value'=>$this->email, 'hidden'=>!notification::requiresAddress($this->notification));
        if ($this->loaded) {
            $parentId = $this->parentId;
            $editableData[] = array ('type' => 'hidden','name' => $this->parentId,'value'=>$this->$parentId);
            $editableData[] = array ('type' => 'hidden','name' => 'template_id','value'=>$this->template_id);
        }
        return $editableData;
    }

    public function getDisplayableData($access, & $user = null) {
        $admin = ($access == access::ADMIN);
        $owner = ($access == access::OWNER);
        $mayEdit = ($access == access::MAY_EDIT);
        $displayableData = array();
        // NAME, DESCRIPTION & STATE
        $displayableData[] = array ('type' => 'text', 'label' => 'session.name', 'value'=> $this->name);
        $displayableData[] = array ('type' => 'long_text', 'label' => 'session.description', 'value'=> $this->description);
        $displayableData[] = array ('type' => 'text', 'label' => 'session.state', 'value'=> $this->state->getTranslatedName());

        // GROUPS
        if ($owner|$admin) {
            $displayableData[] = array ('type' => 'separator');
            $this->addDisplayableGroups($displayableData, $this->accessPrefix);
        }

        // PUBLIC URL
        if ($this->mayBeEditedByPublic()&&($this->contribute_id == 1)) {
            $link = $this->getPublicUrl();
            $displayableData[] = array ('type' => 'separator');
            $displayableData[] = array ('type' => 'link', 'label' => $this->sessionName.'.url', 'value'=> html::url($link), 'link'=>$link);
            if ($this->password_flag) {
                $displayableData[] = array ('type' => 'text', 'label' => $this->sessionName.'.password', 'value'=> $this->password);
            }
            if ($this->style_id>0) {
                if (DataAccess::testAccess($this->style, $user, access::MAY_VIEW))
                    $displayableData[] = array ('type' => 'link', 'label' => 'session.style', 'value'=> $this->style->name, 'link'=>'style/show/'.$this->style_id);
                else
                    $displayableData[] = array ('type' => 'text', 'label' => 'session.style', 'value'=> $this->style->name);
            } else {
                $displayableData[] = array ('type' => 'text', 'label' => 'session.style', 'value'=> Kohana::lang('session.defaultStyle'));
            }
            $languages = language::getAvailableLanguages();
            if (isset($this->language) && isset($languages[$this->language]))
                $displayableData[] = array ('type' => 'text', 'label' => $this->sessionName.'.language', 'value'=> $languages[$this->language]);
        }
        // NOTIFICATION
        if ($owner|$admin|$mayEdit) {
            $displayableData[] = array ('type' => 'separator');
            $displayableData[] = array ('type' => 'text', 'label' => 'session.notification', 'value'=> notification::getText($this->notification, $this->owner->fullName, $this->email));
        }
        
        // ADMIN INFO
        if ($admin|$owner) {
            $displayableData[] = array ('type' => 'separator');
            // OWNER
            $displayableData[] = array ('type' => 'link', 'label' => 'session.owner', 'value'=> $this->owner->fullName, 'link'=> '/user/profile/'.$this->owner->id);
            // Creation date
            $displayableData[] = array ('type' => 'text', 'label' => 'session.creation_date', 'value'=> Utils::translateTimestamp($this->created));
        }
        return $displayableData;
    }

    protected function buildValidation(array & $array) {
        $this->validation = Validation::factory($array)
            ->pre_filter('trim')
            ->add_rules('name', 'required', 'length[1,127]')
            ->add_callbacks('name', array($this, 'uniqueNameByParent'))
            ->add_rules('description', 'length[0,10000]')
            ->add_rules('view_id', 'valid::numeric')
            ->add_rules('edit_id', 'valid::numeric')
            ->add_rules('contribute_id', 'valid::numeric')
            ->add_rules($this->parentId, 'required', 'valid::numeric')
            ->add_rules('template_id', 'valid::numeric')
            ->add_rules('state_id', 'valid::numeric')
            ->add_rules('style_id', 'valid::numeric')
            ->add_rules('inherit', 'in_array[0,1]')
            ->add_rules('password_flag', 'in_array[0,1]')
            ->add_rules('notification', 'valid::numeric')
            ->add_rules('email', 'valid::email', 'length[1,127]')
            ->add_rules('language', 'valid::alpha_numeric', 'length[0,2]');
        if (isset($array['password_flag'])) {
            if ($array['password_flag'])
                $this->validation->add_rules('password', 'required','length[5,50]');
            else
                $this->password = "";
        }
    }

    public function validateEdition(array & $array,& $user, $save = FALSE) {
        $this->type = $this->sessionType;
        $this->checkBooleans($array, $user);
        $this->buildValidation($array);
        $result = parent::validate($this->validation, $save);
        if ($result&&$save) {
            $this->save();
        }
        return $result;
    }

    public function validateCreation(array & $array,& $user, $save = FALSE) {
        $this->setOwner($user, false);
        if ($result = $this->validateEdition($array, $user, false)) {
            $this->type = $this->sessionType;
            $this->setOwner($user, false);
            $this->created = time();
            // Creation of the corresponding instance
            $template = null;
            
            if (isset($this->template_id)&&($this->template_id>0)) {
                // Session is linked to a template: create instance from template
                $template = ORM::factory($this->templateName,$this->template_id)->newInstance($user);
            } else {
                // No template: we create a new instance
                $template = ORM::factory($this->templateName);
                $template->type = $template->templateType;
                $template->setOwner($user, false);
                $template->created = time();
                $template->save();
            }
            $this->template_id = $template->id;
            if ($save) {
                $this->save();
            }
        }
        return $result;
    }

    protected function checkBooleans(& $array, &$user) {
        if ($user->isAdmin()||$this->isOwner($user)) {
            if (!isset($array['password_flag']))
                $array['password_flag']=0;
            if (!isset($array['public_access']))
                $array['public_access']=0;
            else if ($array['public_access']!=0)
                $array['contribute_id'] = 1;
            else
                $array['password_flag'] = 0;
            if (!isset($array['inherit']))
                $array['inherit']=0;
        }
    }

    public function count(& $filter , & $user, $constraints = null) {
        if (!isset($constraints))
            $constraints = array('type' => $this->sessionType);
        else
            $constraints = array_merge($constraints, array('type' => $this->sessionType));
        return $this->countVisibleItems($filter , $user, $constraints);
    }

    public function getItems(& $filter,& $user,$offset = 0, $number = null, $constraints = array()) {
        if (!isset($constraints))
            $constraints = array('type' => $this->sessionType);
        else
            $constraints = array_merge($constraints, array('type' => $this->sessionType));
        return $this->getVisibleItems($filter , $user, $offset, $number, $constraints);
    }

    public function uniqueNameByParent(Validation $valid) {
        if (array_key_exists('name', $valid->errors()))
            return;
        $parentId = $this->parentId;
        if (isset ($valid->$parentId)) {
            $escapedName = addslashes($valid->name);
            $value = $valid->$parentId;
            if ($this->loaded) {
                $other = $this->db->query("SELECT id from ".$this->table_name." WHERE id != $this->id AND $this->parentId = '$value' AND name = '$escapedName'");
            } else {
                $other = $this->db->query("SELECT id from ".$this->table_name." WHERE $this->parentId = '$value' AND name = '$escapedName'");
            }
            if ($other->count() > 0) {
                $valid->add_error( 'name', 'uniqueName');
            }
        }
    }

    protected function getPublicUrl(){
        if (($this->mayBeEditedByPublic())&&($this->loaded)&&($this->contribute_id==1)) {
            return "public/form/".$this->id;
        }
    }

    public function __get($column) {
        if ($column == 'template') {
            // retrieve a formTemplate or an InterviewTemplate
            if (isset($this->template_id) && $this->template_id>0) {
                return Template_Model::getTemplate($this->template_id);
            }
            return null;
        }
        if ($column == 'activity') {
            if (isset($this->activity_id)&&($this->activity_id==0)&&isset($this->evaluation_id)&&($this->evaluation_id>0)) {
                $evaluation = $this->evaluation;
                return $evaluation->activity;
            }
        }
        if ($column == 'indicators') {
            return ORM::factory('indicator')->where('session_id', $this->id)->orderby('order')->find_all();
        }
        return parent::__get($column);
    }

    public function isEditableBy(& $user, $testEditable = true) {
        if ($this->loaded) {
            if ($testEditable && !$this->isEditable())
                return false;
            return parent::isEditableBy($user);
        }
        return false;
    }

    public function isEditable() {
        // a session is editable if the corresponding evaluation is not in state "over"
        return ($this->evaluation->state_id != EvaluationState_Model::OVER);
    }

    public function isOpen() {
        return (($this->loaded)&&($this->state_id==SessionState_Model::GOING_ON));
    }

    public function isParentOpen() {
        return (($this->loaded)&&($this->evaluation->state_id==EvaluationState_Model::GOING_ON));
    }
    
    public function setExportParameters(& $array) {
        if (isset($array['add_author']))
            $this->exportParameters['add_author'] = 1;
        else
            $this->exportParameters['add_author'] = 0;
        if (isset ($array['add_date']))
            $this->exportParameters['add_date'] = 1;
        else
            $this->exportParameters['add_date'] = 0;
        if (isset ($array['add_headers']))
            $this->exportParameters['add_headers'] = 1;
        else
            $this->exportParameters['add_headers'] = 0;
        if (isset ($array['add_state']))
            $this->exportParameters['add_state'] = 1;
        else
            $this->exportParameters['add_state'] = 0;
        if (isset ($array['unpublished']))
            $this->exportParameters['unpublished'] = 1;
        else
            $this->exportParameters['unpublished'] = 0;
        if (isset ($array['private']))
            $this->exportParameters['private'] = 1;
        else
            $this->exportParameters['private'] = 0;
        if (isset ($array['field_separator']))
            $this->exportParameters['field_separator'] = $array['field_separator'];
        else
            $this->exportParameters['field_separator'] = ';';
        if (isset ($array['copy_separator']))
            $this->exportParameters['copy_separator'] = $array['copy_separator'];
        else
            $this->exportParameters['copy_separator'] = '\\n';
        if (isset ($array['answer_separator']))
            $this->exportParameters['answer_separator'] = $array['answer_separator'];
        else
            $this->exportParameters['answer_separator'] = ' ';
        if (isset ($array['field_boundary']))
            $this->exportParameters['field_boundary'] = $array['field_boundary'];
        else
            $this->exportParameters['field_boundary'] = "\"";
        if (isset ($array['format']))
            $this->exportParameters['format'] = $array['format'];
        else
            $this->exportParameters['format'] = 0;
        if (isset ($array['encoding']))
            $this->exportParameters['encoding'] = $array['encoding'];
        else
            $this->exportParameters['encoding'] = 0;
        if (isset ($array['date']))
            $this->exportParameters['date'] = 1;
        else
            $this->exportParameters['date'] = 0;
        if (isset ($array['start_date']))
            $this->exportParameters['start_date'] = $array['start_date'];
        else
            $this->exportParameters['start_date'] = '';
        if (isset ($array['end_date']))
            $this->exportParameters['end_date'] = $array['end_date'];
        else
            $this->exportParameters['end_date'] = '';
    }

    public function getExportEditableParameters(& $user) {
        $editableData = array();
        $editableData[] = array ('type' => 'check','name' => 'add_author','label' => 'session.export_add_author', 'checked'=>$this->exportParameters['add_author']);
        $editableData[] = array ('type' => 'check','name' => 'add_date','label' => 'session.export_add_date', 'checked'=>$this->exportParameters['add_date']);
        $editableData[] = array ('type' => 'check','name' => 'add_headers','label' => 'session.export_add_headers', 'checked'=>$this->exportParameters['add_headers']);
        $editableData[] = array ('type' => 'check','name' => 'add_state','label' => 'session.export_add_state', 'checked'=>$this->exportParameters['add_state']);
        $editableData[] = array ('type' => 'check','name' => 'unpublished','label' => 'session.export_unpublished', 'checked'=>$this->exportParameters['unpublished']);
        if ($this->hasPrivateQuestions($user))
            $editableData[] = array ('type' => 'check','name' => 'private','label' => 'session.export_private', 'checked'=>$this->exportParameters['private']);
        $editableData[] = array ('type' => 'text','name' => 'field_separator','label' => 'session.export_field_separator', 'value'=>$this->exportParameters['field_separator']);
        $editableData[] = array ('type' => 'text','name' => 'copy_separator','label' => 'session.export_copy_separator', 'value' => $this->exportParameters['copy_separator']);
        $editableData[] = array ('type' => 'text','name' => 'field_boundary','label' => 'session.export_field_boundary', 'value' => $this->exportParameters['field_boundary']);
        $editableData[] = array ('type' => 'text','name' => 'answer_separator','label' => 'session.export_answer_separator', 'value' => $this->exportParameters['answer_separator']);
        $formats = array();
        $formats[0] = Kohana::lang('session.export_format_screen');
        $formats[1] = Kohana::lang('session.export_format_csv');
        $formats[2] = Kohana::lang('session.export_format_text');
        $editableData[] = array ('type' => 'select', 'name' => 'format', 'label' => 'session.export_format', 'values' => $formats, 'value'=>$this->exportParameters['format']);
        $encodings = array();
        $encodings[0] = Kohana::lang('session.export_encoding_windows');
        $encodings[1] = Kohana::lang('session.export_encoding_utf8');
        $editableData[] = array ('type' => 'select', 'name' => 'encoding', 'label' => 'session.export_encoding', 'values' => $encodings, 'value'=>$this->exportParameters['encoding'], 'hidden'=>($this->exportParameters['format']==0));
        $editableData[] = array ('type' => 'check','name' => 'date','label' => 'session.export_date', 'checked'=>$this->exportParameters['date']);
        $editableData[] = array ('type' => 'date','name' => 'start_date','label' => 'session.export_start_date', 'value' => $this->exportParameters['start_date'], 'hidden'=>($this->exportParameters['date']==0));
        $editableData[] = array ('type' => 'date','name' => 'end_date','label' => 'session.export_end_date', 'value' => $this->exportParameters['end_date'], 'hidden'=>($this->exportParameters['date']==0));
        return $editableData;
    }

    public function validateExportParameters(array & $array) {
        if (!isset($array['date'])||($array['date']==0)) {
            $array['start_date'] = '';
            $array['end_date'] = '';
        }
        $this->validation = Validation::factory($array)
            ->pre_filter('trim')
            ->add_callbacks('start_date',array($this, 'validDate'))
            ->add_callbacks('end_date',array($this, 'validDate'))
            ->add_callbacks('end_date',array($this, 'endAfterStart'));
        $result = $this->validation->validate();
        if ($result) {
            // retrieve translated dates
            $this->exportParameters['start_date'] = Utils::timestamp_str2db($array['start_date'],Kohana::lang("calendar.format_check"));
            $this->exportParameters['end_date'] = Utils::timestamp_str2db($array['end_date'],Kohana::lang("calendar.format_check"));
        }
        return $result;
    }

    public function getExportParameters() {
        return $this->exportParameters;
    }

    public function setAutomaticSave(& $array, & $user) {
        if (isset($user)) {
            $savedCopies = $this->where('owner_id', $user->id)->where('state_id', CopyState_Model::AUTO_SAVED)->getCopies();
            if ($savedCopies->valid()) {
                $savedCopy = $savedCopies->current();
            } else {
                $savedCopy = ORM::factory($this->copyName);
            }
            $savedCopy->session_id = $this->id;
            $savedCopy->created = time();
            $savedCopy->owner_id = $user->id;
            $array['state_id'] = CopyState_Model::AUTO_SAVED;
            
            // validate and save even if there are errors
            $savedCopy->validateEdition($array , $user, true , true);
            return true;
        }
        return false;
    }

    public function clearAutomaticSave(& $user) {
        if (isset($user)) {
            $savedCopies = $this->where('owner_id', $user->id)->where('state_id', CopyState_Model::AUTO_SAVED)->getCopies();
            foreach ($savedCopies as $copy) {
                $copy->delete();
            }
        }
    }

    public function notify($copyName,$copyId) {
        if ($this->notification!=notification::NO_NOTIFICATION) {
            $from = array('toucan@colombbus.org',Kohana::lang('session.notification_message_from'));
            $subject = Kohana::lang($this->sessionName.'.notification_message_subject');
            $url = html::url("$copyName/show/$copyId");
            $message = sprintf(Kohana::lang($this->sessionName.'.notification_message_contents'), $this->name, $url,$url);
            switch ($this->notification) {
                case notification::NOTIFY_OWNER:
                    email::send($this->owner->email, $from, $subject, $message, true);
                    break;
                case notification::NOTIFY_MANAGERS:
                    $users = $this->edit->users;
                    foreach ($users as $user) {
                        email::send($user->email, $from, $subject, $message, true);
                    }
                    break;
                case notification::NOTIFY_OTHER:
                    email::send($this->email, $from, $subject, $message, true);
                    break;
            }
        }
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
        // questions
        $questions = $this->template->getQuestions($includePrivate);
        foreach ($questions as $question) {
            $question->exportInDocument();
        }
        // send document
        rtf::sendDocument(sprintf(Kohana::lang('session.export_document_name'), $this->name));
    }
    
    public function delete() {
        if ($this->loaded) {
            // 1st delete the associated template
            $this->template->delete();
            
            // 2nd delete the corresponding indicators, if any
            foreach ($this->indicators as $indicator)
                $indicator->delete();
        }
        // then delete the element itself
        parent::delete();
    }

    public function getIndicators(& $user) {
        $nullValue = null;
        return ORM::factory('indicator')->getItems($nullValue,$user,0, $nullValue, array('session_id'=>$this->id));
    }
    
    public function getIndicatorIds(& $user) {
        $indicators = $this->getIndicators($user);
        if (count($indicators)>0)
            return $indicators->primary_key_array();
        return array();
    }

    public function copyIndicators(& $indicatorsIds,& $user, & $variables) {
        foreach($indicatorsIds as $indicatorId) {
            $indicator = ORM::factory($this->templateIndicatorModel, $indicatorId);
            $parameters = array('indicator_model'=>$this->indicatorModel, 'session_id'=>$this->id);
            $indicator->copyTo($this->evaluation->id, $user, $parameters, $variables);
        }
    }
    
    public function isPublic() {
        return $this->mayBeEditedByPublic()&&isset($this->contribute_id)&&$this->contribute_id==1;
    }
    
    public function clearCache() {
        $this->evaluation->clearCache();
    }

    public function hasPrivateQuestions(& $user) {
        if ($this->isEditableBy($user))
            return $this->template->hasPrivateQuestions();
        else
            return false;
    }
    
    public function getDownloadTemplateParameters() {
        $optionData = array();
        $answers= array();
        $answers[] = array('label'=>Kohana::lang('session.include_private_yes'), 'value'=>1);
        $answers[] = array('label'=>Kohana::lang('session.include_private_no'), 'value'=>0);
        $optionData[] = array ('type' => 'choice','name' => 'private','label' => 'session.include_private_question','required'=>'1', 'values' => $answers, 'value'=>1);
        return $optionData;
    }
    
    public function getDownloadTemplateOption(& $array) {
        if (isset($array['private'])&&$array['private']==1)
            return true;
        else
            return false;
    }

}

?>