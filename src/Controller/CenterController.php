<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\Entity\Center;
use App\Entity\User;


use App\Form\CenterType;
use App\Form\UserType;

use App\Form\MedicType;


/**
 * Controller used to manage current center.
 *
 * @Route("/center")
 * @Security("is_granted('ROLE_ADMIN')")
 *
 */
class CenterController extends AbstractController
{

    /**
    * @Route("/{slug}/cpanel", methods={"GET"}, name="center_cpanel")
    */
    public function centerCpanel($slug): Response
    {

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Center::class);
        $center = $repository->findOneBy(['slug' => $slug]);

        $this->denyAccessUnlessGranted('CENTER_EDIT', $center);

        $user = $this->getUser();
        //$center = $this->getUser()->getCenter();

        return $this->render('_admin_center/cpanel.html.twig', [
             
            'user' => $user,
            'center' => $center,
        ]);
    }



    /**
     * @Route("/{slug}/edit", methods={"GET", "POST"}, name="center_edit")
     * 
     * EDITAR el centro id
     */
    public function editCenter(Request $request, $slug): Response
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Center::class);
        $center = $repository->findOneBy(['slug' => $slug]);

        $this->denyAccessUnlessGranted('CENTER_EDIT', $center);

        $form = $this->createForm(CenterType::class, $center);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();
            $this->addFlash('success', 'record.updated_successfully');

            return $this->redirectToRoute('users_index', ['slug' => $center->getSlug()] );
        }

        return $this->render('_admin_center/edit.html.twig', [
            'center' => $center,
            'form' => $form->createView(),
        ]);

    }
    

    /**
     * @Route("/{slug}/users", methods={"GET"}, name="users_index")
     * 
     * LISTAR todos los usuarios del centro slug
     */
    public function indexUsers($slug): Response
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Center::class);
        $center = $repository->findOneBy(['slug' => $slug]);

        $this->denyAccessUnlessGranted('CENTER_VIEW', $center);

        $users = $center->getUsers();
        
        return $this->render('_admin_center/user/index.html.twig', [
             
            
            'users' => $users,
            'center' => $center 
        ]);
    }

    /**
     * @Route("/{slug}/new/user", methods={"GET", "POST"}, name="user_new")
     * 
     * NUEVO usuario del centro id
     */
    public function newUser(Request $request, UserPasswordEncoderInterface $encoder, $slug): Response
    {  

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Center::class);
        $center = $repository->findOneBy(['slug' => $slug]);
    
        $this->denyAccessUnlessGranted('CENTER_EDIT', $center);
        
        $user = new User();

        $user->setCenter($center);
        $user->setEnabled(true);

        $roles[] = 'ROLE_USER';
        $user->setRoles($roles);

        $form = $this->createForm(NewUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword($encoder->encodePassword($user, $form->get('password')->getData()));

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'record.updated_successfully');

            return $this->redirectToRoute('users_index', ['id' => $center->getid()]);
        }

        return $this->render('_admin_center/user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'sidebarContent' => '',
        ]);
    }

    /**
     * @Route("/{slug}/user/{id}/edit", methods={"GET", "POST"}, name="user_edit")
     * 
     * EDITAR otro usuario del mismo centro, cualquiera si es SUPER_ADMIN 
     */
    public function editAnyUser(Request $request, $slug, User $user): Response
    {

        $this->denyAccessUnlessGranted('USER_EDIT', $user);

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('success', 'record.updated_successfully');

            return $this->redirectToRoute('users_index', ['slug' => $slug]);
        }

        //$center = $user->getCenter();

        return $this->render('user/edit.html.twig', [
            'slug' => $slug,
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }    



}
