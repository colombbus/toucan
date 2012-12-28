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

abstract class ToucanTree_Model extends ORM_Tree {

    protected $validation = null;
    protected $accessHeirs = null;

    public abstract function getCreationData($access, & $user, & $parameters = null);
    public abstract function getEditableData($access, & $user);
    public abstract function getDisplayableData($access, & $user = null);
    public abstract function validateEdition(array & $array,& $user, $save = FALSE);
    public abstract function validateCreation(array & $array,& $user, $save = FALSE);
    public abstract function count(& $filter , & $user, $constraintId = null);
    public abstract function getItems(& $filter = null,$parentId = 0, & $user = null,$offset = 0, $number = null, $constraintId = null);

    protected function hasOwner() {
        return (array_key_exists('owner',$this->belongs_to));
    }

    protected function hasView() {
        return (array_key_exists('view',$this->has_one));
    }

    protected function hasEdit() {
        return (array_key_exists('edit',$this->has_one));
    }

    protected function hasContribute() {
        return (array_key_exists('contribute',$this->has_one));
    }

    public function isViewableBy(& $user) {
        if ($this->hasView()) {
            if ($this->hasOwner()) {
                $ownerId = $this->owner->id;
            } else {
                $ownerId = null;
            }
            return DataAccess::checkAccess($user, $this->view, $ownerId);
        }
        return false;
    }
    public function isEditableBy(& $user) {
        if ($this->hasEdit()) {
            if ($this->hasOwner()) {
                $ownerId = $this->owner->id;
            } else {
                $ownerId = null;
            }
            return DataAccess::checkAccess($user, $this->edit, $ownerId);
        }
        return false;
    }

    public function mayBeContributedBy(& $user) {
        if ($this->hasContribute()) {
            if ($this->hasOwner()) {
                $ownerId = $this->owner->id;
            } else {
                $ownerId = null;
            }
            return DataAccess::checkAccess($user, $this->contribute, $ownerId);
        }
        return false;
    }

    public function isOwner(& $user) {
        return ($this->hasOwner())&&isset($user)&&($this->owner->id == $user->id);
    }

    public function setOwner(& $user, $save = false) {
        if ($this->hasOwner()) {
            $this->owner_id = $user->id;
            if ($save)
                $this->save();
        }
    }

    public function getErrors($lang_file=null) {
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

    public function setValues(& $array) {
        $this->load_values($array);
    }

    public function uniqueName($name) {
        $escapedName = addslashes($name);
        if ($this->loaded) {
            $other = $this->db->query("SELECT id from ".$this->table_name." WHERE id != $this->id AND name = '$escapedName'");
        } else {
            $other = $this->db->query("SELECT id from ".$this->table_name." WHERE name = '$escapedName'");
        }
        return !($other->count() > 0);
    }

    public function keepOldValue($name) {
        if (strlen($name)==0) {
            if (isset($this->$name))
                return $this->$name;
        }
        return $name;
    }

    public function getDisplayGroupInfo() {
        // find value from the element itself
        if ($this->view_id == 0) {
            return array('value'=>$this->owner->fullName, 'link'=> '/user/profile/'.$this->owner_id);
        } else {
            return array('value'=> $this->view->name, 'link'=> '/group/show/'.$this->view_id);
        }
    }

    public function getDisplayGroupId() {
        // find id from the element itself
        return $this->view_id;
    }

    public function getEditGroupInfo() {
        // find value from the element itself
        if ($this->edit_id == 0) {
            return array('value'=> $this->owner->fullName, 'link'=> '/user/profile/'.$this->owner_id);
        } else {
            return array('value'=> $this->edit->name, 'link'=> '/group/show/'.$this->edit_id);
        }
    }

    public function getEditGroupId() {
        // find id from the element itself
        return $this->edit_id;
    }


    public function getAccessOwnerId() {
        // find id from the element itself
        return $this->owner_id;
    }


    protected function addEditableGroups(& $data, $prefix) {
        $rawGroups = Group_Model::getProtectedGroups();
        $protectedGroups = array();
        $protectedGroups[0] = Kohana::lang("$prefix.self");
        foreach ($rawGroups as $group) {
            $protectedGroups[$group->id] = $group->name;
        }
        $group = ORM::factory("group");
        $rawGroups = $group->getItems($filter, $user, 0, null);
        $availableGroups = array();
        foreach ($rawGroups as $group) {
            $availableGroups[$group->id] = $group->name;
        }
        $viewGroups = $protectedGroups + $availableGroups;
        // remove "Public access" for edition
        unset ($protectedGroups[1]);
        $editGroups = $protectedGroups + $availableGroups;
        $data[] = array ('type' => 'select','name' => 'view_id','label' => "$prefix.may_view",'required'=>'1', 'values' => $viewGroups, 'value'=>$this->view_id);
        $data[] = array ('type' => 'select','name' => 'edit_id','label' => "$prefix.may_edit",'required'=>'1', 'values' => $editGroups, 'value'=>$this->edit_id);
    }

    protected function addDisplayableGroups(& $data, $prefix) {
        $data[] = array_merge(array('type' => 'link', 'label' => "$prefix.may_view"), $this->getDisplayGroupInfo());
        $data[] = array_merge(array('type' => 'link', 'label' => "$prefix.may_edit"), $this->getEditGroupInfo());
    }


    public function updateAccessControl() {
        if (isset($this->accessHeirs)){
            foreach ($this->accessHeirs as $heirGroup) {
                $elements = $this->$heirGroup;
                foreach ($elements as $heir) {
                    $heir->updateAccessControl();
                }
            }
        }
    }

    public function buildVisibleQuery(& $user, & $query, $tableName = null) {
        $query['from'][] = $this->table_name;
        $ownerField = "owner_id";
        if (isset($tableName)) {
            $query['where'][] = "$tableName.".$this->object_name."_id = $this->table_name.id";
        }

        $ownerGroup = Group_Model::SPECIAL_GROUP_OWNER;
        $publicGroup = Group_Model::SPECIAL_GROUP_PUBLIC;
        $registeredGroup = Group_Model::SPECIAL_GROUP_REGISTERED;
        if (isset($user)) {
            $userGroups = $user->getGroups();
            if (strlen($userGroups)>0)
                $userGroups = ",".$userGroups;

            $query['where'][] = "$this->table_name.view_id in ($publicGroup, $registeredGroup $userGroups) or
                                $this->table_name.$ownerField=$user->id or
                                 $this->table_name.edit_id in ($registeredGroup $userGroups)";
        }
        else
            $query['where'][] = "$this->table_name.view_id=$publicGroup";

    }

    public function save() {
        parent::save();
        $this->updateAccessControl();
    }

}
?>