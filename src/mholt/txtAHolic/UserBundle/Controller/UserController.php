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

namespace mholt\txtAHolic\UserBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\FOSRestController;
use mholt\txtAHolic\UserBundle\Entity\User;
use mholt\txtAHolic\UserBundle\Form\Type\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of UsersController
 *
 * @author morten
 */
class UserController extends FOSRestController {
    /**
     * @Post("/users/register")
     * @return Response
     */
    public function registerUserAction(Request $request)
    {
		$user = new User();
		$form = $this->createForm(new UserType(), $user, array(
			'action' => $this->generateUrl('register_user')
		));
		
		$form->submit($request->request->all());
		if ($form->isValid())
		{
			$em = $this->getDoctrine()->getManager();
			$em->persist($form->getData());
			$em->flush();
			
			return $this->handleView($this->view("User registered", Response::HTTP_OK));
		}

		return $this->handleView($this->view($form), Response::HTTP_BAD_REQUEST);
    }
	
	/**
	 * @Post("/users/auth")
	 * @return Response
	 */
	public function authUserAction()
	{
		/* @var $user User */
		$user = $this->getUser();
		
		return $this->handleView($this->view("User authenticated: " . $user->getUsername()), Response::HTTP_OK);
	}
}
