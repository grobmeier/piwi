<?php
require_once ('test/PiwiWebTestCase.php');

class AuthenticationTest extends PiwiWebTestCase {
	
	function testAuthenticationWithoutCookies() {
		$this->get(self :: $HOST . 'authentication_protected.html');
		
		// check if login screen appears
		$this->assertWantedText('Login', 'Login not displayed.');
		$this->assertWantedText('Name', 'Login not displayed.');
		$this->assertWantedText('Password', 'Login not displayed.');
		
		// check if login fails
		$this->clickSubmit('Login');
		$this->assertWantedText('Login failed.', 'Login did not fail.');
		
		// go to login page again		
		$this->get(self :: $HOST . 'authentication_protected.html');
		
		// check if login screen appears
		$this->assertWantedText('Login', 'Login not displayed.');
		$this->assertWantedText('Name', 'Login not displayed.');
		$this->assertWantedText('Password', 'Login not displayed.');		
		
		// check if login succeeds
		$this->setFieldByName('1Name', 'test');
		$this->setFieldByName('1Password', 'test');
		$this->clickSubmit('Login');
		$this->assertWantedText('Protected page', 'Login failed.');
		
		// leave page and go to it again
		$this->get(self :: $HOST . 'default.html');
		$this->get(self :: $HOST . 'authentication_protected.html');
		$this->assertWantedText('Protected page', 'Login failed.');
		
		// remove session cookie		
		$this->ignoreCookies();
		$this->get(self :: $HOST . 'authentication_protected.html');
		$this->assertWantedText('Login', 'Login not displayed.');
		$this->assertWantedText('Name', 'Login not displayed.');
		$this->assertWantedText('Password', 'Login not displayed.');
	}
	
	function testAuthenticationWithCookies() {
		$this->get(self :: $HOST . 'authentication_protected.html');
		
		// check if login screen appears
		$this->assertWantedText('Login', 'Login not displayed.');
		$this->assertWantedText('Name', 'Login not displayed.');
		$this->assertWantedText('Password', 'Login not displayed.');
		
		// check if login fails
		$this->setFieldByName('1Cookies', 'Cookies');
		$this->clickSubmit('Login');
		$this->assertWantedText('Login failed.', 'Login did not fail.');
		
		// go to login page again		
		$this->get(self :: $HOST . 'authentication_protected.html');
		
		// check if login screen appears
		$this->assertWantedText('Login', 'Login not displayed.');
		$this->assertWantedText('Name', 'Login not displayed.');
		$this->assertWantedText('Password', 'Login not displayed.');		
		
		// check if login succeeds
		$this->setFieldByName('1Name', 'test');
		$this->setFieldByName('1Password', 'test');
		$this->setFieldByName('1Cookies', 'Cookies');
		$this->clickSubmit('Login');
		$this->assertWantedText('Protected page', 'Login failed.');
		$this->assertCookie('username', '%2BI-.%01%00', 'Cookie invalid.');
		$this->assertCookie('password', '%2BI-.%01%00', 'Cookie invalid.');
		
		// leave page and go to it again
		$this->get(self :: $HOST . 'default.html');
		$this->get(self :: $HOST . 'authentication_protected.html');
		$this->assertWantedText('Protected page', 'Login failed.');
		
		// remove all cookies
		$this->ignoreCookies();
		$this->get(self :: $HOST . 'authentication_protected.html');
		$this->assertWantedText('Login', 'Login not displayed.');
		$this->assertWantedText('Name', 'Login not displayed.');
		$this->assertWantedText('Password', 'Login not displayed.');		
	}
	
	function testAuthenticationWithExistingCookie() {
		$this->setCookie('username', '%2BI-.%01%00');
		$this->setCookie('password', '%2BI-.%01%00');
		$this->get(self :: $HOST . 'authentication_protected.html');
		$this->assertWantedText('Protected page', 'Login failed.');
	}
	
	function testAuthenticationWithExistingInvalidCookie() {
		$this->setCookie('username', '666');
		$this->setCookie('password', '666');
		$this->get(self :: $HOST . 'authentication_protected.html');
		$this->assertWantedText('Login', 'Login not displayed.');
	}
	
	function testLogoutWithoutCookies() {
		$this->get(self :: $HOST . 'authentication_protected.html');
		$this->assertWantedText('Login', 'Login not displayed.');
		
		// check if login succeeds
		$this->setFieldByName('1Name', 'test');
		$this->setFieldByName('1Password', 'test');
		$this->clickSubmit('Login');
		$this->assertWantedText('Protected page', 'Login failed.');
		
		// check if logout works
		$this->get(self :: $HOST . 'login.html');
		$this->clickSubmit('Logout');
		$this->assertWantedText('Logged out successfully.', 'Logout not successful.');
		
		// try to access protected page again
		$this->get(self :: $HOST . 'authentication_protected.html');
		$this->assertWantedText('Login', 'Login not displayed.');
		$this->assertWantedText('Name', 'Login not displayed.');
		$this->assertWantedText('Password', 'Login not displayed.');
	}
	
	function testLogoutWithCookies() {
		$this->get(self :: $HOST . 'authentication_protected.html');
		$this->assertWantedText('Login', 'Login not displayed.');
		
		// check if login succeeds
		$this->setFieldByName('1Name', 'test');
		$this->setFieldByName('1Password', 'test');
		$this->setFieldByName('1Cookies', 'Cookies');
		$this->clickSubmit('Login');
		$this->assertWantedText('Protected page', 'Login failed.');
		$this->assertCookie('username', '%2BI-.%01%00', 'Cookie invalid.');
		$this->assertCookie('password', '%2BI-.%01%00', 'Cookie invalid.');
		
		// check if logout works
		$this->get(self :: $HOST . 'login.html');
		$this->clickSubmit('Logout');
		$this->assertWantedText('Logged out successfully.', 'Logout not successful.');
		$this->assertCookie('username', '', 'Cookie invalid.');
		$this->assertCookie('password', '', 'Cookie invalid.');
		
		// try to access protected page again
		$this->get(self :: $HOST . 'authentication_protected.html');
		$this->assertWantedText('Login', 'Login not displayed.');
		$this->assertWantedText('Name', 'Login not displayed.');
		$this->assertWantedText('Password', 'Login not displayed.');
	}
}
?>