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
use ad\ExtraBundle\Form\FileType;
use ad\ExtraBundle\Entity\File;
use JMS\SecurityExtraBundle\Annotation\Secure;

class FileController extends Controller
{
	/**
	 * @Route("/file/list/", name="ad_list_file")
	 * @Template()
	 */
	public function listAction()
	{
		$em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('adExtraBundle:File');
		
		$file = $repo->findAll();

		return $this->container->get('templating')->renderResponse('adExtraBundle:Category:list.html.twig', array(
				
		));
	}
	
	/**
	 * @Route("/file/add/", name="ad_add_file")
	 * @Template()
	 */
	public function newAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		
		$file = new File();
		
		$form = $this->createForm(new FileType(), $file); //, $adsParameter
		
		$formView = $form->createView();
		
		$form->handleRequest($request);
		
		if ($form->isValid()) 
		{
			$em = $this->getDoctrine()->getManager();
			
			$file->uploadFile();
			$file->setDate(new \DateTime('now'));
			$file->setUserId($this->getUser());
			
			
			return $this->redirect($this->generateUrl('ad_index'));
		}
		
		return $this->render('adExtraBundle:File:new.html.twig', array ('form' => $formView));
	}
}