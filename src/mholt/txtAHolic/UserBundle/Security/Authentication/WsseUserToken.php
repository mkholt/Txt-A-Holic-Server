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

namespace mholt\txtAHolic\UserBundle\Security\Authentication;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * Description of WsseUserToken
 *
 * @author morten
 */
class WsseUserToken extends AbstractToken {
	public $created;
	public $digest;
	public $nonce;
	
	public function __construct(array $roles = array())
	{
		parent::__construct($roles);
		
		// If the user has roles, consider it authenticated
        $this->setAuthenticated(count($roles) > 0);
	}
	
	public function getCredentials()
    {
        return '';
    }
}
