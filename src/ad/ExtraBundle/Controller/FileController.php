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

class CategoryController extends Controller
{
	/**
	 * @Route("/file/list/", name="ad_list_file")
	 * @Template()
	 */
	public function listAction()
	{
		$em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('adExtraBundle:File');
		

		return $this->container->get('templating')->renderResponse('adExtraBundle:Category:list.html.twig', array(
				
		));
	}
	
}