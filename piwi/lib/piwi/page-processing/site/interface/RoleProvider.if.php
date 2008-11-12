<?php

/**
 * Interface that role providers have to implement.
 * Role providers are used to for user authentication.
 */
interface RoleProvider {
	/**
	 * Checks if a user has one of the given roles. 
	 * If the user has at least one of the given roles, true is returned, otherwise false.
	 * @param string $username The name of the user.
	 * @param array $role The roles that are allowed.
	 * @return boolean True if user has at least one of the given roles, otherwise false.
	 */
	public function isUserInRole($username, $roles);

	/**
	 * Checks whether the given user exists and validates its password.
	 * Returns true if user exists and password is valid, otherwise false.
	 * @return boolean True if user existspassword is valid, otherwise false.
	 */
	public function isPasswordValid($username, $password);
}
?>