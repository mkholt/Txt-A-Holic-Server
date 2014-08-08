<?php

/*
 * Copyright (C) 2014 Txt-A-Holic
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace mholt\txtAHolic\UserBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Util\SecureRandom;

/**
 * Description of UserControllerTest
 *
 * @author morten
 */
class UserControllerTest extends WebTestCase {
	protected function registerUser($client, $username, $password)
	{
		$client->request('POST', '/users/register',
			array(
				'username' => $username,
				'password' => $password
			));
	}
	
	protected function getNonce()
	{
		$generator = new SecureRandom();
		return $generator->nextBytes(10);
	}
	
	protected function getWSSEHeader($username, $password, $time = 'now', $nonce = null)
	{
		$nonce = (!empty($nonce)) ? $nonce : $this->getNonce();
		
		$created = date('c', strtotime($time));
		$passwordDigest = base64_encode(sha1($nonce . $created . $password, true));
		
		return array(
			'HTTP_Authorization' => 'WSSE profile="UsernameToken"',
			'HTTP_X-WSSE' => 'UsernameToken Username="'.$username.'", PasswordDigest="'.$passwordDigest.'", Nonce="'.base64_encode($nonce).'", Created="'.$created.'"'
		);
	}
	
	public function setUp()
	{
		$this->loadFixtures(array());
	}
	
    public function testDisallowRegisterGet()
    {
        $client = static::createClient();
        $client->request('GET', '/users/register');
        $this->assertEquals(
                Response::HTTP_METHOD_NOT_ALLOWED,
                $client->getResponse()->getStatusCode());
    }
	
	public function testAllowRegisterPost()
	{
		$client = static::createClient();
		$client->request('POST', '/users/register');
		$this->assertNotEquals(
				Response::HTTP_METHOD_NOT_ALLOWED,
				$client->getResponse()->getStatusCode());
	}
	
	public function testRegisterMissingData()
	{
		$client = static::createClient();
		$client->request('POST', '/users/register');
		$response = json_decode($client->getResponse()->getContent(), true);
		
		$this->assertEquals(
				Response::HTTP_BAD_REQUEST,
				$client->getResponse()->getStatusCode());
		
		$this->assertEquals(
				"Missing username",
				$response['errors']['children']['username']['errors'][0]);
		
		$this->assertEquals(
				"Missing password",
				$response['errors']['children']['password']['errors'][0]);
	}
	
	public function testRegisterNewUser()
	{
		$client = static::createClient();
		$this->registerUser($client, 'test', 'test');
		
		$this->assertEquals(
				Response::HTTP_OK,
				$client->getResponse()->getStatusCode());
		
		$this->assertEquals(
				"User registered",
				json_decode($client->getResponse()->getContent()));
	}
	
	public function testRegisterUserAlreadyExists()
	{
		$client = static::createClient();
		$this->registerUser($client, 'test', 'test');
		$this->registerUser($client, 'test', 'test');
		
		$this->assertEquals(
				Response::HTTP_BAD_REQUEST,
				$client->getResponse()->getStatusCode());
		
		$response = json_decode($client->getResponse()->getContent(), true);
		$this->assertEquals(
				"User already exists",
				$response['errors']['children']['username']['errors'][0]);
	}
		
	public function testAuthenticateNewUser()
	{
		$username = 'test';
		$password = 'test';
		
		$client = static::createClient();
		$this->registerUser($client, 'test', 'test');
		
		$client->request('POST', '/users/auth', array(), array(), $this->getWSSEHeader($username, $password));
		
		$this->assertEquals(
				Response::HTTP_OK,
				$client->getResponse()->getStatusCode());
		
		$this->assertEquals(
				"User authenticated: test",
				json_decode($client->getResponse()->getContent()));
	}
	
	public function testAuthenticateNewUserWrongPassword()
	{
		$username = 'test';
		$password = 'test';
		
		$client = static::createClient();
		$this->registerUser($client, $username, $password);
		
		$client->request('POST', '/users/auth', array(), array(), $this->getWSSEHeader($username, $password . '_fail'));
		
		$this->assertEquals(
				Response::HTTP_FORBIDDEN,
				$client->getResponse()->getStatusCode());
	}
	
	public function testAuthenticateNewUserTooOld()
	{
		$username = 'test';
		$password = 'test';
		
		$client = static::createClient();
		$this->registerUser($client, $username, $password);
		
		$client->request('POST', '/users/auth', array(), array(), $this->getWSSEHeader($username, $password, '-1 hour'));
		
		$this->assertEquals(
				Response::HTTP_FORBIDDEN,
				$client->getResponse()->getStatusCode());
	}
	
	public function testAuthenticateNewUserTooNew()
	{
		$username = 'test';
		$password = 'test';
		
		$client = static::createClient();
		$this->registerUser($client, $username, $password);
		
		$client->request('POST', '/users/auth', array(), array(), $this->getWSSEHeader($username, $password, '+1 hour'));
		
		$this->assertEquals(
				Response::HTTP_FORBIDDEN,
				$client->getResponse()->getStatusCode());
	}
	
	public function testAuthenticateNewUserReUsedNonce()
	{
		$username = 'test';
		$password = 'test';
		
		$client = static::createClient();
		$this->registerUser($client, $username, $password);
		
		$nonce = $this->getNonce();
		
		$client->request('POST', '/users/auth', array(), array(), $this->getWSSEHeader($username, $password, 'now', $nonce));
		
		$this->assertEquals(
				Response::HTTP_OK,
				$client->getResponse()->getStatusCode());
		
		$client->request('POST', '/users/auth', array(), array(), $this->getWSSEHeader($username, $password, 'now', $nonce));
		
		$this->assertEquals(
				Response::HTTP_FORBIDDEN,
				$client->getResponse()->getStatusCode());
	}
}
