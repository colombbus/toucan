<?php defined('SYSPATH') or die('No direct access allowed.');
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

$lang=array(
    "show"=>"<h1>Affichage des détails d'un indicateur</h1>
    <ul>
    <li><b>Nom</b> : le nom de l'indicateur</li>
    <li><b>Description</b> : un texte optionnel décrivant l'indicateur</li>
    <li class='help_separator'></li>
    <li><b>Type</b> : le type d'indicateur. Celui-ci peut être :
    <ul>
    <li><b>Fixé par les évaluateurs</b> : ce sont les évaluateurs qui définissent sa valeur au cours de l'évaluation</li>
    <li><b>Valeur numérique calculée automatiquement</b> : sa valeur se met à jour en fonction des questionnaires saisis</li>
    <li><b>Graphique calculé automatiquement</b> : sa représentation se met à jour en fonction des questionnaires saisis</li>
    </ul>
    </li>
    <li><b>Valeurs</b> (indicateur fixé manuellement) : lien pour consulter les valeurs que peut prendre l'indicateur</li>
    </ul>
    ",
    
    "edit"=>"<h1>Modification d'un indicateur</h1>
    <ul>
    <li><b>Nom</b> : le nom de l'indicateur</li>
    <li><b>Description</b> : un texte optionnel décrivant l'indicateur</li>
    </ul>
    ",

    "values"=>"<h1>Liste des valeurs pouvant être prises par l'indicateur</h1>
    Lorsqu'un indicateur est fixé manuellement, sa valeur est définie parmi plusieurs valeurs potentielles, listées sur cette page.
    Chaque valeur est représentée sous la forme d'un cadre. Vous pouvez afficher ou masquer les détails d'une valeur, ou bien agir directement sur toutes les valeurs grâce aux boutons 
    &laquo;&nbsp;Cacher tous les détails&nbsp;&raquo; et &laquo;&nbsp;Montrer tous les détails&nbsp;&raquo;.
    <br/>Chaque valeur contient les informations suivantes :
    <ul>
    <li><b>Valeur</b> : le texte correspondant à la valeur</li>
    <li><b>Description</b> : un texte optionnel de description</li>
    <li><b>Couleur</b> : éventuellement, une couleur peut être définie pour la valeur. Dans ce cas, le cadre apparaît dans la couleur sélectionnée</li>
    </ul>
    <h1>Ajouter une valeur (gestionnaires)</h1>
    Si vous avez les droits pour modifier l'indicateur, vous pouvez ajouter une valeur (bouton en bas de l'écran) : un nouveau cadre apparaît, dans lequel vous pouvez 
    entrer les informations sur cette nouvelle valeur.
    <h1>Modifier l'ordre des valeurs (gestionnaires)</h1>
    Si vous avez les droits pour modifier l'indicateur, vous pouvez modifier l'ordre dans lequel 
    apparaissent les valeurs : il vous suffit de cliquer et de faire glisser l'image".html::image(array("src"=>Kohana::config("toucan.images_directory")."/handle.png"))."
    ",

    "createStart"=>"<h1>Création d'un indicateur - choix du type d'indicateur</h1>
    <ul>
    <li><b>Nom</b> : le nom de l'indicateur</li>
    <li><b>Description</b> : un texte optionnel décrivant l'indicateur</li>
    </ul>
    ",
    
);
?>