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

namespace mholt\txtAHolic\UserBundle\Tests\Entity;

use mholt\txtAHolic\UserBundle\Entity\Nonce;

/**
 * Description of NonceTest
 *
 * @author Morten Holt <thawk@t-hawk.com>
 */
class NonceTest extends \PHPUnit_Framework_TestCase
{
	public function testNotExpiredNow()
	{
		$nonce = new Nonce();
		$nonce->setTime(new \DateTime());
		
		$this->assertFalse($nonce->isExpired());
	}
	
	public function testNotExpired5Minutes()
	{
		$nonce = new Nonce();
		$nonce->setTime(new \DateTime('-5 minutes'));
		
		$this->assertFalse($nonce->isExpired());
	}
	
	public function testExpired6Minutes()
	{
		$nonce = new Nonce();
		$nonce->setTime(new \DateTime('-6 minutes'));
		
		$this->assertTrue($nonce->isExpired());
	}
	
	public function testNotExpiredPlus5Minutes()
	{
		$nonce = new Nonce();
		$nonce->setTime(new \DateTime('+5 minutes'));
		
		$this->assertFalse($nonce->isExpired());
	}
	
	public function testExpiredPlus6Minutes()
	{
		$nonce = new Nonce();
		$nonce->setTime(new \DateTime('+6 minutes'));
		
		$this->assertTrue($nonce->isExpired());
	}
}
