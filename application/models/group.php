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

class Group_Model extends Toucan_Model {

    protected $belongs_to = array('owner' => 'user');
    protected $has_and_belongs_to_many = array('users');

    const SPECIAL_GROUP_OWNER = 0;
    const SPECIAL_GROUP_PUBLIC = 1;
    const SPECIAL_GROUP_REGISTERED = 2;

    public function validateEdition(array & $array,& $user, $save = FALSE) {
        $this->checkBooleans($array);
        $this->buildValidation($array);
        return parent::validate($this->validation, $save);
    }

    public function validateCreation(array & $array,& $user, $save = FALSE) {
        $this->checkBooleans($array);
        $this->buildValidation($array);
        if (parent::validate($this->validation, false)) {
            $this->setOwner($user, false);
            $this->created = time();
            if ($save)
                $this->save();
            return true;
        }
        return false;
    }

    protected function buildValidation(array & $array) {
        $this->validation = Validation::factory($array)
            ->pre_filter('trim')
            ->add_rules('name', 'required', array($this, 'uniqueName'), 'length[1,127]')
            ->add_rules('description', 'length[0,500]')
            ->add_rules('active', 'in_array[0,1]');
    }

    public function isActive() {
        return $this->active;
    }

    public function isProtected() {
        return ($this->id == 1)|($this->id == 2);
    }

    public function setActive($value, $save=false) {
        if (!($this->loaded)||($value != $this->isActive())) {
            if ($value) {
                $this->active = 1;
            } else {
                $this->active = 0;
            }
            if ($save)
                $this->save();
        }
    }

    public function getDisplayableData($access, & $user = null) {
        $displayableData = array();
        if ($this->isActive()) {
            $value = Kohana::lang('user.yes');
        } else {
            $value = Kohana::lang('user.no');
        }
        $displayableData[] = array ('type' => 'text', 'label' => 'group.active', 'value'=> $value);
        $displayableData[] = array ('type' => 'separator');
        // NAME & DESCRIPTION
        $displayableData[] = array ('type' => 'text', 'label' => 'group.name', 'value'=> $this->name);
        $displayableData[] = array ('type' => 'long_text', 'label' => 'group.description', 'value'=> $this->description);
        if ($access == access::ADMIN) {
            // OWNER
            $ownerName = $this->owner->firstname." ".$this->owner->name;
            $displayableData[] = array ('type' => 'link', 'label' => 'group.owner', 'value'=> $ownerName, 'link'=> '/user/profile/'.$this->owner->id);
            // Creation date
            $displayableData[] = array ('type' => 'text', 'label' => 'group.creation_date', 'value'=> Utils::translateTimestamp($this->created));


        }
        return $displayableData;
    }

    public function getEditableData($access, & $user) {
        $editableData = array();
        $editableData[] = array ('type' => 'text','name' => 'name','label' => 'group.name','required'=>'1', 'value' => $this->name);
        $editableData[] = array ('type' => 'long_text','name' => 'description','label' => 'group.description','required'=>'0', 'value' => $this->description);
        $editableData[] = array ('type' => 'check','name' => 'active','label' => 'group.active','checked' => $this->isActive());
        return $editableData;
    }

    public function getCreationData($access, & $user, & $parameters = null) {
        return $this->getEditableData($access, $user);
    }

    public static function getProtectedGroups() {
        $groups = ORM::factory('group');
        $groups->orderby('id');
        $groups->in('id', array(1,2));
        return $groups->find_all();
    }

    public function getItems(& $filter,& $user,$offset = 0, $number = null, $constraintId = null) {
        // $user->isAdmin() must be the first test, because
        // following operations affect all ORM objects
        // (since db 'default' instance is shared)
        if (isset($user)&&!$user->isAdmin())
            $this->where('owner_id',$user->id);
        if (isset($filter))
            $filter->add($this);
        else
            $this->orderby('name', 'ASC'); // by default, order by name
        if (isset($number))
            $this->limit($number, $offset);
        $this->notin('id', array(1,2)); // remove protected groups
        return $this->find_all();
    }

    public function count(& $filter , & $user, $constraintId = null) {
        // $user->isAdmin() must be the first test, because
        // following operations affect all ORM objects
        // (since db 'default' instance is shared)
        if (!($user->isAdmin()))
            $this->where('owner_id',$user->id);
        if (isset($filter))
            $filter->add($this);
        $this->notin('id', array(1,2)); // remove protected groups
        return $this->count_all();
    }

    public function load_values(array $values) {
        parent::load_values($values);
        if ($this->id==1) {
            // all users
            $this->name = Kohana::lang('group.all');
        } else if ($this->id==2) {
            // registered users
            $this->name = Kohana::lang('group.registered');
        }
        return $this;
    }

    public function isViewableBy(& $user) {
        return $this->isOwner($user);
    }

    public function isEditableBy(& $user) {
        return $this->isOwner($user);
    }

    protected function checkBooleans(& $array) {
        if (!isset($array['active']))
            $array['active']=0;
    }

    public function delete() {
        if ($this->loaded) {
            // 1st: replace this group everywhere by special group "owner"
            $activities = ORM::factory('activity')->where('view_id', $this->id)->find_all();
            foreach ($activities as $activity) {
                $activity->view_id= self::SPECIAL_GROUP_OWNER;
                $activity->save();
            }
            $activities = ORM::factory('activity')->where('edit_id', $this->id)->find_all();
            foreach ($activities as $activity) {
                $activity->edit_id= self::SPECIAL_GROUP_OWNER;
                $activity->save();
            }
            $evaluations = ORM::factory('evaluation')->where('view_id', $this->id)->find_all();
            foreach ($evaluations as $evaluation) {
                $evaluation->view_id= self::SPECIAL_GROUP_OWNER;
                $evaluation->save();
            }
            $evaluations = ORM::factory('evaluation')->where('edit_id', $this->id)->find_all();
            foreach ($evaluations as $evaluation) {
                $evaluation->edit_id= self::SPECIAL_GROUP_OWNER;
                $evaluation->save();
            }
            $sessions = ORM::factory('formSession')->where('view_id', $this->id)->find_all();
            foreach ($sessions as $session) {
                $session->view_id= self::SPECIAL_GROUP_OWNER;
                $session->save();
            }
            $sessions = ORM::factory('formSession')->where('contribute_id', $this->id)->find_all();
            foreach ($sessions as $session) {
                $session->contribute_id= self::SPECIAL_GROUP_OWNER;
                $session->save();
            }
            $sessions = ORM::factory('interviewSession')->where('view_id', $this->id)->find_all();
            foreach ($sessions as $session) {
                $session->view_id = self::SPECIAL_GROUP_OWNER;
                $session->save();
            }
            $sessions = ORM::factory('interviewSession')->where('contribute_id', $this->id)->find_all();
            foreach ($sessions as $session) {
                $session->contribute_id = self::SPECIAL_GROUP_OWNER;
                $session->save();
            }
            $indicators = ORM::factory('indicator')->where('view_id', $this->id)->find_all();
            foreach ($indicators as $indicator) {
                $indicator->view_id = self::SPECIAL_GROUP_OWNER;
                $indicator->save();
            }
            $indicators = ORM::factory('indicator')->where('edit_id', $this->id)->find_all();
            foreach ($indicators as $indicator) {
                $indicator->edit_id = self::SPECIAL_GROUP_OWNER;
                $indicator->save();
            }
            $templates = ORM::factory('formTemplate')->where('view_id', $this->id)->find_all();
            foreach ($templates as $template) {
                $template->view_id = self::SPECIAL_GROUP_OWNER;
                $template->save();
            }
            $templates = ORM::factory('formTemplate')->where('edit_id', $this->id)->find_all();
            foreach ($templates as $template) {
                $template->edit_id = self::SPECIAL_GROUP_OWNER;
                $template->save();
            }
            $templates = ORM::factory('interviewTemplate')->where('view_id', $this->id)->find_all();
            foreach ($templates as $template) {
                $template->view_id = self::SPECIAL_GROUP_OWNER;
                $template->save();
            }
            $templates = ORM::factory('interviewTemplate')->where('edit_id', $this->id)->find_all();
            foreach ($templates as $template) {
                $template->edit_id = self::SPECIAL_GROUP_OWNER;
                $template->save();
            }
            // 2nd: delete the group
            parent::delete();
        }
    }


}
?>