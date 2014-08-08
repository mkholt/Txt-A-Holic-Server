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

namespace mholt\txtAHolic\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Description of User
 *
 * @author morten
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="mholt\txtAHolic\UserBundle\Entity\UserRepository")
 */
class User implements UserInterface, \Serializable {
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var string
     * @ORM\Column(type="string", length=255, unique=true)
     */
    protected $username;
    
    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    protected $password;
	
	/**
	 * @var string
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	protected $sessionPassword;
    
    /**
     * 
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * 
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * 
     * @param string $username
     * @return \mholt\txtAHolic\ServerBundle\Entity\User
     */
    public function setUsername($username) {
        $this->username = $username;
        
        return $this;
    }

    /**
     * 
     * @param string $password
     * @return \mholt\txtAHolic\ServerBundle\Entity\User
     */
    public function setPassword($password) {
        $this->password = $password;
        
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * 
     * @return string[]
     */
    public function getRoles() {
        return array('ROLE_USER');
    }
    
    /**
     * 
     * @return null
     */
    public function getSalt() {
        return null;
    }
    
    public function eraseCredentials() { }
    
    public function serialize() {
        return serialize(array(
            $this->getId(),
            $this->getUsername(),
            $this->getPassword()
        ));
    }
    
    public function unserialize($serialized) {
        list(
                $this->id,
                $this->username,
                $this->password) = unserialize($serialized);
    }
	
	public function fromArray($arr)
	{
		$this->id = (!empty($arr['id'])) ? $arr['id'] : null;
		$this->username = $arr['username'];
		$this->password = $arr['password'];
		
		return $this;
	}

    /**
     * Set sessionPassword
     *
     * @param string $sessionPassword
     * @return User
     */
    public function setSessionPassword($sessionPassword)
    {
        $this->sessionPassword = $sessionPassword;

        return $this;
    }

    /**
     * Get sessionPassword
     *
     * @return string 
     */
    public function getSessionPassword()
    {
        return $this->sessionPassword;
    }
}
