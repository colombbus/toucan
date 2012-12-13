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
    "showAll"=>"<h1>Liste des utilisateurs</h1>
    Cette page présente la liste des utilisateurs enregistrés dans Toucan.
    <ul>
    <li><b>Prénom</b> : le prénom de l'utilisateur</li>
    <li><b>Nom</b> : le nom de l'utilisateur</li>
    <li><b>Nom d'utilisateur</b> : le nom d'utilisateur utilisé dans Toucan</li>
    </ul>
    
    ",
    "show"=>"<h1>Affichage du détail d'un utilisateur</h1>
    <ul>
    <li><b>Prénom</b> : le prénom de l'utilisateur</li>
    <li><b>Nom</b> : le nom de l'utilisateur</li>
    <li><b>Sexe</b> : homme / femme</li>
    <li class='help_separator'></li>
    <li><b>Nom d'utilisateur</b> : le nom d'utilisateur enregistré dans Toucan</li>
    <li><b>E-mail</b> : l'adresse e-mail de l'utilisateur (cette information peut être cachée par l'utilisateur)</li>
    <li class='help_separator'></li>
    <li><b>Localité</b> : le lieu où vit l'utilisateur</li>
    <li><b>Né le</b> : la date de naissance de l'utilisateur</li>
    <li><b>Informations complémentaires</b> : des informations éventuelles entrées par l'utilisateur</li>
    </ul>
    ",

    "profile"=>"<h1>Affichage de vos informations personnelles</h1>
    <ul>
    <li><b>Prénom</b> : votre prénom</li>
    <li><b>Nom</b> : votre nom</li>
    <li><b>Sexe</b> : homme / femme</li>
    <li class='help_separator'></li>
    <li><b>Nom d'utilisateur</b> : votre nom d'utilisateur enregistré dans Toucan</li>
    <li><b>E-mail</b> : votre adresse e-mail</li>
    <li class='help_separator'></li>
    <li><b>Localité</b> : votre localité</li>
    <li><b>Né le</b> : votre date de naissance</li>
    <li><b>Informations complémentaires</b> : des informations complémentaires éventuelles</li>
    </ul>
    ",

    "changePassword"=>"<h1>Modification du mot de passe</h1>
    <ul>
    <li><b>Mot de passe</b> : le nouveau mot de passe que vous souhaitez utiliser</li>
    <li><b>Vérification du mot de passe</b> : le nouveau mot de passe une nouvelle fois</li>
    </ul>
    ",
    
    "edit"=>"<h1>Modification des informations d'un utilisateur</h1>
    <ul>
    <li><b>Prénom</b> : le prénom de l'utilisateur</li>
    <li><b>Nom</b> : le nom de l'utilisateur</li>
    <li><b>Sexe</b> : homme / femme</li>
    <li class='help_separator'></li>
    <li><b>Nom d'utilisateur</b> : le nom d'utilisateur enregistré dans Toucan</li>
    <li><b>E-mail</b> : l'adresse e-mail de l'utilisateur (cette information peut être cachée par l'utilisateur)</li>
    <li><b>E-mail public</b> : cochez cette case si vous voulez que les autres utilisateurs de Toucan puissent voir l'adresse</li>
    <li class='help_separator'></li>
    <li><b>Localité</b> : le lieu où vit l'utilisateur</li>
    <li><b>Né le</b> : la date de naissance de l'utilisateur</li>
    <li><b>Informations complémentaires</b> : des informations complémentaires qui s'afficheront sur la page personnelle de l'utilisateur</li>
    <li class='help_separator'></li>
    <li><b>Avatar</b> : une image éventuelle utilisée sur la page de profil de l'utilisateur</li>
    <li><b>Supprimer l'avatar</b> : cochez cette case si vous souhaitez supprimer un avatar enregistré précédemment</li>
    </ul>
    ",

    "create"=>"<h1>Création d'un nouvel utilisateur (administration)</h1>
    <ul>
    <li><b>Prénom</b> : le prénom de l'utilisateur</li>
    <li><b>Nom</b> : le nom de l'utilisateur</li>
    <li><b>Sexe</b> : homme / femme</li>
    <li class='help_separator'></li>
    <li><b>Nom d'utilisateur</b> : le nom d'utilisateur enregistré dans Toucan</li>
    <li><b>Mot de passe</b> : le mot de passe que l'utilisateur utilisera pour se connecter à Toucan</li>
    <li><b>Vérification du mot de passe</b> : le mot de passe une nouvelle fois</li>
    <li><b>E-mail</b> : l'adresse e-mail de l'utilisateur (cette information peut être cachée par l'utilisateur)</li>
    <li><b>E-mail public</b> : cochez cette case si vous voulez que les autres utilisateurs de Toucan puissent voir l'adresse</li>
    <li class='help_separator'></li>
    <li><b>Localité</b> : le lieu où vit l'utilisateur</li>
    <li><b>Né le</b> : la date de naissance de l'utilisateur</li>
    <li><b>Informations complémentaires</b> : des informations complémentaires qui s'afficheront sur la page personnelle de l'utilisateur</li>
    <li class='help_separator'></li>
    <li><b>Avatar</b> : une image éventuelle utilisée sur la page de profil de l'utilisateur</li>
    </ul>
    ",

    "register"=>"<h1>Inscription à Toucan</h1>
    <ul>
    <li><b>Prénom</b> : votre prénom</li>
    <li><b>Nom</b> : votre nom</li>
    <li><b>Sexe</b> : homme / femme</li>
    <li class='help_separator'></li>
    <li><b>Nom d'utilisateur</b> : le nom d'utilisateur que vous souhaitez utiliser dans Toucan</li>
    <li><b>Mot de passe</b> : le mot de passe que vous utiliserez pour vous connecter à Toucan</li>
    <li><b>Vérification du mot de passe</b> : entrez le mot de passe une nouvelle fois</li>
    <li><b>E-mail</b> : une adresse e-mail valide que Toucan utilisera pour vous envoyer des notifications</li>
    <li><b>E-mail public</b> : cochez cette case si vous voulez que les autres utilisateurs de Toucan puissent voir votre adresse</li>
    <li class='help_separator'></li>
    <li><b>Localité</b> : le lieu où vous vivez</li>
    <li><b>Né le</b> : votre date de naissance</li>
    <li><b>Informations complémentaires</b> : toute information complémentaire que vous souhaitez afficher sur votre page personnelle</li>
    <li class='help_separator'></li>
    <li><b>Avatar</b> : une image éventuelle qui sera affichée sur votre page personnelle</li>
    </ul>
    ",
    
    "sendPassword"=>"<h1>Récupération du  mot de passe</h1>
    Entrez l'adresse e-mail associée à votre compte dans Toucan : votre mot de passe vous sera automatiquement envoyé.
    ",

    "setGroups"=>"<h1>Définition des groupes auxquels appartient l'utilisateur (administration)</h1>
    Cochez les groupes auxquels vous souhaitez faire appartenir l'utilisateur.
    ",

    
);
?>