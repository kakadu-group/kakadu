<?php
/**
 * Part of the Sentry bundle for Laravel.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Sentry
 * @version    1.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2012, Cartalyst LLC
 * @link       http://cartalyst.com
 */

return array(

	/** General Exception Messages **/
	'account_not_activated'  => 'Benutzer hat sein Account nicht aktiviert.',
	'account_is_disabled'    => 'Dieser Account wurde deaktiviert.',
	'invalid_limit_attempts' => 'Sentry Config Item: "limit.attempts" muss ein Integer gr&ouml;&szlig;er als 0 sein',
	'invalid_limit_time'     => 'Sentry Config Item: "limit.time" muss ein Integer gr&ouml;&szlig;er als 0 sein',
	'login_column_empty'     => 'Sie m&uuml;ssen "login_column" in Sentry config setzen.',

	/** Group Exception Messages **/
	'group_already_exists'      => 'Der Gruppenname ":group" existiert bereits.',
	'group_level_empty'         => 'Sie m&uuml;ssen ein Level f&uuml;r die Gruppe spezifizieren.',
	'group_name_empty'          => 'Sie m&uuml;ssen einen Namen f&uuml;r die Gruppe spezifizieren.',
	'group_not_found'           => 'Die Gruppe ":group" existiert nicht.',
	'invalid_group_id'          => 'Die Gruppen ID muss ein g&uuml;ltiger Integer gr&ouml;&szlig;er als 0 sein.',
	'not_found_in_group_object' => 'Das Feld ":field" existiert nicht im Objekt "group".',
	'no_group_selected'         => 'Es wurde keine Gruppe ausgew&auml;hlt.',
	'user_already_in_group'     => 'Der Benutzer ist bereits in der Gruppe ":group".',
	'user_not_in_group'         => 'Der Benutzer ist nicht in der Gruppe ":group".',

	/** User Exception Messages **/
	'column_already_exists'           => ':column existiert bereits.',
	'column_and_password_empty'       => ':column und Passwort d&uuml;rfen nicht leer sein.',
	'column_email_and_password_empty' => ':column, Email und Passwort d&uuml;rfen nicht leer sein.',
	'column_is_empty'                 => ':column darf nicht leer sein.',
	'email_already_in_use'            => 'Die Email wird bereits verwendet.',
	'invalid_old_password'            => 'Altes Passwort ist nicht korrekt',
	'invalid_user_id'                 => 'Die Benutzer ID muss ein g&uuml;ltiger Integer gr&ouml;&szlig;er als 0 sein.',
	'no_user_selected'                => 'Sie m&uuml;ssen zuerst einen Benutzer ausw&auml;hlen.',
	'no_user_selected_to_delete'      => 'Es ist kein Benutzer ausgew&auml;hlt zum L&ouml;schen.',
	'no_user_selected_to_get'         => 'Es ist kein Benutzer ausgew&auml;hlt zur&uuml;ckzugeben.',
	'not_found_in_user_object'        => 'Das Feld ":field" existiert nicht im Objekt "user".',
	'password_empty'                  => 'Das Passwort darf nicht leer sein.',
	'user_already_enabled'            => 'Der Benutzer ist bereits aktiviert',
	'user_already_disabled'           => 'Der Benutzer ist bereits deaktiviert',
	'user_not_found'                  => 'Der Benutzer existiert nicht.',
	'username_already_in_use'         => 'Der Benutzername wird bereits verwendet.',

	/** Attempts Exception Messages **/
    'login_ip_required'    => 'Login Id und IP Addresse werden ben&ouml;tigt um ein Loginversuch hinzuzuf&uuml;gen.',
    'single_user_required' => 'Versuche k&ouml;nnen nur an einem einzigen Benutzer hinzugef&uuml;gt werden, ein Array war gegeben.',
    'user_suspended'       => 'Sie wurden f&uuml;r die Anmeldung an Ihr Account ":account" f&uuml;r :time Minuten gesperrt.',

    /** Hashing **/
    'hash_strategy_null'      => 'Die Hashstrategie ist null oder leer. Es muss eine Hashstrategie gesetzt sein.',
    'hash_strategy_not_exist' => 'Hashstrategie Datei existiert nicht.',

	/** Permissions Messages **/
	'no_rules_added'    => 'Oops, Sie haben vergessen irgendeine Regel zu spezifizieren.',
	'rule_not_found'    => 'Die Regel :rule, existiert nicht in Ihren festgelegten Regeln. Bitte &uuml;berpr&uuml;fen Sie ihre Regeln in der Sentry config.',
	'permission_denied' => 'Oops, Sie haben keine Berechtigungen um auf :resource zuzugreifen',

);
