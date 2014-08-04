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

namespace mholt\txtAHolic\UserBundle\Security\Provider;

use Doctrine\ORM\EntityManagerInterface;
use mholt\txtAHolic\UserBundle\Security\Authentication\WsseUserToken;
use mholt\txtAHolic\UserBundle\Entity\Nonce;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\NonceExpiredException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Description of WsseProvider
 *
 * @author Morten Holt <thawk@t-hawk.com>
 */
class WsseProvider implements AuthenticationProviderInterface
{
    private $userProvider;
    private $em;

    public function __construct(UserProviderInterface $userProvider, EntityManagerInterface $em)
    {
        $this->userProvider = $userProvider;
		$this->em = $em;
    }

    public function authenticate(TokenInterface $token)
    {
        $user = $this->userProvider->loadUserByUsername($token->getUsername());

        if ($user && $this->validateDigest($token->digest, $token->nonce, $token->created, $user->getPassword())) {
            $authenticatedToken = new WsseUserToken($user->getRoles());
            $authenticatedToken->setUser($user);

            return $authenticatedToken;
        }

        throw new AuthenticationException('The WSSE authentication failed.');
    }

    /**
     * This function is specific to Wsse authentication and is only used to help this example
     *
     * For more information specific to the logic here, see
     * https://github.com/symfony/symfony-docs/pull/3134#issuecomment-27699129
     */
    protected function validateDigest($digest, $nonce, $created, $secret)
    {
        // Check created time is not in the future
        if (strtotime($created) > time()) {
            return false;
        }

        // Expire timestamp after 5 minutes
        if (time() - strtotime($created) > 300) {
            return false;
        }

        // Validate that the nonce is *not* used in the last 5 minutes
        // if it has, this could be a replay attack
		$repo = $this->em->getRepository('UserBundle:Nonce');
		/* @var $nonce Nonce */
		$nonceObj = $repo->findOneBy(array('nonce' => $nonce));
		if (!is_null($nonceObj) && !$nonceObj->isExpired()) {
			throw new NonceExpiredException('Previously used nonce detected');
		}
		
		// The nonce was either expired, or completely new, persist the new nonce
		if (is_null($nonceObj))
		{
			// The nonce was new, create new object to store it
			$nonceObj = new Nonce();
			$nonceObj->setNonce($nonce);
			$this->em->persist($nonceObj);
		}
		$nonceObj->setTime(new \DateTime());
		$this->em->flush();

        // Validate Secret
        $expected = base64_encode(sha1(base64_decode($nonce).$created.$secret, true));

        return $digest === $expected;
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof WsseUserToken;
    }
}
