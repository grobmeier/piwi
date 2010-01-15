<?php
/**
 * XML RoleProvider.
 * Uses an xml file for a simple authentification.
 * Can be enabled in configuration xml with:
 * 
 * <authentication enabled="1" roleProvider="XmlRoleProvider" loginPageId="login">
 *		<authFile>custom/resources/auth.xml</authFile>
 * </authentication>
 * 
 * The XML file for authentification must follow the auth.xsd structure:
 * 
 * <?xml version="1.0" encoding="UTF-8"?>
 * <piwi-users xmlns="http://piwi.googlecode.com/xsd/auth"
 * 	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 * 	xsi:schemaLocation="http://piwi.googlecode.com/xsd/auth ../../resources/xsd/auth.xsd">
 *   <user name="piwi" password="piwi1" roles="admin" />
 *   <user name="test" password="test1" roles="admin"  />
 *   <user name="both" password="both1" roles="admin,role1" />
 * </piwi-users>
 */
class XmlRoleProvider implements RoleProvider {
	/** reference to the authentification xml file */
	private $auth = null;
	
	/**
	 * Checks if a user has one of the given roles. 
	 * If the user has at least one of the given roles, true is returned, otherwise false.
	 * @param string $username The name of the user.
	 * @param array $role The roles that are allowed.
	 * @return boolean True if user has at least one of the given roles, otherwise false.
	 */
	public function isUserInRole($username, array $roles) {
		foreach ($this->_getUserRoles($username) as $role) {
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
	 * @param string $username The username.
	 * @param string $password The SHA1-encrypted password.
	 * @return boolean True if user existspassword is valid, otherwise false.
	 */
	public function isPasswordValid($username, $password) {
		foreach ($this->auth as $user) {
		    if ($username == $user->attributes()->name && 
		    	$password == sha1($user->attributes()->password)) {
		    	return true;
		    } 
		}
		return false;
	}
	
	/**
	 * Returns all roles of a user.
	 * This method is just a dummy, normally you will query a database here.
	 * @return array All roles of a user.
	 */
	private function _getUserRoles($username) {
		foreach ($this->auth as $user) {
		    if ($username == $user->attributes()->name) {
				$rolesString = $user->attributes()->roles;
				return explode(',',$rolesString);
		    } 
		}
		return array();
	}
	
	/**
	 * Set the auth file as defined in the config.xml
	 */
	public function setAuthFile($authFile) {
	    $this->auth = simplexml_load_file($authFile);
		$this->auth->registerXPathNamespace('auth', 'http://piwi.googlecode.com/xsd/auth');
	}
}
?>