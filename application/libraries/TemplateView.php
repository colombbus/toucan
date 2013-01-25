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
class TemplateView_Core extends View {
	
    protected $style = null;
    protected $step = null;
    protected $wrappedElements = array("message","error");
    
    
	public function __construct($name = NULL, $data = NULL, $type = NULL) {
        parent::__construct($name, $data, $type);
        $this->__set("title", "");
        $this->__set("description", "");
        $this->__set("message", "");
        $this->__set("error", "");
        $this->__set("content", "");
	}
    
    public function set_filename($name, $type = NULL) {
		if ($type == NULL) {
			// Load the filename and set the content type
			$this->kohana_filename = DOCROOT.$name;
			$this->kohana_filetype = EXT;
		} else {
            parent::set_filename($name, $type);
        }
    }
    
    public function setStyle(& $style) {
        $this->style = $style;
    }

    public function setStep($step) {
        $this->step = $step;
    }

    
    protected function getDirectory() {
        if (isset($this->style)) {
            return $this->style->getRootDirectory();
        } else {
            return Style_Model::getDefaultDirectory();
        }
    }

    protected function getCSS() {
        if (isset($this->style)) {
            return html::stylesheet(array($this->style->getCSSFile()),array("all"),FALSE);
        } else {
            return html::stylesheet(array(Style_Model::getDefaultCSSFile()),array("all"),FALSE);
        }
    }

    protected function getStepCSS() {
        if (isset($this->style)) {
            return html::stylesheet(array($this->style->getCSSFile($this->step)),array("all"),FALSE);
        } else {
            return html::stylesheet(array(Style_Model::getDefaultCSSFile($this->step)),array("all"),FALSE);
        }
    }

    
    public function __set($key, $value) {
        if (in_array($key,$this->wrappedElements)&&is_string($value)&&strlen(trim($value))>0)
            $value = "<div class='wrap'>".$value."</div>";
		$this->kohana_local_data['{'.$key.'}'] = $value;
	}

    public function &__get($key) {
        $actualKey = '{'.$key.'}';
		if (isset($this->kohana_local_data[$actualKey]))
			return $this->kohana_local_data[$actualKey];

		if (isset(View::$kohana_global_data[$actualKey]))
			return View::$kohana_global_data[$actualKey];

		if (isset($this->$key))
			return $this->$key;
	}
    
    protected function addToucanData(& $contents) {
        $headLocation = stripos($contents, "<head>");
        if ($headLocation === FALSE) {
            // location not found: could not include toucan data
            return $contents;
        }
        $part1 = substr($contents, 0, $headLocation+6);
        $part2 = substr($contents, $headLocation+6);
        $result = $part1;
        $result .= "<script language='javascript' src = '".url::file(Kohana::config('toucan.js_directory')."/prototype.js")."' > </script>\n";
        $result .= "<script language='javascript' src = '".url::file(Kohana::config('toucan.js_directory')."/scriptaculous.js")."' > </script>\n";
        $result.= "<script language='javascript' src = '".url::file(Kohana::config('toucan.js_directory')."/textarearesizer.js")."' > </script>\n";
        $result.= "<base href='".url::base().$this->getDirectory()."/' />\n";
        $result.= $this->getStepCSS();
        $result.=$part2;
        return $result;
    }
    
    public function render($print = FALSE, $renderer = FALSE)
	{
		if (empty($this->kohana_filename))
			throw new Kohana_Exception('core.view_set_filename');
        $contents = file_get_contents($this->kohana_filename);
        $contents = $this->addToucanData($contents);
        $contents = str_replace(array_keys($this->kohana_local_data), array_values($this->kohana_local_data), $contents);
        echo $contents;
        return $contents;
    }
}

?>
