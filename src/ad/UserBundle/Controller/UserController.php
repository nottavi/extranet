<?php
namespace ad\UserBundle\Controller;

use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ad\UserBundle\Form\UserType;
use ad\UserdBundle\Entity\User;
use JMS\SecurityExtraBundle\Annotation\Secure;
use FOS\UserBundle\Controller\ProfileController as FosUser;

class UserController extends Controller
{
	/**
	 * @Route("/user/delete/{id}", name="ad_delete_user_id")
	 */
	public function deleteUserAction($id)
	{
		$em = $this->getDoctrine()->getManager();
	
		$user = $em->getRepository('adUserBundle:User')->findById($id);
	
		if (!$user)
		{
			throw $this->createNotFoundException('Cette utilisateur n\'existe pas.');
		}
	
		if ($this->get('security.context')->isGranted('ROLE_ADMIN') || $user[0] == $this->getUser())
		{
			$em->remove($user[0]);
			$em->flush();
			
			return $this->redirect($this->generateUrl('ad_index', array('delete')));
		}
		else
		{
			throw new AccessDeniedHttpException('Vous n\'avez pas les droits nécessaire à la suppression d\'utilisateurs.');
		}
	}
	
	/**
	 * @Route("/user/edit/{id}", name="ad_edit_user_id")
	 */
	public function editUserAction($id)
	{
		$em = $this->getDoctrine()->getManager();
		
		$user = $em->getRepository('adUserBundle:User')->findById($id);
		
		if (!$user[0])
		{
			throw $this->createNotFoundException('L\'utilisateur n\'existe pas.');
		}
		
		$form = $this->createForm(new UserType, $user[0]);
		
		return $this->render('adUserBundle:User:edit.html.twig', array('form' => $form->createView(), 'id' => $id));
		
	}
	
	/**
	 * @Route("/user/update/{id}", name="ad_update_user")
	 * @Template()
	 */
	public function updateAction($id)
	{
		$request = $this->getRequest();
		$em = $this->getDoctrine()->getManager();
		$userManager = $this->container->get('fos_user.user_manager');
		$user = $em->getRepository('adUserBundle:User')->find($id);
		$form = $this->createForm(new UserType, $user);
	
		if ('POST' == $request->getMethod())
		{
			$form->bind($request);
			if ($form->isValid())
			{
				$userManager->updateUser($user);
	
				return $this->redirect($this->generateUrl('ad_index', array('update')));
			}
		}
		return $this->render('adUserBundle:User:edit.html.twig', array(
				'form' => $form->createView(),
		));
	}
	
	/**
	 * @Route("/user/add/", name="ad_add_user")
	 */
	public function addUserAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$userManager = $this->container->get('fos_user.user_manager');
	
		$user = $userManager->createUser();
		
		$form = $this->createForm(new UserType(), $user);
		
		$form->handleRequest($request);
		
		if ($form->isValid())
		{
			$user->setEnabled(true);
			$user->setRoles(array($form->get('roles')->getData()));

			$userManager->updateUser($user, true);
			
			return $this->redirect($this->generateUrl('ad_index', array('new')));
		}
			
			return $this->render('adUserBundle:User:new.html.twig', array ('form' => $form->createView()));
	}
	
	/**
	 * @Route("/user/list/{action}", name="ad_list_user_action")
	 * @Route("/user/list/", name="ad_list_user")
	 */
	public function listUserAction(Request $request, $action = null)
	{
		$em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('adUserBundle:User');
		
		$users = $repo->findAll();
		
		return $this->container->get('templating')->renderResponse('adUserBundle:User:list.html.twig', array(
				'users' => $users,
				'action' => $action
		));
	}
}