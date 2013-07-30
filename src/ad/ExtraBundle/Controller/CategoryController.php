<?php
namespace ad\ExtraBundle\Controller;

use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ad\ExtraBundle\Form\CategoryType;
use ad\ExtraBundle\Entity\Category;
use JMS\SecurityExtraBundle\Annotation\Secure;

class CategoryController extends Controller
{
	/**
	 * @Route("/category/manage/", name="ad_manage_category")
	 * @Secure(roles="ROLE_ADMIN")
	 * @Template()
	 */
	public function manageAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('adExtraBundle:Category');
		
		$arrayTree = array('root' => $repo->childrenHierarchy());
		
		$ajax = $request->request->get('ajax') == 'on';
		
		if ( $ajax != null)
		{
			if ($request->request->get('ajax') == 'on')
			{
				return $this->container->get('templating')->renderResponse('adExtraBundle:Category:manageajax.html.twig', array(
						'category' => $arrayTree,
				));
			}
		}
		else {
			return $this->container->get('templating')->renderResponse('adExtraBundle:Category:manage.html.twig', array(
					'category' => $arrayTree,
			));
		}		
	}
	
	/**
	 * @Route("/category/add/{slug}", name="ad_new_category_slug")
	 * @Route("/category/add/", name="ad_new_category")
	 * @Secure(roles="ROLE_ADMIN")
	 * @Template()
	 */
	public function addAction($slug = 'none', Request $request)
	{
		$category = new Category();
		
		$form = $this->createForm(new CategoryType(), $category); //, $adsParameter
		
		$form->handleRequest($request);
		
		if ($form->isValid())
		{
			
			if ($slug == 'none')
			{
				$cat = $form->getData();
				$em = $this->getDoctrine()->getManager();
					
				$cat->setSlug('');
				$cat->setParent(null);
					
				$em->persist($cat);
				$em->flush();
					
				return $this->redirect($this->generateUrl('ad_manage_category'));
			}
			else
			{
				$em = $this->getDoctrine()->getManager();
				$repo = $em->getRepository('adExtraBundle:Category');
				$parent = $repo->findCatBySlug($slug);
					
				$cat = $form->getData();
				$em = $this->getDoctrine()->getManager();
					
				$cat->setSlug('');
				$cat->setParent($parent);
					
				$em->persist($cat);
				$em->flush();
					
				return $this->redirect($this->generateUrl('ad_manage_category'));
			}
		}
		
		return $this->render('adExtraBundle:Category:new.html.twig', array ('form' => $form->createView(),
																			'slug' => $slug));
	}
	

	
	/**
	 * @Route("/category/edit/{slug}", name="ad_edit_category_slug")
	 * @Route("/category/edit/", name="ad_edit_category")
	 * @Secure(roles="ROLE_ADMIN")
	 * @Template()
	 */
	public function editAction($slug = 'none')
	{
		if ($slug == 'none')
		{
			throw new \Exception('La racine ne peut pas être choisie');
		}
		
		$em = $this->getDoctrine()->getManager();
		$category = $em->getRepository('adExtraBundle:Category')->findCatBySlug($slug);
		
		if (!$category)
		{
			throw $this->createNotFoundException('La catégorie n\'existe pas.');
		}
		$id = $category->getId();
		$form = $this->createForm(new CategoryType, $category);
		
		return $this->render('adExtraBundle:Category:edit.html.twig', array('form' => $form->createView(), 'id' => $id));
		
	}
	
	/**
	 * @Route("/category/update/{id}", name="ad_update_category")
	 * @Template()
	 */
	public function updateAction($id)
	{
		$request = $this->getRequest();
		$em = $this->getDoctrine()->getManager();
		$category = $em->getRepository('adExtraBundle:Category')->find($id);
		$form = $this->createForm(new CategoryType, $category);
	
		if ('POST' == $request->getMethod())
		{
			$form->bind($request);
			if ($form->isValid())
			{
				$data = $form->getData();
				$em = $this->getDoctrine()->getManager();
				$em->persist($data);
				$em->flush();
				return $this->redirect($this->generateUrl('ad_manage_category'));
			}
		}
		return $this->render('adExtraBundle:Category:edit.html.twig', array(
				'form' => $form->createView(),
		));
	}
	
	/**
	 * @Route("/category/delete/{slug}", name="ad_delete_category_slug")
	 * @Route("/category/delete/", name="ad_delete_category")
	 * @Template()
	 */
	public function deleteAction($slug = 'none')
	{
		if ($slug == 'none')
		{
			throw new \Exception('La racine ne peut pas être choisie');
		}
		
		$em = $this->getDoctrine()->getManager();	//la même que celle du cours
		$entity = $em->getRepository('adExtraBundle:Category')->findCatBySlug($slug);
	
		if (!$entity)
		{
			throw $this->createNotFoundException('Cette catégorie n\'existe pas.');
		}
	
		$em->remove($entity);
		$em->flush();
	
		return $this->redirect($this->generateUrl('ad_manage_category'));
	}
}