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

require_once Kohana::find_file('vendor','jpgraph/jpgraph');

class graphic_Core {

    public static function initGraph(& $info, &$labels) {
        try {
            // Width and height of the graph
            $width = Kohana::config('toucan.graphic_width');
            $height = Kohana::config('toucan.graphic_height');
    
            // Create a graph instance
            $graph = new Graph($width,$height);
    
            $graph->SetShadow();
    
            $graph->SetScale('textlin');
    
            // set fonts
            $graph->xaxis->SetFont(FF_DV_SANSSERIF,FS_NORMAL,9);
            $graph->xaxis->title->SetFont(FF_DV_SANSSERIF,FS_BOLD,9);
            $graph->yaxis->SetFont(FF_DV_SANSSERIF,FS_NORMAL,9);
            $graph->yaxis->title->SetFont(FF_DV_SANSSERIF,FS_BOLD,9);
            $graph->title->SetFont(FF_DV_SANSSERIF,FS_BOLD,9);
    
            if (isset($info['title'])) {
                $graph->title->Set($info['title']);
            }
    
            if (isset($info['x_axis'])) {
                $graph->xaxis->title->Set($info['x_axis']);
            }
    
            $graph->xaxis->SetTickLabels($labels);
    
            if (isset($info['y_axis'])) {
                $graph->yaxis->title->Set($info['y_axis']);
            }
    
            $graph->SetMargin(40,30,20,40);
    
            return $graph;
        } catch (JpGraphException $e)
        { 
            throw new Exception('graphic.error_init');
        }
    }

    public static function diagram(& $values, & $labels, & $info, & $fileName) {
        try {
            require_once Kohana::find_file('vendor','jpgraph/jpgraph_line');

            $graph = self::initGraph($info, $labels);

            // Create the linear plot
            $linePlot=new LinePlot($values);
            $linePlot->SetColor('blue');
            $linePlot->SetWeight(2);   // Two pixel wide
            $linePlot->value->SetFont(FF_DV_SANSSERIF,FS_NORMAL,9);
            $linePlot->value->Show();

            // Add the plot to the graph
            $graph->Add($linePlot);

            // Display the graph
            $graph->Stroke($fileName);
        } catch (JpGraphException $e)
        { 
            throw new Exception('graphic.error_diagram');
        }
    }

    public static function histogram(& $values, & $labels, & $info, & $fileName) {
        try {
            require_once Kohana::find_file('vendor','jpgraph/jpgraph_bar');
    
            $graph = self::initGraph($info, $labels);
    
            // Create a bar pot
            $barPlot = new BarPlot($values);
    
            // Adjust fill color
            $barPlot->SetFillColor('orange');
            $graph->Add($barPlot);
    
            // Display the graph
            $graph->Stroke($fileName);
        } catch (JpGraphException $e)
        { 
            throw new Exception('graphic.error_histogram');
        }
    }

    public static function pie_chart(& $values, & $labels, & $info, & $fileName) {
        try{
            require_once Kohana::find_file('vendor','jpgraph/jpgraph_pie');
    
            // Compute percent values
            $total = 0;
            foreach($values as $value) {
                $total += $value;
            }
    
            $percents = array();
    
            foreach ($values as $value) {
                $percents[] = $value/$total;
            }
    
            // Retrieve graphic constants
            $width = Kohana::config('toucan.graphic_width');
            $height = Kohana::config('toucan.graphic_height');
            $lineHeight = Kohana::config('toucan.graphic_legend_line_height');
            $maxChars = Kohana::config('toucan.graphic_max_chars');
            $pieSize = Kohana::config('toucan.graphic_pie_size');
    
            // Deal with labels
            $rowsNumber = count($labels);
            $newLabels = array();
            foreach ($labels as $label) {
                $newLabel = wordwrap($label, $maxChars, "\n");
                $rowsNumber += substr_count($newLabel , "\n");
                $newLabels[] = $newLabel;
            }
    
            // Create the graphic
            $height = max($height, $rowsNumber*$lineHeight);
            $graph = new PieGraph($width,$height);
    
            $graph->title->SetFont(FF_DV_SANSSERIF,FS_BOLD,9);
    
            if (isset($info['title'])) {
                // Setup a title for the graph
                $graph->title->Set($info['title']);
            }
    
            $graph->SetShadow();
            $graph->SetAntiAliasing();
            $graph->legend->SetFont(FF_DV_SANSSERIF,FS_NORMAL,9);
            $graph->legend->Pos(0.45, 0.12, 'left', 'top');
    
            $piePlot = new PiePlot($percents);
            //$piePlot->SetSize(0.35);
            $piePlot->value->SetFont(FF_DV_SANSSERIF,FS_NORMAL,9);
            $piePlot->SetLegends($newLabels);
            $piePlot->SetTheme("earth");
            $piePlot->ShowBorder(true,true);
    
            $piePlot->SetLabelType(PIE_VALUE_ADJPERCENTAGE);
            $piePlot->SetCenter(0.21);
            $piePlot->SetSize($pieSize);
            $graph->Add($piePlot);
    
            $graph->Stroke($fileName);
        } catch (JpGraphException $e)
        { 
            throw new Exception('graphic.error_pie_chart');
        }
    }

}
?>