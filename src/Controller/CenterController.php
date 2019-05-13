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

use App\Entity\CenterDocGroup;



use App\Form\CenterType;
use App\Form\UserType;
use App\Form\NewUserType;

use App\Form\MedicType;

use App\Form\CenterDocGroupType;




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

        $center = $this->getUser()->getCenter();
        $this->denyAccessUnlessGranted('CENTER_EDIT', $center);
        $user = $this->getUser();

        
        $groups = $center->getCenterDocGroups();

        return $this->render('_admin_center/cpanel.html.twig', [
             
            'user' => $user,
            'center' => $center,
            'groups' => $groups,

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
            $this->addFlash('info', 'record.updated_successfully');

            return $this->redirectToRoute('users_index', ['slug' => $center->getSlug()] );
        }

        return $this->render('_admin_center/edit.html.twig', [
            'slug' => $slug,
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
             
            'slug' => $slug,
            'users' => $users,
            'center' => $center 
        ]);
    }

    /**
     * @Route("/{slug}/medic-users", methods={"GET"}, name="medics_index")
     * 
     * LISTAR todos los usuarios medicos del centro id
     */
    public function indexMedUsers(Request $request, $slug): Response
    {        
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Center::class);
        $center = $repository->findOneBy(['slug' => $slug]);

        $this->denyAccessUnlessGranted('CENTER_VIEW', $center);

        $repository = $em->getRepository(User::class);
        $medics = $repository->findBy(['center' => $center->getId(), 'medic' => '1']);

        return $this->render('_admin_center/user/index-medic.html.twig', [
            'center' => $center,
            'medics' => $medics,
             
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

            $this->addFlash('info', 'record.updated_successfully');

            return $this->redirectToRoute('users_index', ['slug' => $slug]);
        }

        //$center = $user->getCenter();

        return $this->render('user/edit.html.twig', [
            'slug' => $slug,
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/{slug}/medic-user/{id}/edit", methods={"GET", "POST"}, name="edit_user_medic")
     * 
     * Editar los datos médicos de un usuario
     */
    public function editMedUser(Request $request, $slug, User $user): Response
    {

        $this->denyAccessUnlessGranted('USER_EDIT', $user);
               
        $form = $this->createForm(MedicType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('info', 'record.updated_successfully');

            return $this->redirectToRoute('medics_index', ['slug' => $slug]);

        }

        return $this->render('_admin_center/user/edit-medic.html.twig', [
             
            'user' => $user,
            'form' => $form->createView(),
        ]);
    
    }


    /**
     * @Route("/{slug}/program-options", methods={"GET", "POST"}, name="program_options")
     * 
     * 
     */
    public function programOptions(Request $request, $slug): Response
    {

        return $this->render('_admin_center/options.html.twig', [
             
        ]);


    }



    /**
     * @Route("/{slug}/new/documents-group", methods={"GET", "POST"}, name="group_new")
     * 
     * 
     */
    public function newDocGroup(Request $request, $slug): Response
    {
        $center = $this->getUser()->getCenter();
     
        $this->denyAccessUnlessGranted('CENTER_EDIT', $center);

        $group = new CenterDocGroup();
        $group->setCenter($center);

        $form = $this->createForm(CenterDocGroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($group);
            $em->flush();

            $this->addFlash('info', 'record.updated_successfully');

            return $this->redirectToRoute('doc_groups_index', ['slug' => $slug]);


        }

        return $this->render('_admin_center/doc_groups/edit.html.twig', [
            
            'group' => $group,
            'form' => $form->createView(), 
        ]);




    }

    /**
     * @Route("/{slug}/documents-group/{id}/edit", methods={"GET", "POST"}, name="group_edit")
     * 
     */
    public function centerDocGroupEdit(Request $request, $slug, CenterDocGroup $centerDocGroup)
    {
        $center = $this->getUser()->getCenter();

        $this->denyAccessUnlessGranted('CENTER_EDIT', $center);        

        $form = $this->createForm(CenterDocGroupType::class, $centerDocGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('info', 'record.updated_successfully');

            return $this->redirectToRoute('doc_groups_index', ['slug' => $slug] );
        }

        return $this->render('_admin_center/doc_groups/edit.html.twig', [
             
            'group' => $centerDocGroup,
            'form' => $form->createView(),
        ]);
    }





}
