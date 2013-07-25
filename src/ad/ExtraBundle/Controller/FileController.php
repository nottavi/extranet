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
			
			$em->persist($file);
			$em->flush();
			
			return $this->redirect($this->generateUrl('ad_index'));
		}
		return $this->render('adExtraBundle:File:new.html.twig', array ('form' => $formView));
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
				throw $this->createNotFoundException('Cette annonce n\'existe pas.');
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
}