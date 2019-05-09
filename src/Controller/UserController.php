<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\Form\UserType;
use App\Form\Type\ChangePasswordType;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Treatment;
use App\Entity\Opera;
use App\Entity\StoredImg;



/**
 * Controller used to manage current user.
 *
 * @Route("/user")
 * @Security("is_granted('ROLE_USER')")
 *
 */
class UserController extends AbstractController
{
 
    /**
     * @Route("/{slug}/cpanel", methods={"GET"}, name="user_cpanel")
     */
    public function userCpanel($slug): Response
    {
        $user = $this->getUser();
        $center = $this->getUser()->getCenter();
        $groups = $center->getCenterDocGroups();

        return $this->render('user/cpanel.html.twig', [
            'user' => $user,
            'center' => $center,
            'groups' => $groups,
             
        ]);
    }

    /**
     * @Route("/profile/edit", methods={"GET", "POST"}, name="profile_edit")
     */
    public function profileEdit(Request $request): Response
    {
        $user = $this->getUser();
        $slug = $user->getCenter()->getSlug();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('info', 'record.updated_successfully');
            return $this->redirectToRoute('user_cpanel', ['slug' => $slug]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/profile/change-password", methods={"GET", "POST"}, name="user_change_password")
     */
    public function changePassword(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($encoder->encodePassword($user, $form->get('newPassword')->getData()));

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('logout');
        }


        return $this->render('user/change_password.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }    


    

    /**
     * @Route("/treatments", methods={"POST"}, name="treatments_get")
     */
    public function getTreatmentsFromTypeApi(Request $request, EntityManagerInterface $em)
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $typeId = $request->query->get('query');

        //$typeId = $type->getId();
        $results = [];
        
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Treatment::class);
        $foundTreatments = $repository->findBy(['type' => $typeId], ['name' => 'ASC']);

        foreach ($foundTreatments as $treatment){

            $results[] = [
                'id' => $treatment->getId(),
                'name' => $treatment->getName(),
            ];

        };

        return $this->json($results);
    }

    /**
     * @Route("/opera/{id}", methods={"DELETE"}, name="opera_delete")
     */
    public function deleteOpera(Opera $opera)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $em = $this->getDoctrine()->getManager();
        $em->remove($opera);
        $em->flush();
        return new Response(null, 204);
    }    

    /**
     * @Route("/img/{id}", methods={"DELETE"}, name="img_delete")
     */
    public function deleteImg(StoredImg $storedImg)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $em = $this->getDoctrine()->getManager();
        $em->remove($storedImg);
        $em->flush();
        return new Response(null, 204);
    }    




}