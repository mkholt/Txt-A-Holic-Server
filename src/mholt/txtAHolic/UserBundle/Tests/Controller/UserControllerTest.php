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

/**
 * Description of UserControllerTest
 *
 * @author morten
 */
class UserControllerTest extends WebTestCase {
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
		$client->request('POST', '/users/register',
				array(
					'username' => 'test',
					'password' => 'test'
				));
		$this->assertEquals(
				Response::HTTP_OK,
				$client->getResponse()->getStatusCode());
	}
	
	public function testRegisterUserAlreadyExists()
	{
		$client = static::createClient();
		$client->request('POST', '/users/register',
				array(
					'username' => 'test',
					'password' => 'test'
				));
		
		$client->request('POST', '/users/register',
				array(
					'username' => 'test',
					'password' => 'test'
				));
		
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
		$client = static::createClient();
		$client->request('POST', '/users/register',
				array(
					'username' => 'test',
					'password' => 'test'
				));
		
		$client->request('POST', '/users/auth');
		
		$this->assertEquals(
				Response::HTTP_OK,
				$client->getResponse()->getStatusCode());
		
		$this->assertEquals(
				"User authenticated: test",
				$client->getResponse()->getContent());
	}
}
