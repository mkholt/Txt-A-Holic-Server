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

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of UserControllerTest
 *
 * @author morten
 */
class UserControllerTest extends WebTestCase {
    public function testDisallowRegisterGet()
    {
        $client = static::createClient();
        $client->request('GET', '/users/register');
        $this->assertEquals(
                Response::HTTP_METHOD_NOT_ALLOWED,
                $client->getResponse()->getStatusCode());
    }
}
