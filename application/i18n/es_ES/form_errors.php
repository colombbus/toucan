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
        'default' => "Fecha inválida (DD/MM/AAAA)",
    ),
    'choice' => Array
    (
        'default' => "El texto debe tener entre 1 y 127 letras"
    ),
    'city' => Array
    (
        'default' => "Ciudad inválida",
        'length' => "La ciudad debe tener entre 1 y 127 letras",
    ),
    'email' => Array
    (
        'default' => 'Direción email inválida',
        'length' => "La dirección email debe tener entre 1 y 127 letras",
        'required' => "La dirección email es obligatoria",
        'unknown' => "Esta dirección email no corresponde a un usuario registrado",
        'uniqueEmail' => "Esta dirección email ya está atribuida",
    ),
    'emailBool' => Array
    (
        'default' => "Datos inválidos",
    ),
    'end_date' => Array
    (
        'before_start' => "La fecha de fín es anterior a la fecha de principio",
        'valid_date' => "Fecha inválida (DD/MM/AAAA)",
    ),
    'extra' => Array
    (
        'default' => "Datos inválidos",
        'length' => "500 letras máximo",
    ),
    'extraBool' => Array
    (
        'default' => "Datos inválidos",
    ),
    'file' => Array
    (
        'size' => "El archivo debe hacer menos de 2 Mo",
    ),
    'file_title' => Array
    (
        'length' => "El titulo debe tener entre 1 y 127 letras",
    ),
    'firstname' => Array
    (
        'default' => "Nombre inválido",
        'length' => "El nombre debe tener entre 1 y 127 letras",
        'required' => "El nombre es obligatorio",
    ),
    'formation' => Array
    (
        'default' => "datos inválidos",
        'length' => "500 letras máximo",
    ),
    'formationBool' => Array
    (
        'default' => "Datos inválidos",
    ),
    'logo' => Array
    (
        'default' => "Datos inválidos",
    ),
    'long_value_too_long' => 'El valor no debe exceder 500 letras',
    'main' => Array
    (
        'groups_wrong_id' => "Error al seleccionar los grupos",
        'email_problem' => "No se puede enviar emails por el momento",
    ),
    'name' => Array
    (
        'default' => "Apellido inválido",
        'length' => "El apellido debe tenr entre 1 y 127 letras",
        'required' => "el apellido es obligatorio",
        'uniqueName' => 'Ese apellido ya es utilizado',
    ),
    'password' => Array
    (
        'default' => "Error",
        'length' => "La contraseña debe tener entre 5 y 50 letras",
        'required' => "Debe escribir una contraseña",
    ),
    'password_confirm' => Array
    (
        'default' => "Error",
        'length' => "", // nothing since error message will come from password1
        'matches' => "La segunda contraseña no corresponde a la primera",
        'required' => "Escribir su contraseña nuevamente, para validar",
    ),
    'photo' => Array
    (
        'default' => "Error al cargar la foto",
        'matches' => "La segunda contraseña no corresponde a la primera",
    ),
    'photoBool' => Array
    (
        'default' => "Datos inválidos",
    ),
    'selection_id' => Array
    (
        'numerical' => "Este test no puede ser utilizado porque la variable puede tener valores no numéricos",
    ),
    'sex' => Array
    (
        'default' => "Datos inválidos",
        'required' => "Favor de especificar si es hombre o mujer",
    ),
    'short_value_too_long' => 'El valor no debe exceder 127 letras',
    'start_date' => Array
    (
        'valid_date' => "Fecha inválida (DD/MM/AAAA)",
    ),
    'text' => Array
    (
        'default' => "Texto incorrecto",
        'length' => "Texto demasiado largo",
        'required' => "Escribir texto",
    ),
    'text_too_long' => 'El texto no debe exceder 65 536 letras',
    'triggers' => Array
    (
        'empty_choices' => "Debe seleccionar una opción",
    ),
    'unknown_choice' => 'Opción incorrecta',
    'username' => Array
    (
        'alpha_dash' => "Unicamente letras, cifras et y los carácteres '_' y '#' son autorizados",
        'correctUsername' => "Unicamente letras, cifras et y los carácteres '_' y '#' son autorizados",
        'default' => "Nombre de usuario inválido",
        'length' => "El nombre del usuario debe tener entre 4 y 32 letras",
        'required' => "El nombre del usuario es obligatorio",
        'uniqueUsername' => "Ese nombre de usuario ya ha sido atribuido",
    ),
    'variable' => Array
    (
        'uniqueVariableName' => "Este nombre de variable ya existe",
    ),
    'variable_name' => Array
    (
        'uniqueVariableName' => "Este nombre de variable ya existe",
    ),
    'value' => Array
    (
        'required' => "Debe escribir un valor",
        'numeric' => "Debe escribir un valor numérico",
    ),
    'variable_id' => Array
    (
        'required' => "Debe especificar una variable",
    ),
    'value_min' => Array
    (
        'default' => "Datos inválidos",
        'required' => "Debe especificar al menos un valor mínimo o máximo",
    ),
    'value_max' => Array
    (
        'default' => "Datos inválidos",
        'required' => "Debe especificar al menos un valor mínimo o máximo",
        'inferior' => "El valor máximo debe ser superior al valor mínimo",
    ),
    'value_required' => 'Respuesta obligatoria',
    'wrong_integer' => 'Escribir un nombre entero',
    'wrong_password' => 'Contraseña incorrecta',
    'wrong_real' => 'Escribir un valor numérico',

    );
?>