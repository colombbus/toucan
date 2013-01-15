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

class File_Model extends Toucan_Model {

    protected $ignored_columns = array('contents', 'oldName', 'upload', 'overwrite');
    protected $oldName = null;
    protected $allowedExtensions = null;
    protected $editableExtensions = null;
    protected $table_name = "files";

    public function delete() {
        if ($this->loaded) {
            @unlink($this->getFile());
        }
        parent::delete();
    }

    protected static function generateRandomName($extension = "") {
        return rand().$extension;
    }

    protected static function fileExists($fileName, $directory) {
        if (file_exists(self::findFile($fileName, $directory)))
            return true;
        $files = ORM::factory('file')->where('name',$fileName)->where('directory',$directory)->find_all();
        if ($files->valid())
            return true;
        return false;
    }

    protected static function newName($directory = "", $extension = "") {
        $name = self::generateRandomName($extension);
        while (self::fileExists($name, $directory)) {
            $name = self::generateRandomName($extension);
        }
        return $name;
    }

    public static function newFile($directory = "", $extension = "") {
        $file = ORM::factory('file');
        $file->directory = $directory;
        $file->name = self::newName($directory, $extension);
        $file->save();
        return $file;
    }

    public function getPath() {
        $path = Kohana::config('toucan.files_directory');
        if (isset($this->directory)) {
            $path.="/".$this->directory;
        }
        return $path."/".$this->name;
    }

    public function getPathInDirectory() {
        $path = "";
        if (isset($this->directory)) {
            $path=$this->directory."/";
        }
        return $path.$this->name;
    }

    public function getAbsoluteDirectory() {
        $directory = DOCROOT.Kohana::config('toucan.files_directory');
        if (isset($this->directory)) {
            $directory.="/".$this->directory;
        }
        return $directory;
    }

    protected static function findFile($fileName, $directory) {
        $path = DOCROOT.Kohana::config('toucan.files_directory');
        if (isset($directory)) {
            $path.="/".$directory;
        }
        return $path."/".$fileName;
    }

    public function getFile() {
        return $this->getAbsoluteDirectory()."/".$this->name;
    }

    public function __get($column) {
            if ($column == 'path') {
            // Order indicators by field 'order'
            return $this->getPath();
        }
        return parent::__get($column);
    }

    public function getItems(& $filter,& $user,$offset = 0, $number = null, $constraints = null) {
        if (isset($constraints)&& is_array($constraints) && sizeof($constraints)>0)
            $this->in('id',$constraints);
        else
            return null;
        if (isset($filter))
            $filter->add($this);
        else
            $this->orderby('name', 'ASC'); // by default, order by name
        if (isset($number))
            $this->limit($number, $offset);
        return $this->find_all();
    }

    public function count(& $filter , & $user, $constraints= null) {
        if (isset($constraints)&  is_array($constraints)&& sizeof($constraints)>0)
            $this->in('id',$constraints);
        else
            return 0;
        if (isset($filter))
            $filter->add($this);
        return $this->count_all();
    }

    public function getCreationData($access, & $user, & $parameters = null) {
        $creationData[] = array ('type' => 'text','name' => 'name','label' => 'file.name','required'=>'1', 'value' => $this->name);
        return $creationData;
    }
    
    public function getEditableData($access, & $user) {
        $editionData[] = array ('type' => 'text','name' => 'name','label' => 'file.name','required'=>'1', 'value' => $this->name);
        if (isset($this->editableExtensions) && in_array($this->getExtension(),$this->editableExtensions)) {
            // file is editeable : provide contents for edition
            $editionData[] = array ('type' => 'file_contents','name' => 'contents','label' => 'file.contents','value' => $this->getFileContents());
        }
        $editionData[] = array ('type' => 'hidden','name' => 'directory','value'=>$this->directory);
        return $editionData;
    }

    public function getFileUploadData($access, & $user) {
        $uploadData[] = array ('type' => 'file','name' => 'upload', 'label' => 'file.upload', 'required'=>'1');
        $uploadData[] = array ('type' => 'check','name' => 'overwrite','label' => 'file.overwrite');
        return $uploadData;
    }

    
    public function getDisplayableData($access,& $user =null) {
        // nothing
    }
    
    public function validateEdition(array & $array,& $user, $save = FALSE) {
        if (!isset($array['directory']))
            return false;
        if ($this->loaded)
            $this->oldName = $this->name;
        $this->directory = $array['directory'];
        $this->validation = Validation::factory($array)
        ->pre_filter('trim')
        ->add_rules('name', 'required', array($this, 'uniqueName'), 'length[1,127]', array($this, 'extensionAllowed'))
        ->add_rules('directory', 'required');
        if (parent::validate($this->validation, false)) {
            if ($this->loaded  && strcmp($this->name, $this->oldName) != 0) {
                // filename has changed: move file
                $this->moveFile($this->oldName, $this->name);
            }
            if (isset($array['contents'])&&strlen($array['contents'])>0) {
                // save contents
                $this->setFileContents($array['contents']);
            }
            if ($save) {
                $this->save();
            }
            return true;
        }
        return false;
    }
    
    public function validateCreation(array & $array,& $user, $save = FALSE) {
        if ($this->validateEdition($array, $user, $save)) {
            $this->createFile();
            return true;
        }
        return false;
    }
    
    public function validateUpload(array & $array, & $user, $save = FALSE) {
        $this->validation = Validation::factory($array)
            ->pre_filter('trim')
            ->add_rules('overwrite','in_array[0,1]')
            ->add_rules('directory', 'required');
        $fileValidation = Validation::factory($_FILES)
            ->add_rules('upload', 'upload::valid', array($this, 'extensionAllowed'), 'upload::size[2M]');
        if (parent::validate($this->validation, false)) {
            if (parent::validate($fileValidation, false)) {
                $fileName = $_FILES['upload']['name'];
                if (!$this->uniqueName($fileName)) {
                    // file already exists
                    if (!isset($array['overwrite'])||$array['overwrite']==0) {
                        // No overwrite allowed: we have to find a new name
                        do {
                            $fileName = $this->copyName($fileName);
                        }
                        while (!$this->uniqueName($fileName));
                    }
                }
                $this->name = $fileName;
                upload::save('upload', $fileName, $this->getAbsoluteDirectory(), Kohana::config('toucan.public_file_mode'));
                if ($save) {
                    $this->save();
                }
                return true;
            } else {
                $fileErrors = $fileValidation->errors();
                foreach ($fileErrors as $key=>$value)
                if ($fileValidation) {
                    $this->validation->add_error($key,$value);
                }
            }
        }
        return false;
    }

    public function uniqueName($name) {
        if ($this->loaded && strcmp($this->name, $name) == 0)
            return true;
        $absolutePath = $this->getAbsoluteDirectory()."/".$name;
        return !file_exists($absolutePath);
    }

    public function extensionAllowed($name) {
        if (!isset($this->allowedExtensions))
            return true;
        if (is_array($name)) {
            $name = $name['name'];
        }
        $index = strrpos($name, ".");
        if ($index === FALSE) {
            // no extension
            return false;
        }
        $extension = substr($name, $index+1);
        return (in_array($extension, $this->allowedExtensions));
    }
    
    protected function getExtension() {
        $pathParts = pathinfo($this->name);
        if (isset($pathParts['extension']))
            return $pathParts['extension'];
        return "";
    }
    
    protected function getFileContents() {
        if (isset($this->oldName)) {
            $file = $this->getAbsoluteDirectory()."/".$this->oldName;
        } else {
            $file = $this->getFile();
        }
        if (file_exists($file))
            return file_get_contents($file);
        return "";
    }
    
    protected function setFileContents($contents) {
        return file_put_contents($this->getFile(), $contents);
    }
    
    protected function createFile() {
        $handle = fopen($this->getFile(),"w");
        fclose($handle);
    }
    
    protected function moveFile($old, $new) {
        $oldPath = $this->getAbsoluteDirectory()."/".$old;
        $newPath = $this->getFile();
        rename($oldPath, $newPath);
    }
   
    protected function copyName($fileName) {
        $extensionIndex = strrpos($fileName, ".");
        $baseName = substr($fileName, 0, $extensionIndex);
        $extension = substr($fileName, $extensionIndex+1);
        return $baseName."_copy.".$extension;
    }
    
}
?>