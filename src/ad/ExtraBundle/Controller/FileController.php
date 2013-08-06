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
	 * @Route("/file/add/", name="ad_add_file")
	 * @Route("/file/add/{catslug}", name="ad_add_file_sluged")
	 * @Template()
	 */
	public function newAction(Request $request, $catslug = null)
	{
		$em = $this->getDoctrine()->getManager();
		
		$file = new File();
		
		if ($catslug != null)
		{
			$category = $em->getRepository('adExtraBundle:Category')->findCatBySlug($catslug);
			
			$file->setCategoryId($category);
		}
		
		
		$form = $this->createForm(new FileType(), $file);
		
		$formView = $form->createView();
		
		$form->handleRequest($request);

		
		$validator = $this->get('validator');
		$errorList = $validator->validate($file);
		
		if (count($errorList) > 0) {
			return $this->render('adExtraBundle:File:new.html.twig', array ('form' => $formView,
																			'errors' => $errorList));
		}
		
	    if ($form->isValid())
		{
			$em = $this->getDoctrine()->getManager();
			
			$file->uploadFile();
			$file->setDate(new \DateTime('now'));
			$file->setUserId($this->getUser());
			
			$em->persist($file);
			$em->flush();
			
			return $this->redirect($this->generateUrl('ad_index', array('new')));
		}
		
		return $this->render('adExtraBundle:File:new.html.twig', array ('form' => $form->createView()));
	}
	
	/**
	 * @Route("/file/delete/{id}", name="ad_delete_file")
	 */
	public function deleteFileAction($id)
	{
		$em = $this->getDoctrine()->getManager();
	
		$file = $em->getRepository("adExtraBundle:File")->findOneBy(array('id' => $id));
	
		if ($this->get('security.context')->isGranted('ROLE_ADMIN'))
		{
			if (!$file)
			{
				throw $this->createNotFoundException('Ce fichier n\'existe pas en BDD.');
			}
			
			if (!file_exists('uploads/files/'.$file->getName()))
			{
				$em->remove($file);
				$em->flush();
				
				throw $this->createNotFoundException('Ce fichier n\'existe pas sur le disque.');
			}
			
			unlink('uploads/files/'.$file->getName());
			
			$em->remove($file);
			$em->flush();
			
			return $this->redirect($this->generateUrl('ad_index', array('delete')));
			
		}
		else
		{
			throw new AccessDeniedHttpException('Vous n\'avez pas les droits nécessaire à la suppression du fichier.');
		}
	}
	
	/**
	 * @Route("/file/donwload/{filename}", name="ad_download_file")
	 */
	public function downloadAction($filename)
	{
		$request = $this->get('request');
		$path = $this->get('kernel')->getRootDir(). "/../web/uploads/files/";
		$content = file_get_contents($path.$filename);
	
		$response = new Response();
		
		$response->headers->set('Content-Type', 'mime/type');
		$response->headers->set('Content-Disposition', 'attachment;filename="'.$filename);
	
		$response->setContent($content);
	
		return $response;
	}
	
	/**
	 * @Route("/file/category/{id}", name="ad_category_file")
	 */
	public function fileCategoryAction($id, Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('adExtraBundle:Category');
		
		$results = $repo->hasFile($id);
		
		return $this->container->get('templating')->renderResponse('adExtraBundle:File:listajax.html.twig', array(
				'files' => $results,
				'user' => $this->getUser()
		));
	}
}