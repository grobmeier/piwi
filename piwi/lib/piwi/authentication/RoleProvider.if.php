<?php

/**
 * Interface that role providers have to implement.
 * Role providers are used to for user authentication.
 */
interface RoleProvider {
	/**
	 * Checks if a user has one of the given roles. 
	 * If the user has at least one of the given roles, true is returned, otherwise false.
	 * 
	 * If a user is not authentificated and the roles array contains the
	 * role "anonymous", the user is in role.
	 * 
	 * @param string $username The name of the user.
	 * @param array $role The roles that are allowed.
	 * @return boolean True if user has at least one of the given roles, otherwise false.
	 */
	public function isUserInRole($username, array $roles);

	/**
	 * Checks whether the given user exists and validates its password.
	 * Returns true if user exists and password is valid, otherwise false.
	 * @param string $username The username.
	 * @param string $password The SHA1-encrypted password.
	 * @return boolean True if user existspassword is valid, otherwise false.
	 */
	public function isPasswordValid($username, $password);
}
?>