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
    "show"=>"<h1>Affichage du détail d'une activité</h1>
    <ul>
    <li><b>Nom</b> : le nom de l'activité</li>
    <li><b>Description</b> : un texte optionnel décrivant l'activité</li>
    <li><b>Activité mère</b> : l'activité à laquelle celle-ci est rattachée (facultatif)</li>
    <li><b>Consultable par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant lire les informations de cette activité</li>
    <li><b>Modifiable par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant modifier les informations de cette activité</li>
    </ul>
    ",
    
    "evaluations"=>"<h1>Evaluations rattachées à l'activité</h1>
    Une évaluation est obligatoirement rattachée à une activité. Elle peut elle-même être constituée de sessions de questionnaires, de sessions d'entretiens et d'indicateurs.
    <ul>
    <li><b>Nom</b> : le nom de l'évaluation</li>
    <li><b>Date de début</b> : date de début de l'évaluation (information optionnelle)</li>
    <li><b>Date de fin</b> : date de fin de l'évaluation (information optionnelle)</li>
    <li><b>Etat</b> : l'évaluation peut être <i>en élaboration</i>, <i>en cours</i>, <i>en cours d'analyse</i>, <i>terminée</i> ou <i>annulée</i></li>
    <li><b>Accès directs</b> : pour chaque évaluation, des boutons permettent d'accéder :
    <ul class='no_bullet'>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/information.png"))." aux informations sur l'évaluation</li>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/formSession.png"))." à la liste des sessions de questionnaires</li>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/interviewSession.png"))." à la liste des sessions d'entretien</li>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/indicator.png"))." à la liste des indicateurs</li>
    </ul>
    </li>
    </ul>
    ",

    "surveys"=>"<h1>Enquêtes rattachées à l'activité</h1>
    Une enquête est obligatoirement rattachée à une activité. Elle est elle-même constituée d'un questionnaire et éventuellement d'indicateurs.
    <ul>
    <li><b>Nom</b> : le nom de l'enquête</li>
    <li><b>Etat</b> : l'enquête peut être <i>en élaboration</i>, <i>en cours</i>, <i>terminée</i> ou <i>annulée</i></li>
    <li><b>Accès directs</b> : pour chaque enquête, des boutons permettent d'accéder :
    <ul class='no_bullet'>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/information.png"))." aux informations sur l'enquête</li>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/copies.png"))." à la liste des questionnaires remplis</li>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/indicator.png"))." à la liste des indicateurs</li>
    </ul>
    </li>
    </ul>
    ",

    "showAll"=>"<h1>Liste des sous-activités</h1>
    Une activité peut elle-même contenir plusieurs sous-activités.
    <br/>Une sous-activité est simplement une activité dont la propriété 'activité mère' est renseignée.
    
    ",

    "edit"=>"<h1>Modification des informations d'une activité</h1>
    <ul>
    <li><b>Nom</b> : le nom de l'activité</li>
    <li><b>Description</b> : un texte optionnel décrivant l'activité</li>
    <li><b>Logo</b> : une image qui sera affichée lorsqu'on consulte l'activité</li>
    <li><b>Supprimer le logo</b> : cochez cette case si vous souhaitez supprimer le logo enregistré précédemment</li>
    <li class='help_separator'></li>
    <li><b>Activité mère</b> : l'activité à laquelle celle-ci est rattachée ; laissez le champ vide si vous ne souhaitez pas faire de cette activité une sous-activité</li>
    <li class='help_separator'></li>
    <li><b>Consultable par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant lire les informations de cette activité</li>
    <li><b>Modifiable par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant modifier les informations de cette activité</li>
    </ul>
    ",
    
    "owner"=>"<h1>Modification du propriétaire d'une activité (administration)</h1>
    Sélectionnez un utilisateur dans la liste pour en faire le nouveau propriétaire de l'activité.
    ",

    "create"=>"<h1>Création d'une activité</h1>
    Vous pouvez précisez les informations suivantes :
    <ul>
    <li><b>Nom</b> : le nom de l'activité</li>
    <li><b>Description</b> : un texte optionnel décrivant l'activité</li>
    <li><b>Logo</b> : une image qui sera affichée lorsqu'on consulte l'activité</li>
    <li class='help_separator'></li>
    <li><b>Activité mère</b> : l'activité à laquelle celle-ci est rattachée ; laissez le champ vide si vous ne souhaitez pas faire de cette activité une sous-activité</li>
    <li class='help_separator'></li>
    <li><b>Consultable par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant lire les informations de cette activité</li>
    <li><b>Modifiable par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant modifier les informations de cette activité</li>
    </ul>
    ",

    
);
?>