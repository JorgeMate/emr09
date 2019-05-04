<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;


class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {

        return $this->render('default/homepage.html.twig', [ 
            
        ]);
    }

    /**
     * @Route("/target", name="target")
     */
    public function target()
    {
        return $this->render('default/target.html.twig', [
            
        ]);
    }


}
