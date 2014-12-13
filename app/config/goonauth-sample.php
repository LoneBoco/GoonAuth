<?php

return array(

	/**
	 * Your SA bbuserid cookie value. Required to auth accounts.
	 */
	'bbuserid' => '125337',

	/**
	 * Your SA bbpassword cookie value. Required to auth accounts.
	 */
	'bbpassword' => 'f819107c3275d0278db4a6a068407f00',

	/**
	 * Administrator account
	 */
	'adminAccount' => 'ipboard.ldap.account',

	/**
	 * URL to your forums
	 */
	'forumUrl' => 'http://forums.goonrathi.com/',

	/**
	 * LDAP settings
	 */
	'ldapHost' => 'localhost',
	'ldapPort' => 389,
	'ldapUser' => 'ldap_user',
	'ldapPassword' => 'ldap_password',
	'ldapDN' => 'ou=users,dc=mydomain,dc=com',

	/**
	 * LDAP entry configuration
	 */
	

	/**
	 * Forum usergroup for authed goons
	 */
	'authId' => 1,

	/**
	 * Forum usergroup for FLJK
	 */
	'fljkId' => 2,

	/**
	 * Forum usergroup for WoL
	 */
	'wolId' => 3,

);
