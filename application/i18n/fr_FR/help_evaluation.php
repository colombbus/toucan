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
    "show"=>"<h1>Affichage du détail d'une évaluation</h1>
    <ul>
    <li><b>Nom</b> : le nom de l'évaluation</li>
    <li><b>Description</b> : un texte optionnel décrivant l'évaluation</li>
    <li class='help_separator'></li>
    <li><b>Etat</b> : une évaluation peut être <i>en élaboration</i>, <i>en cours</i>, <i>en cours d'analyse</i>, <i>terminée</i> ou <i>annulée</i></li>
    <li><b>Date de début</b> : une date éventuelle de début (information indicative)</li>
    <li><b>Date de fin</b> : une date éventuelle de fin (information indicative)</li>
    <li class='help_separator'></li>
    <li><b>Consultable par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant consulter cette évaluation</li>
    <li><b>Gestionnaires</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant modifier les informations de cette évaluation</li>
    </ul>
    
    <h1>Actions rapides (gestionnaires)</h1>
    Si vous avez l'autorisation de modifier l'évaluation (propriétaire, gestionnaire, administrateur), des boutons en bas à droite vous permettent de changer directement son état :
    <ul class='no_bullet'>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/under_construction.png"))." en élaboration</li>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/going_on.png"))." en cours</li>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/under_analyse.png"))." en analyse</li>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/over.png"))." terminée</li>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/cancelled.png"))." annulée</li>
    </ul>
    ",

    "edit"=>"<h1>Modification des informations d'une évaluation</h1>
    <ul>
    <li><b>Nom</b> : le nom de l'évaluation</li>
    <li><b>Description</b> : un texte optionnel décrivant l'évaluation</li>
    <li class='help_separator'></li>
    <li><b>Etat</b> : une évaluation peut être <i>en élaboration</i>, <i>en cours</i>, <i>en cours d'analyse</i>, <i>terminée</i> ou <i>annulée</i></li>
    <li><b>Date de début</b> : une date éventuelle de début (information indicative)</li>
    <li><b>Date de fin</b> : une date éventuelle de fin (information indicative)</li>
    <li class='help_separator'></li>
    <li><b>Appliquer le même contrôle d'accès que pour l'activité</b> : si la case est cochée, l'évaluation est consultable par tous les utilisateurs qui peuvent consulter l'activité et est modifiable par tous ceux qui peuvent modifier l'activité</li>
    <li><b>Consultable par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant lire les informations de cette évaluation</li>
    <li><b>Gestionnaires</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant modifier les informations de cette évaluation</li>
    </ul>
    ",

    "owner"=>"<h1>Modification du propriétaire d'une évaluation (administration)</h1>
    Sélectionnez un utilisateur dans la liste pour en faire le nouveau propriétaire de l'évaluation.
    ",
    
    "forms"=>"<h1>Liste des sessions de questionnaires rattachées à l'évaluation</h1>
    Une évaluation peut comporter un certain nombre de sessions de questionnaires, qui sont listées ici.
    <ul>
    <li><b>Nom</b> : le nom du questionnaire</li>
    <li><b>Etat</b> : la session peut être <i>en élaboration</i>, <i>en cours</i>, <i>terminée</i> ou <i>annulée</i></li>
    <li><b>Accès directs</b> : pour chaque session, des boutons permettent d'accéder :
    <ul class='no_bullet'>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/information.png"))." aux informations sur la session</li>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/copies.png"))." à la liste des questionnaires remplis</li>
    </ul>
    </li>
    </ul>
    ",

    "interviews"=>"<h1>Liste des sessions d'entretiens rattachées à l'évaluation</h1>
    Une évaluation peut comporter un certain nombre de sessions d'entretiens, qui sont listées ici.
    <ul>
    <li><b>Nom</b> : le nom de l'entretien</li>
    <li><b>Etat</b> : la session peut être <i>en élaboration</i>, <i>en cours</i>, <i>terminée</i> ou <i>annulée</i></li>
    <li><b>Accès directs</b> : pour chaque session, des boutons permettent d'accéder :
    <ul class='no_bullet'>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/information.png"))." aux informations sur la session</li>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/copies.png"))." à la liste des rapports d'entretien remplis</li>
    </ul>
    </li>
    </ul>
    ",
    
    "indicators"=>"<h1>Liste des indicateurs de l'évaluation</h1>
    Cette page présente la liste des indicateurs définis pour l'évaluation. Chaque indicateur est représenté sous la forme d'un cadre.
    <ul>
    <li>Un  <b>indicateur graphique</b> s'affiche directement sous la forme d'une représentation graphique</li>
    <li>Un <b>indicateur numérique</b> affiche sa valeur sous la forme &laquo;&nbsp;Valeur : XXX&nbsp;&raquo;. Si des seuils de représentation ont été défini, sa couleur peut varier en fonction de cette valeur.</li>
    <li>Un <b>indicateur manuel</b> affiche la valeur telle qu'elle a été définie par les évaluateurs. En fonction de la manière dont il a été défini, sa couleur peut changer en fonction de cette valeur.</li>
    </ul>
    <h1>Indicateurs non renseignés</h1>
    <ul>
    <li>Si un indicateur ne trouve aucune information pour calculer sa valeur ou sa représentation graphique, il affiche le texte &laquo;&nbsp;aucune valeur&nbsp;&raquo;</li>
    <li>Si un indicateur manuel n'a pas encore été renseigné par les évaluateurs, il affiche le texte &laquo;&nbsp;indicateur non défini&nbsp;&raquo;</li>
    </ul>
    <h1>Exporter les indicateurs</h1>
    Un bouton situé en bas de la page vous permet d'exporter les indicateurs sous la forme d'un document au format rtf (lisible par MS Word, OpenOffice et de nombreux éditeurs de texte).
    <h1>Réorganiser les indicateurs (gestionnaires)</h1>
    Si vous avez les droits pour modifier l'évaluation (propriétaire, gestionnaires, administrateur), vous pouvez modifier l'ordre dans lequel 
    apparaissent les indicateurs : il vous suffit de cliquer et de faire glisser l'image".html::image(array("src"=>Kohana::config("toucan.images_directory")."/handle.png"))."
    ",

    "selectActivity"=>"<h1>Création d'une évaluation - choix de l'activité</h1>
    Une évaluation est nécessairement rattachée à une activité. Cet écran vous présente la liste des activités enregistrées dans Toucan : sélectionnez celle à laquelle la nouvelle évaluation sera rattachée.
    ",
    
    "create"=>"<h1>Création d'une évaluation</h1>
    Vous pouvez précisez les informations suivantes :
    <ul>
    <li><b>Nom</b> : le nom de l'évaluation</li>
    <li><b>Description</b> : un texte optionnel décrivant l'évaluation</li>
    <li class='help_separator'></li>
    <li><b>Etat</b> : une évaluation peut être <i>en élaboration</i>, <i>en cours</i>, <i>en cours d'analyse</i>, <i>terminée</i> ou <i>annulée</i></li>
    <li><b>Date de début</b> : une date éventuelle de début (information indicative)</li>
    <li><b>Date de fin</b> : une date éventuelle de fin (information indicative)</li>
    <li class='help_separator'></li>
    <li><b>Appliquer le même contrôle d'accès que pour l'activité</b> : si la case est cochée, l'évaluation est consultable par tous les utilisateurs qui peuvent consulter l'activité et est modifiable par tous ceux qui peuvent modifier l'activité</li>
    <li><b>Consultable par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant lire les informations de cette activité</li>
    <li><b>Gestionnaires</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant modifier les informations de cette évaluation</li>
    </ul>
    ",

    "showAll"=>"<h1>Liste des évaluations</h1>
    Cette liste présente l'ensemble des évaluations enregistrées dans Toucan.
    <ul>
    <li><b>Nom</b> : le nom de l'évaluation</li>
    <li><b>Activité</b> : l'activité à laquelle est rattachée l'évaluation</li>
    <li><b>Etat</b> : une évaluation peut être <i>en élaboration</i>, <i>en cours</i>, <i>en cours d'analyse</i>, <i>terminée</i> ou <i>annulée</i></li>
    </ul>
    <h1>Filtres</h1>
    Les boutons en bas à droite vous permettent de n'afficher que les évaluations qui sont dans un certain état :
    <ul class='no_bullet'>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/all.png"))." toutes les évaluations</li>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/going_on.png"))." évaluations en cours</li>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/over.png"))." évaluations terminées</li>
    </ul>
    
    ",

);
?>