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

require_once Kohana::find_file('vendor','phprtflite/PHPRtfLite');

class rtf_Core {

    static $document = null;
    static $header = null;
    static $section = null;
    static $formatText = null;
    static $formatTitle = null;
    static $fontText = null;
    static $fontTitle = null;
    static $formatSeparator = null;
    static $fontSeparator = null;
    static $formatSubSeparator = null;
    static $fontSubSeparator = null;

    protected static function init() {
        if (!isset(self::$document)) {
            PHPRtfLite::registerAutoloader();
            self::$document = new PHPRtfLite();
            self::$header = self::$document->addHeader();
            self::$section = self::$document->addSection();
            self::$section->setMarginLeft(2.0);
            self::$section->setMarginRight(2.0);
            self::$formatText = new PHPRtfLite_ParFormat();
            self::$fontText = new PHPRtfLite_Font(10);
            self::$fontTitle = new PHPRtfLite_Font(11,'',"#385D8A");
            self::$fontTitle->setBold(true);
            self::$formatTitle = new PHPRtfLite_ParFormat();
            self::$formatTitle->setBackgroundColor('#f2f2f2');
            self::$formatTitle->setSpaceBefore(15);
            self::$formatSeparator = new PHPRtfLite_ParFormat();
            self::$formatSeparator->setBackgroundColor('#385D8A');
            self::$formatSeparator->setSpaceBefore(20);
            self::$fontSeparator = new PHPRtfLite_Font(14,'',"#ffffff");
            self::$fontSeparator->setBold(true);
            self::$formatSubSeparator = new PHPRtfLite_ParFormat();
            self::$formatSubSeparator->setSpaceBefore(10);
            self::$fontSubSeparator = new PHPRtfLite_Font(13,'',"#385D8A");
            self::$fontSubSeparator->setBold(true);
            $footer = self::$document->addFooter();
            $footer->writeText(sprintf(Kohana::lang("export.rtf_footer"),Utils::translateTimestamp(time())), new PHPRtfLite_Font(9), new PHPRtfLite_ParFormat('center'));
        }
    }

    public static function initDocument($title=null, $subtitle = null, $logo = null) {
        self::$document = null;
        self::init();
        if (isset($logo)) {
            self::$header->addImage($logo, self::$formatText);
        }
        if (isset($title)) {
            $titleFormat = new PHPRtfLite_ParFormat('center');
            $titleFormat->setSpaceAfter(5);
            $titleFont = new PHPRtfLite_Font(14,'', "#385D8A");
            $titleFont->setBold(true);
            self::$section->writeText($title, $titleFont, $titleFormat);
        }
        if (isset($subtitle)) {
            $subtitleFont = new PHPRtfLite_Font(12,'', "#385D8A");
            self::$section->writeText($subtitle, $subtitleFont, $titleFormat);
        }
        $separatorFormat = new PHPRtfLite_ParFormat('center');
        $separatorFormat->setSpaceAfter(30);
        self::$section->writeText(" ", null, $separatorFormat);
    }

    public static function addText($text) {
        self::init();
        self::$section->writeText($text, self::$fontText);
    }

    public static function addParagraph($text, $title = null) {
        self::init();
        if (isset($title)) {
            self::$section->writeText($title, self::$fontTitle, self::$formatTitle);
        }
        self::$section->writeText($text, self::$fontText, self::$formatText);
    }
    
    public static function addInformation($information) {
        self::init();
        $separatorFormat = new PHPRtfLite_ParFormat('center');
        $separatorFormat->setSpaceBefore(30);
        self::$section->writeText(" ", null, $separatorFormat);
        $informationFormat = new PHPRtfLite_ParFormat();
        $informationFormat->setSpaceBefore(2);
        $informationFormat->setSpaceAfter(2);
        $informationFormat->setBackgroundColor('#f2f2f2');
        $fontInformation = new PHPRtfLite_Font(10,'',"#385D8A");
        foreach ($information as $piece) {
            self::$section->writeText($piece, $fontInformation, $informationFormat);
        }
    }

    public static function addSeparator($title, $text = null, $subSeparator = false) {
        self::init();
        if ($subSeparator) {
            $format = self::$formatSubSeparator;
            $font = self::$fontSubSeparator;
        } else {
            $format = self::$formatSeparator;
            $font = self::$fontSeparator;
        }
        self::$section->writeText($title, $font, $format);
        if (isset($text)) {
            self::$section->writeText($text, self::$fontText, self::$formatText);
        }
    }

    public static function addTextQuestion($text, $description = " ", $long = false, $answer = null) {
        self::init();
        self::$section->writeText($text, self::$fontTitle, self::$formatTitle);
        self::$section->writeText($description, self::$fontText, self::$formatText);
        $table = self::$section->addTable();
        $table->addColumn(17);
        if ($long && ! isset($answer))
            $table->addRow(4);
        else
            $table->addRow(1);
        
        $border = PHPRtfLite_Border::create(self::$document, 1, '#385D8A');
        $cell = $table->getCell(1, 1);
        $cell->setBorder($border);
        $cell->setCellPaddings(0.2, 0.2, 0.2, 0.2);
        if (isset($answer)) {
            $table->writeToCell(1, 1, $answer, self::$fontText, self::$formatText);
        }
    }

    public static function addQuestionWithChoices($text, $description = " ", $choices = array(), $multiple = false, $answers = null) {
        self::init();
        self::$section->writeText($text, self::$fontTitle, self::$formatTitle);
        self::$section->writeText($description, self::$fontText, self::$formatText);
        $choicesNumber = sizeof($choices);
        if ($choicesNumber>0) {
            $table = self::$section->addTable();
            $table->addRows($choicesNumber);
            $table->addColumn(0.7);
            $table->addColumn(15);
            if ($multiple) {
                $dot = Kohana::config("toucan.images_directory")."/several_choices.png";
                $checked = Kohana::config("toucan.images_directory")."/several_choices_checked.png";
            }
            else {
                $dot = Kohana::config("toucan.images_directory")."/one_choice.png";
                $checked = Kohana::config("toucan.images_directory")."/one_choice_checked.png";
            }
            for ($i=0; $i<$choicesNumber; $i++) {
                $cell = $table->getCell($i+1, 1);
                $cell->setVerticalAlignment('top');
                if (isset($answers)&&in_array($i, $answers))
                    $cell->addImage($checked);
                else
                    $cell->addImage($dot);
                $cell = $table->getCell($i+1, 2);
                $cell->setVerticalAlignment('top');
                $cell->writeText($choices[$i], self::$fontText);
            }
        }
    }

    public static function addImage($image) {
        self::init();
        self::$section->addImage($image);
    }

    public static function saveDocument($file) {
        self::init();
        self::$document->save($file);
    }

    public static function sendDocument($fileName="") {
        self::init();
        self::$document->sendRtf($fileName);
    }

}
?>