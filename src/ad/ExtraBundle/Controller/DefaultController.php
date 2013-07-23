<?php

namespace ad\ExtraBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ad\ExtraBundle\Entity\Category;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="ad_index")
     * @Template()
     */
    public function indexAction()
    {
    	
    	if ($this->getUser())
    	{
    		return $this->render('adExtraBundle:Default:index.html.twig',array(
    				'user' => $this->getUser()
    				));
    	}
    	else 
    	{
    		return $this->redirect($this->generateUrl('fos_user_security_login'));
    	}
        
        
    }
}
