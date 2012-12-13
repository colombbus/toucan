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
    
    "showAll"=>"<h1>Liste des modèles de style</h1>
    <ul>
    <li><b>Nom</b> : le nom du modèle de style</li>
    <li><b>Description</b> : un texte descriptif optionnel</li>
    </ul>
    ",
    
    "show"=>"<h1>Détails d'un modèle de style</h1>
    <ul>
    <li><b>Nom</b> : le nom du modèle de style</li>
    <li><b>Description</b> : un texte optionnel décrivant le modèle</li>
    <li><b>Répertoire</b> : le nom du répertoire contenant les fichiers du modèle</li>
    <li class='help_separator'></li>
    <li><b>Visible par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant consulter ce modèle</li>
    <li><b>Modifiable par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant modifier ce modèle</li>
    ",

    "edit"=>"<h1>Modification des informations d'un modèle de style</h1>
    <ul>
    <li><b>Nom</b> : le nom du modèle de style</li>
    <li><b>Description</b> : un texte optionnel décrivant le modèle</li>
    <li><b>Répertoire</b> : le nom du répertoire contenant les fichiers du modèle. Si vous modifiez ce répertoire, les fichiers existant seront placés dans ce nouveau répertoire</li>
    <li class='help_separator'></li>
    <li><b>Visible par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant consulter ce modèle</li>
    <li><b>Modifiable par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant modifier ce modèle</li>
    </ul>
    ",

    "create"=>"<h1>Création d'un modèle de style</h1>
    <ul>
    <li><b>Nom</b> : le nom du modèle de style</li>
    <li><b>Description</b> : un texte optionnel décrivant le modèle</li>
    <li><b>Répertoire</b> : le nom du répertoire qui contiendra les fichiers du modèle</li>
    <li class='help_separator'></li>
    <li><b>Visible par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant consulter ce modèle</li>
    <li><b>Modifiable par</b> : l'utilisateur ou l'ensemble des utilisateurs pouvant modifier ce modèle</li>
    <li><b>Copier les fichiers du thème par défaut</b> : cochez cette case si vous voulez baser ce modèle sur le thème par défaut : une copie des fichiers par défaut sera alors placée dans le répertoire du nouveau style</li>
    </ul>
    ",

    "files"=>"<h1>Liste des fichiers utilisés par le modèle</h1>
    Cette page présente la liste des fichiers utilisés par le modèle de style
    <ul>
    <li><b>Fichier</b> : le nom du fichier</li>
    <li><b>Accès directs</b> : pour chaque fichier, des boutons permettent :
    <ul class='no_bullet'>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/edit.png"))." de modifier les informations et le contenu de ce fichier</li>
    <li>".html::image(array("src"=>Kohana::config("toucan.images_directory")."/delete.png"))." de supprimer ce fichier</li>
    </ul>
    </li>
    </ul>
    <h1>Ajouter un fichier (gestionnaires)</h1>
    Si vous avez le droit de modifier le modèle, des boutons en bas de la page vous permettent de créer un nouveau fichier et d'envoyer sur le serveur un fichier qui sera placé dans le répertoire du modèle du style.
    ",

    "owner"=>"<h1>Modification du propriétaire d'un modèle (administration)</h1>
    Sélectionnez un utilisateur dans la liste pour en faire le nouveau propriétaire de ce modèle.
    ",
    
    "sendFile"=>"<h1>Envoyer un nouveau fichier</h1>
    Cette page vous permet d'envoyer sur le serveur un nouveau fichier, qui sera placé dans le répertoire du modèle de style.
    <ul>
    <li><b>Fichier</b> : sélectionnez le fichier sur votre ordinateur</li>
    <li><b>Ecraser le fichier précédent</b> : cochez la case si le fichier existe déjà dans le modèle de style et si vous souhaitez le remplacer par celui-ci</li>
    </ul>
    <h1>Types de fichiers</h1>
    Les extensions de fichier autorisées sont : 'tpl', 'css', 'jpg', 'png', 'jpeg', 'gif', 'bmp'
    ",

    "newFile"=>"<h1>Créer un nouveau fichier</h1>
    Cette page vous permet de créer un nouveau fichier dans le répertoire du modèle de style.
    <ul>
    <li><b>Nom du fichier</b> : entrez le nom du fichier que vous voulez créer</li>
    </ul>
    <h1>Types de fichiers</h1>
    Les extensions de fichier autorisées sont : 'tpl', 'css', 'jpg', 'png', 'jpeg', 'gif', 'bmp'
    ",

    "editFile"=>"<h1>Modification d'un fichier</h1>
    Cette page vous permet de modifier les informations d'un fichier. Pour les fichier 'css' et 'tpl', vous pouvez également modifier le contenu du fichier.
    <ul>
    <li><b>Nom du fichier</b> : le nom du fichier dans le répertoire du modèle</li>
    <li><b>Contenu du fichier</b> : vous pouvez directement éditer le contenu d'un fichier 'css' ou 'tpl'</li>
    </ul>
    <h1>Types de fichiers</h1>
    Les extensions de fichier autorisées sont : 'tpl', 'css', 'jpg', 'png', 'jpeg', 'gif', 'bmp'
    ",

    "usage"=>"<h1>Utilisation du modèle</h1>
    Cette page donne la liste des questionnaires utilisant le modèle de style.
    ",
    
);
?>