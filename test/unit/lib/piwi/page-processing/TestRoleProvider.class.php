<?php
/**
 * Custom RoleProvider.
 * Uses some dummy methods.
 */
class TestRoleProvider implements RoleProvider {
	/**
	 * Checks if a user has one of the given roles. 
	 * If the user has at least one of the given roles, true is returned, otherwise false.
	 * @param string $username The name of the user.
	 * @param array $role The roles that are allowed.
	 * @return boolean True if user has at least one of the given roles, otherwise false.
	 */
	public function isUserInRole($username, array $roles) {
		foreach ($this->getUserRoles($username) as $role) {
       		if (in_array($role, $roles)) {
       			return true;
       		}
		}	
		return false;
	}

	/**
	 * Checks whether the given user exists and validates its password.
	 * Returns true if user exists and password is valid, otherwise false.
	 * This method is just a dummy, normally you will query a database here.
	 * @return boolean True if user existspassword is valid, otherwise false.
	 */
	public function isPasswordValid($username, $password) {
		return true;
	}
	
	/**
	 * Returns all roles of a user.
	 * This method is just a dummy, normally you will query a database here.
	 * @return array All roles of a user.
	 */
	private function getUserRoles($username) {
		return array('admin');
	}
	
	/**
	 * Encrypts the password with a custom algorithm.
	 * @param string $password The password.
	 * @return string The encrypted password.
	 */
	public function encryptPassword($password) {
		return sha1($password);
	}
}
?>