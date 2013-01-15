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

class Style_Model extends Toucan_Model {

    protected $belongs_to = array('owner'=>'user');
    protected $has_one = array('view' => 'group', 'edit' => 'group');
    protected $has_many = array('formSessions');
    protected $has_and_belongs_to_many = array('files');
    protected $ignored_columns = array('default_structure');
    protected $oldDirectory = null;


    public function getCreationData($access, & $user, & $parameters = null) {
        $data = $this->getEditableData(access::OWNER, $user);
        $defaultStructure = false;
        if (isset($this->default_structure)&& $this->default_structure == 1) {
            $defaultStructure = true;
        }
        $data[] = array ('type' => 'check','name' => 'default_structure','label' => 'style.default_structure', 'checked' => $defaultStructure);
        return $data;
    }

    public function getEditableData($access, & $user) {
        $admin = ($access == access::ADMIN);
        $owner = ($access == access::OWNER);
        $editableData = array();
        $filter = null;
        $editableData[] = array ('type' => 'text','name' => 'name','label' => 'style.name','required'=>'1', 'value' => $this->name);
        $editableData[] = array ('type' => 'long_text','name' => 'description','label' => 'style.description','value' => $this->description);
        $editableData[] = array ('type' => 'text','name' => 'directory','label' => 'style.directory','required'=>'1','value' => $this->directory);
        if ($owner|$admin) {
            $editableData[] = array ('type' => 'separator');
            $this->addEditableGroups($editableData, "style");
        }
        return $editableData;
    }

    public function getDisplayableData($access,& $user =null) {
        $admin = ($access == access::ADMIN);
        $owner = ($access == access::OWNER);
        $displayableData = array();
        // NAME & DESCRIPTION
        $displayableData[] = array ('type' => 'text', 'label' => 'style.name', 'value'=> $this->name);
        $displayableData[] = array ('type' => 'long_text', 'label' => 'style.description', 'value'=> $this->description);
        $displayableData[] = array ('type' => 'text', 'label' => 'style.directory', 'value'=> $this->directory);
        // GROUPS
        if ($owner|$admin) {
            $displayableData[] = array ('type' => 'separator');
            $this->addDisplayableGroups($displayableData, "style");
            $displayableData[] = array ('type' => 'separator');
            // OWNER
            $displayableData[] = array ('type' => 'link', 'label' => 'style.owner', 'value'=> $this->owner->fullName, 'link'=> '/user/profile/'.$this->owner->id);
            // Creation date
            $displayableData[] = array ('type' => 'text', 'label' => 'style.creation_date', 'value'=> Utils::translateTimestamp($this->created));
        }
        return $displayableData;
    }

    protected function keepOldValues(& $array) {
        if (!isset($array['view_id']))
            $array['view_id'] = $this->view_id;
        if (!isset($array['edit_id']))
            $array['edit_id'] = $this->edit_id;
        $this->oldDirectory = $this->directory;
    }


    public function validateEdition(array & $array,& $user, $save = FALSE) {
        $this->keepOldValues($array);
        $this->validation = Validation::factory($array)
            ->pre_filter('trim')
            ->add_rules('name', 'required', array($this, 'uniqueName'), 'length[1,127]')
            ->add_rules('description', 'length[0,500]')
            ->add_rules('directory', 'required', array($this, 'uniqueDirectory'), 'length[1,127]', 'valid::alpha_dash')
            ->add_rules('view_id', 'valid::numeric')
            ->add_rules('default_structure', 'in_array[0,1]')
            ->add_rules('edit_id', 'valid::numeric');
        $result = parent::validate($this->validation, $save);
        if ($this->loaded && $result && strcmp($this->directory, $this->oldDirectory)!=0) {
            // directory has changed : rename it
            rename(DOCROOT.Kohana::config('toucan.files_directory')."/".Kohana::config('toucan.templates_directory')."/".$this->oldDirectory, $this->getAbsoluteDirectory());
            $this->updateFiles();
        }
        return $result;
    }

    public function validateCreation(array & $array,& $user, $save = FALSE) {
        $result = $this->validateEdition($array, $user, false);
        if ($result) {
            $this->setOwner($user, false);
            $this->created = time();
            if ($save)
                $this->save();
            $this->checkDirectory();
            if (isset($array['default_structure']) && $array['default_structure']==1) {
                $this->createDefaultStructure();
                $this->updateFiles();
            }
        }
        return $result;
    }

    public function count(& $filter , & $user, $constraintId = null) {
        return $this->countVisibleItems($filter , $user);
    }

    public function getItems(& $filter,& $user,$offset = 0, $number = null, $constraintId = null) {
        return $this->getVisibleItems($filter , $user, $offset, $number);
    }

    public function uniqueDirectory($name) {
        // 1st check if directory not present in db
        $escapedName = addslashes($name);
        if ($this->loaded) {
            $other = $this->db->query("SELECT id from ".$this->table_name." WHERE id != $this->id AND directory = '$escapedName'");
        } else {
            $other = $this->db->query("SELECT id from ".$this->table_name." WHERE directory = '$escapedName'");
        }
        if ($other->count() > 0)
            return false;
        
        // 2nd check that directory does not physically exists
        if (($this->loaded) && isset($this->oldDirectory) && strcmp($name, $this->oldDirectory)==0)
            return true;
        
        return (!file_exists(DOCROOT.Kohana::config('toucan.files_directory')."/".Kohana::config('toucan.templates_directory')."/".$name));
    }

    public function delete() {
        if ($this->loaded) {
            // first: set default style for all sessions
            $sessions = $this->formSessions;
            foreach ($sessions as $session) {
                $session->style_id = 0;
                $session->save();
            }
            
            // second: delete all files
            $files = $this->files;
            foreach ($files as $file) {
                $this->remove($file);
                $file->delete();
            }
            $this->save();
            
            // third: delete the directory
            if (file_exists($this->getAbsoluteDirectory()))
                @rmdir($this->getAbsoluteDirectory());
            
            // fourth: delete element itself
            parent::delete();
        }
    }
    
    public function getDirectory() {
        return Kohana::config('toucan.templates_directory')."/$this->directory";
    }
    
    public function getRootDirectory() {
        return Kohana::config('toucan.files_directory')."/".$this->getDirectory();
    }
    
    public function getAbsoluteDirectory() {
        return DOCROOT.$this->getRootDirectory();
    }
    
    public function getViewFile() {
        return $this->getRootDirectory()."/template.tpl";
    }

    public function getCSSFile($suffix = null) {
        if (isset($suffix))
            return $this->getRootDirectory()."/style_$suffix.css";
        else
            return $this->getRootDirectory()."/style.css";
    }
    
    public static function getAbsoluteDefaultDirectory() {
        return DOCROOT.self::getDefaultDirectory();
    }
    
    public static function getDefaultDirectory() {
        return Kohana::config('toucan.files_directory')."/".Kohana::config('toucan.templates_directory')."/default";
    }
    
    public static function getDefaultViewFile() {
        return self::getDefaultDirectory()."/template.tpl";
    }

    public static function getDefaultCSSFile($suffix = null) {
        if (isset($suffix))
            return self::getDefaultDirectory()."/style_$suffix.css";
        else
            return self::getDefaultDirectory()."/style.css";
    }
    
    protected function checkDirectory() {
        //die(var_dump(umask()));
        if (!file_exists($this->getAbsoluteDirectory())) {
            $oldumask = umask(0);
            mkdir($this->getAbsoluteDirectory(), Kohana::config('toucan.public_directory_mode'));
            umask($oldumask);
        }
    }

    protected function createDefaultStructure() {
        $this->checkDirectory();
        $defaultDirectory = self::getAbsoluteDefaultDirectory();
        $destinationDirectory = $this->getAbsoluteDirectory();
        $oldumask = umask(0);
        if ($handle = opendir($defaultDirectory)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    copy($defaultDirectory."/".$entry, $destinationDirectory."/".$entry);
                    chmod($destinationDirectory."/".$entry, Kohana::config('toucan.public_file_mode'));
                }
            }
            closedir($handle);
        }
        umask($oldumask);
    }
    
    public function updateFiles() {
        $this->checkDirectory();
    
        $presentFiles = array();
        
        // scan files in template directory
        if ($handle = opendir($this->getAbsoluteDirectory())) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..")
                    $presentFiles[] = $entry;
            }
            closedir($handle);
        }
        
        // delete database files that are not physically present
        $directory = $this->getDirectory();
        foreach ($this->files as $file) {
            $key = array_search($file->name, $presentFiles);
            if ($key !== FALSE && strcmp($file->directory, $directory) == 0) {
                // file present in physical
                unset($presentFiles[$key]);
            } else {
                // file not present: remove it from db
                $file->delete();
            }
        }

        // add in db the physically present files that are not already in db
        foreach ($presentFiles as $file) {
            $newFile = ORM::factory('file');
            $newFile->name = $file;
            $newFile->directory = $this->getDirectory();
            $newFile->save();
            $this->add($newFile);
        }
        $this->save();
    }

    public function deleteFile($fileId) {
        $file = ORM::factory('file', $fileId);
        if ($this->has($file)) {
            $this->remove($file);
            $file->delete();
            $this->save();
            return true;
        }
        return false;
    }
    
    public function setValues(& $array) {
        $this->load_values($array);
        if (isset($array['default_structure'])&& $array['default_structure'] == 1) {
            $this->default_structure = 1;
        }
    }
    
}
?>