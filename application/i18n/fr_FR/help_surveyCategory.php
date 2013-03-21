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
   
    "show"=>"<h1>Affichage du détail d'une catégorie</h1>
    <ul>
    <li><b>Catégorie activé</b> : si elle est activée, la catégorie apparaîtra dans la liste des catégories</li>
    <li class='help_separator'></li>
    <li><b>Nom</b> : le nom de la catégorie</li>
    <li><b>Description</b> : un texte descriptif optionnel</li>
    <li><b>Catégorie récapitulative</b> : une catégorie récapitulative liste automatiquement tous les indicateurs définis dans l'enquête</li>
    <li class='help_separator'></li>
    <li><b>Consultable par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant voir cette catégorie</li>
    <li><b>Modifiable par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant modifier les informations de cette catégorie</li>
    <li class='help_separator'></li>
    <li><b>Adresse d'accès par le public (accès public)</b> : lien menant à la catégorie publiée</li>
    <li><b>Mot de passe (accès public)</b> : le mot de passe (si défini) protégeant l'accès aux indicateurs</li>
    <li><b>Style (accès public)</b> : modèle de style utilisé pour la catégorie</li>
    <li><b>Langue utilisée (accès public)</b> : la langue utilisée par Toucan pour afficher les informations aux utilisateurs</li>
    </ul>
    ",

    "members"=>"<h1>Affichage des indicateurs de la catégorie</h1>
    <ul>
    <li><b>Nom</b> : le nom de l'indicateur</li>
    <li><b>Type</b> : le type de l'indicateur (Graphique calculé automatiquement, Valeur numérique calculée automatiquement ou Fixé par les évaluateurs)</li>
    </ul>
    ",

    "setMembers"=>"<h1>Définition des indicateurs de la catégorie</h1>
    Cliquez sur un indicateur pour l'ajouter à la catégorie ou l'enlever de la catégorie
    <ul>
    <li><b>#</b> : ordre de l'indicateur</li>
    <li><b>Nom</b> : le nom de l'indicateur</li>
    <li><b>Type</b> : le type de l'indicateur (Graphique calculé automatiquement, Valeur numérique calculée automatiquement ou Fixé par les évaluateurs)</li>
    </ul>
    ",
    
    "edit"=>"<h1>Modification des informations de la catégorie</h1>
    <ul>
    <li><b>Catégorie activée</b> : cochez cette case si vous souhaitez que la catégorie apparaisse dans la liste des catégories</li>
    <li class='help_separator'></li>
    <li><b>Nom</b> : le nom de la catégorie</li>
    <li><b>Description</b> : un texte descriptif optionnel</li>
    <li><b>Catégorie récapitulative</b> : cochez cette case si vous souhaitez que la catégorie affiche automatiquement tous les indicateurs de l'enquête</li>
    <li class='help_separator'></li>
    <li><b>Appliquer le même contrôle d'accès que pour l'enquête</b> : si la case est cochée, la catégorie est consultable par tous les utilisateurs qui peuvent consulter l'enquête et est modifiable par tous ceux qui peuvent modifier l'enquête</li>
    <li><b>Consultable par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant voir cette catégorie</li>
    <li><b>Modifiable par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant modifier cette catégorie</li>
    <li class='help_separator'></li>
    <li><b>Catégorie en accès public</b> : cochez la case si vous souhaitez publier la catégorie en ligne</li>
    <li><b>Accès protégé par un mot de passe (accès public)</b> : cochez la case si vous souhaitez que seules les personnes connaissant le mot de passe puissent consulter les indicateurs de la catégorie</li>
    <li><b>Mot de passe (accès public)</b> : le mot de passe qui protège l'accès aux indicateurs de la catégorie</li>
    <li><b>Style (accès public)</b> : le modèle de style que vous souhaitez appliquer à la catégorie</li>
    <li><b>Langue utilisée (accès public)</b> : la langue que Toucan doit utiliser pour afficher les informations aux utilisateurs</li>
    </ul>
    ",

    "create"=>"<h1>Création d'une nouvelle catégorie</h1>
    <ul>
    <li><b>Catégorie activée</b> : cochez cette case si vous souhaitez que la catégorie apparaisse dans la liste des catégories</li>
    <li class='help_separator'></li>
    <li><b>Nom</b> : le nom de la catégorie</li>
    <li><b>Description</b> : un texte descriptif optionnel</li>
    <li><b>Catégorie récapitulative</b> : cochez cette case si vous souhaitez que la catégorie affiche automatiquement tous les indicateurs de l'enquête</li>
    <li class='help_separator'></li>
    <li><b>Appliquer le même contrôle d'accès que pour l'enquête</b> : si la case est cochée, la catégorie est consultable par tous les utilisateurs qui peuvent consulter l'enquête et est modifiable par tous ceux qui peuvent modifier l'enquête</li>
    <li><b>Consultable par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant voir cette catégorie</li>
    <li><b>Modifiable par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant modifier cette catégorie</li>
    <li class='help_separator'></li>
    <li><b>Catégorie en accès public</b> : cochez la case si vous souhaitez publier la catégorie en ligne</li>
    <li><b>Accès protégé par un mot de passe (accès public)</b> : cochez la case si vous souhaitez que seules les personnes connaissant le mot de passe puissent consulter les indicateurs de la catégorie</li>
    <li><b>Mot de passe (accès public)</b> : le mot de passe qui protège l'accès aux indicateurs de la catégorie</li>
    <li><b>Style (accès public)</b> : le modèle de style que vous souhaitez appliquer à la catégorie</li>
    <li><b>Langue utilisée (accès public)</b> : la langue que Toucan doit utiliser pour afficher les informations aux utilisateurs</li>
    </ul>
    ",

    "owner"=>"<h1>Modification du propriétaire d'une catégorie (administration)</h1>
    Sélectionnez un utilisateur dans la liste pour en faire le nouveau propriétaire de la catégorie.
    ",


    
);
?>