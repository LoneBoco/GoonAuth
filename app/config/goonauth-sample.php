<?php

return array(

	/**
	 * Your SA bbuserid cookie value. Required to auth accounts.
	 */
	'bbuserid' => '0',

	/**
	 * Your SA bbpassword cookie value. Required to auth accounts.
	 */
	'bbpassword' => 'fffffffffffffffff',

	/**
	 * Administrator account
	 */
	'adminAccount' => 'auth.ldap.account',

	/**
	 * URL to your forums
	 */
	'forumUrl' => 'http://forums.goonrathi.com/',

	/**
	 * Max number of users you may sponsor.
	 */
	'maxSponsored' => null,

	/**
	 * LDAP settings
	 */
	'disableLDAP' => false,
	'ldapHost' => 'localhost',
	'ldapPort' => 389,
	'ldapUser' => 'ldap_user',
	'ldapPassword' => 'ldap_password',

	/**
	 * LDAP entry configuration
	 */
	'ldapDN' => 'ou=users,dc=mydomain,dc=com',
	'ldapGroupDN' => 'ou=groups,dc=mydomain,dc=com',	
	'ldapUidStart' => 20000,
	'ldapGid' => 10001,

	/**
	 * For the FLJK org_module.
	 */
	'rsiUser' => '',
	'rsiPass' => 'md5-hash-of-password',

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
