<?php
class XmlRoleProviderTest extends UnitTestCase {

	function testIsPasswordValid() {
		$provider = new XmlRoleProvider();
		$provider->setAuthFile(dirname(__FILE__) . '/data/auth.xml');
		
		$this->assertTrue($provider->isPasswordValid('piwi','a94a8fe5ccb19ba61c4c0873d391e987982fbbd3'));
		$this->assertFalse($provider->isPasswordValid('piwi','somethingelse'));
		$this->assertFalse($provider->isPasswordValid('notexistend','a94a8fe5ccb19ba61c4c0873d391e987982fbbd3'));
		// correct password but not encrypted
		$this->assertFalse($provider->isPasswordValid('piwi','test'));
		$this->assertTrue($provider->isPasswordValid('test','a94a8fe5ccb19ba61c4c0873d391e987982fbbd3'));
	}
	
	function testIsUserInRoleUnauthenticated() {
		$provider = new XmlRoleProvider();
		$provider->setAuthFile(dirname(__FILE__) . '/data/auth.xml');
		
		$roles[0] = 'admin';
		$this->assertFalse($provider->isUserInRole('piwi', $roles));
		$roles[1] = '!';
		$this->assertTrue($provider->isUserInRole('piwi', $roles));
	}
	
	function testEncryptPassword() {
		$provider = new XmlRoleProvider();
		$provider->setAuthFile(dirname(__FILE__) . '/data/auth.xml');
		$this->assertEqual('a94a8fe5ccb19ba61c4c0873d391e987982fbbd3',$provider->encryptPassword('test'));
	}
	
	function testLogin() {
		BeanFactory::initialize(dirname(__FILE__) . '/data/context.xml');
		$this->assertTrue(UserSessionManager::loginUser('piwi','test'));
		$this->assertTrue('piwi', UserSessionManager::getUserName());
		$this->assertFalse(UserSessionManager::loginUser('piwi','wrong'));
		$this->assertTrue('piwi', UserSessionManager::getUserName());
		$this->assertFalse(UserSessionManager::loginUser('piwi2','test'));
		$this->assertTrue('piwi', UserSessionManager::getUserName());
		$this->assertFalse(UserSessionManager::loginUser('both','wrong'));
		$this->assertTrue('piwi', UserSessionManager::getUserName());
		$this->assertTrue(UserSessionManager::loginUser('both','test'));
		$this->assertTrue('both', UserSessionManager::getUserName());
		UserSessionManager::logoutUser();
		$this->assertNull(UserSessionManager::getUserName());
	}
	
	function testIsUserInRoleAuthenticated() {
		BeanFactory::initialize(dirname(__FILE__) . '/data/context.xml');
		$this->assertTrue(UserSessionManager::loginUser('piwi','test'));
		$this->assertTrue('piwi', UserSessionManager::getUserName());
		$provider = BeanFactory::getBeanById('configurationManager')->getRoleProvider();
		
		$roles[0] = 'admin';
		$this->assertTrue($provider->isUserInRole('piwi', $roles));
		
		$roles[0] = '*';
		$this->assertTrue($provider->isUserInRole('piwi', $roles));
		
		$roles[0] = 'somethingelse';
		$this->assertFalse($provider->isUserInRole('piwi', $roles));
	}
	
	function testThrowException() {
		BeanFactory::initialize(dirname(__FILE__) . '/data/context.xml');
		$this->assertTrue(UserSessionManager::loginUser('piwi','test'));
		$this->assertTrue('piwi', UserSessionManager::getUserName());
		
		$provider = BeanFactory::getBeanById('configurationManager')->getRoleProvider();
		try {
			$provider->setAuthFile('/dev/null/auth.xml');
			$this->fail('Provider should throw an exception');
		} catch (PiwiException $e) {
			$this->assertEqual(1005, $e->getCode());
		}
		
		$xml = new XmlRoleProvider();
		try {
			$xml->isPasswordValid('test','test');
			$this->fail('Provider should throw an exception');
		} catch (PiwiException $e) {
			$this->assertEqual(1003, $e->getCode());
		}
		
		try {
			$xml->isUserInRole('test',array());
			$this->fail('Provider should throw an exception');
		} catch (PiwiException $e) {
			$this->assertEqual(1003, $e->getCode());
		}
	}
}
?>