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

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Post;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

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
    public function registerUserAction()
    {
        
        return $this->handleView($this->view(array("hello" => "world"), Response::HTTP_OK));
    }
    
    public function loginAction(Request $request)
    {
        $session = $request->getSession();
        
        $error = '';
        if ($request->attributes->has(SecurityContextInterface::AUTHENTICATION_ERROR))
        {
            $error = $request->attributes->get(SecurityContextInterface::AUTHENTICATION_ERROR);
        }
        elseif ($session !== null && $session->has(SecurityContextInterface::AUTHENTICATION_ERROR))
        {
            $error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR);
        }
        
        if (!empty($error))
        {
            return $this->handleView($this->view(array('error' => $error)), Response::HTTP_FORBIDDEN);
        }
        
        return $this->handleView($this->view(array('error' => 'Illegal login request')), Response::HTTP_BAD_REQUEST);
    }
    
    public function postUserAction()
    {
        return $this->handleView($this->view(array("hello" => "world"), Response::HTTP_OK));
    }
}
