<?php

/*
 * Copyright (C) 2014 Morten Holt <thawk@t-hawk.com>
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

namespace mholt\txtAHolic\UserBundle\Tests\Form\Type;

use mholt\txtAHolic\UserBundle\Entity\User;
use mholt\txtAHolic\UserBundle\Form\Type\UserType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Description of UserTypeTest
 *
 * @author Morten Holt <thawk@t-hawk.com>
 */
class UserTypeTest extends TypeTestCase {
	public function testSubmitValidData()
	{
		$formData = array(
			'username' => 'test',
			'password' => 'test2'
		);
		
		$type = new UserType();
		$form = $this->factory->create($type);
		
		$object = new User();
		$object->fromArray($formData);
		
		$form->submit($formData);
		
		$this->assertTrue($form->isSynchronized());
		$this->assertEquals($object, $form->getData());
		
		$view = $form->createView();
		$children = $view->children;
		
		foreach (array_keys($formData) as $key)
		{
			$this->assertArrayHasKey($key, $children);
		}
	}
}
