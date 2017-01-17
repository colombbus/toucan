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

$lang = array
(
    'birthday' => Array
    (
        'default' => "Date invalide (JJ/MM/AAAA)",
    ),
    'choice' => Array
    (
        'default' => "Le texte doit avoir entre 1 et 127 caractères"
    ),
    'city' => Array
    (
        'default' => "Ville invalide",
        'length' => "La ville doit avoir entre 1 et 127 caractères",
    ),
    'description' => Array
    (
        'default' => "Description invalide",
        'length' => "La description ne doit pas dépasser les 500 caractères",
    ),
    'directory' => Array
    (
        'uniqueDirectory' => "Ce répertoire existe déjà",
        'alpha_numeric' => "Nom de répertoire incorrect",
    ),
    'email' => Array
    (
        'default' => 'Adresse email invalide',
        'length' => "L'adresse email doit avoir entre 1 et 127 caractères",
        'required' => "L'adresse email est obligatoire",
        'uniqueEmail' => 'Cette adresse email est déjà attribuée',
        'unknown' => "Cette adresse ne correspond à aucun utilisateur enregistré",
    ),
    'emailBool' => Array
    (
        'default' => "Données invalides",
    ),
    'end_date' => Array
    (
        'before_start' => "La date de fin est antérieure à la date de début",
        'valid_date' => "Date invalide (JJ/MM/AAAA)",
    ),
    'extra' => Array
    (
        'default' => "Données invalides",
        'length' => "500 caractères max",
    ),
    'extraBool' => Array
    (
        'default' => "Données invalides",
    ),
    'file' => Array
    (
        'size' => "Le fichier ne doit pas faire plus de 2 Mo",
    ),
    'file_title' => Array
    (
        'length' => "Le titre doit avoir entre 1 et 127 caractères",
    ),
    'firstname' => Array
    (
        'default' => "Prénom invalide",
        'length' => "Le prénom doit avoir entre 1 et 127 caractères",
        'required' => "Le prénom est obligatoire",
    ),
    'formation' => Array
    (
        'default' => "Données invalides",
        'length' => "500 caractères max",
    ),
    'formationBool' => Array
    (
        'default' => "Données invalides",
    ),
    'logo' => Array
    (
        'default' => "Données invalides",
    ),
    'long_value_too_long' => 'La valeur ne doit pas dépasser 1000 caractères',
    'main' => Array
    (
        'email_problem' => "L'envoi de mail est actuellement impossible. Merci de contacter un administrateur.",
        'groups_wrong_id' => "Erreur lors de la sélection des groupes",
    ),
    'name' => Array
    (
        'default' => "Nom invalide",
        'length' => "Le nom doit avoir entre 1 et 127 caractères",
        'required' => "Le nom est obligatoire",
        'uniqueName' => 'Ce nom est déjà utilisé',
    ),
    'password' => Array
    (
        'default' => "Erreur",
        'length' => "Le mot de passe doit avoir entre 5 et 50 caractères",
        'required' => "Vous devez entrer un mot de passe",
    ),
    'password_confirm' => Array
    (
        'default' => "Erreur",
        'length' => "", // nothing since error message will come from password1
        'matches' => "Le deuxième mot de passe ne correspond pas au premier",
        'required' => "Vous devez entrer votre mot de passe une deuxième fois, pour validation",
    ),
    'photo' => Array
    (
        'default' => "Erreur de chargement de la photo",
        'length' => "", // nothing since error message will come from password1
        'matches' => "Le deuxième mot de passe ne correspond pas au premier",
    ),
    'photoBool' => Array
    (
        'default' => "Données invalides",
    ),
    'selection_id_simple' => Array
    (
        'numerical' => "Ce test ne peut pas être utilisé car la variable peut prendre des valeurs non numériques",
        'multiple' => "Ce test ne peut pas être appliqué car la variable peut prendre plusieurs valeurs simultannément",
    ),
    'selection_id_simple_numerical' => Array
    (
        'multiple' => "Ce test ne peut pas être appliqué car la variable peut prendre plusieurs valeurs simultannément",
    ),
    'selection_id_multiple' => Array
    (
        'numerical' => "Ce test ne peut pas être utilisé car la variable peut prendre des valeurs non numériques",
        'simple' => "Ce test ne peut pas être appliqué car la variable ne peut prendre qu'une seule valeur",
    ),
    'sex' => Array
    (
        'default' => "Données invalides",
        'required' => "Merci de préciser si vous êtes un homme ou une femme",
    ),
    'short_value_too_long' => 'La valeur ne doit pas dépasser 127 caractères',
    'start_date' => Array
    (
        'valid_date' => "Date invalide (JJ/MM/AAAA)",
    ),
    'text' => Array
    (
        'default' => "Texte incorrect",
        'length' => "Texte trop long",
        'required' => "Vous devez entrer un texte",
    ),
    'text_too_long' => 'Le texte ne doit pas dépasser 65 536 caractères',
    'triggers' => Array
    (
        'empty_choices' => "Vous devez sélectionner au moins un choix",
    ),
    'unknown_choice' => 'Choix incorrect',
    'upload' => Array
    (
        'default' => "Fichier non autorisé",
    ),
    'username' => Array
    (
        'alpha_dash' => "Seuls les lettres, chiffres et caractères '_' et '#' sont autorisés",
        'correctUsername' => "Seuls les lettres, chiffres et caractères '_' et '#' sont autorisés",
        'default' => "Nom d'utilisateur invalide",
        'length' => "Le nom d'utilisateur doit avoir entre 4 et 32 caractères",
        'required' => "Le nom d'utilisateur est obligatoire",
        'uniqueUsername' => "Ce nom d'utilisateur est déjà attribué",
    ),
    'variable' => Array
    (
        'uniqueVariableName' => "Ce nom de variable existe déjà",
    ),
    'variable_name' => Array
    (
        'uniqueVariableName' => "Ce nom de variable existe déjà",
    ),
    'value' => Array
    (
        'numeric' => "Vous devez entrer une valeur numérique",
        'required' => "Vous devez entrer une valeur",
    ),
    'variable_id' => Array
    (
        'required' => "Vous devez préciser une variable",
    ),
    'value_min' => Array
    (
        'default' => "Données invalides",
        'required' => "Vous devez préciser au moins une valeur minimale ou maximale",
    ),
    'value_max' => Array
    (
        'default' => "Données invalides",
        'inferior' => "La valeur maximale doit être supérieure à la valeur minimale",
        'required' => "Vous devez préciser au moins une valeur minimale ou maximale",
    ),
    'value_required' => 'Réponse obligatoire',
    'wrong_integer' => 'Veuillez entrer un nombre entier',
    'wrong_password' => 'Mot de passe incorrect',
    'wrong_real' => 'Veuillez entrer une valeur numérique',
    );
?>
