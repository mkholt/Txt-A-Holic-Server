<?php

namespace mholt\txtAHolic\UserBundle;

use mholt\txtAHolic\UserBundle\DependencyInjection\Security\Factory\WsseFactory;
use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class UserBundle extends Bundle
{
	public function build(ContainerBuilder $container)
	{
		parent::build($container);
		
		/* @var $extension SecurityExtension */
		$extension = $container->getExtension('security');
		$extension->addSecurityListenerFactory(new WsseFactory());
	}
}
