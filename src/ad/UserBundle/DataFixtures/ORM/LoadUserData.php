<?php
namespace ad\UserBundle\DataFixtures\ORM;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use ad\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;

class LoadUserData implements FixtureInterface, ContainerAwareInterface
{
	private $container;

	public function setContainer(ContainerInterface $container = null)
	{
		$this->container = $container;
	}

	public function load(ObjectManager $manager)
	{
		$userManager = $this->container->get('fos_user.user_manager');
		
		$userAdmin = $userManager->createUser();
		$userPartenaire = $userManager->createUser();
		
		$encoder = $this->container
						->get('security.encoder_factory')
						->getEncoder($userAdmin);
		
		//Enregistrement du SuperAdmin
		$userAdmin->setUsername('admin');
		$userAdmin->setPassword($encoder
				  ->encodePassword('admin', $userAdmin->getSalt()));
		$userAdmin->setEmail('admin@superadmin.com');
		$userAdmin->setEnabled(true);
		$userAdmin->setRoles(array('ROLE_ADMIN'));
		$userManager->updateUser($userAdmin, true);
		
		//Enregistrement de user
		$userPartenaire->setUsername('partenaire');
		$userPartenaire->setPassword($encoder
            	  ->encodePassword('partenaire', $userPartenaire->getSalt()));
		$userPartenaire->setEmail('partenaire@partenaire.com');
		$userPartenaire->setEnabled(true);
		$userPartenaire->setRoles(array('ROLE_PARTENAIRE'));
		$userManager->updateUser($userPartenaire, true);
	}
}