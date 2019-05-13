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

use App\SuperSaaS\Client;

use App\Entity\CenterDocGroup;
use App\Entity\UserDoc;

use App\Form\UserDocType;



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
     * @Route("/{slug}/cpanel", methods={"GET","POST"}, name="user_cpanel")
     */
    public function userCpanel($slug): Response
    {
        $user = $this->getUser();
        $center = $this->getUser()->getCenter();
        $groups = $center->getCenterDocGroups();

        $client = null;
        $agendas = null;
        $checksum = null;

        if($center->getSsaasAccountName() && $center->getSsaasApiKey()){

            // Comprobamos si el usuario tiene uso autorizado a las agendas
            // TODO, también moverlo a __consttruct para hacerlo una sola vez

            if(false){

                $client = new Client();  // SaasS
                /////////////////////////////////
                $client->account_name = $center->getSsaasAccountName();
                $client->api_key = $center->getSsaasApiKey();

            //    $usersSaas = $client->users->getList();
            //    var_dump($usersSaas);die;

                $agendas = $client->schedules->getList();
                //var_dump($agendas);die;

                $checksum = md5($client->account_name . $client->api_key . $user->getEmail());
                //var_dump($checksum);die;
            }
        }

        return $this->render('user/cpanel.html.twig', [

            'user' => $user,
            'center' => $center,
            'groups' => $groups,

            'client' => $client,
            'agendas' => $agendas,

            'checksum' => $checksum,
             
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
    
    
        /**
     * @Route("/{slug}/documents-groups", methods={"GET", "POST"}, name="doc_groups_index")
     * 
     * 
     */
    public function programDocGroupsIndex(Request $request, $slug): Response
    {

        $center = $this->getUser()->getCenter();

        $this->denyAccessUnlessGranted('CENTER_VIEW', $center);

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(CenterDocGroup::class);
        $groups = $repository->findBy(['center' => $center->getId()], ['name' => 'ASC']);

        return $this->render('_admin_center/doc_groups/index.html.twig', [
             
            'slug' => $slug,
            'groups' => $groups,
        ]);     

        
    }



    /**
     * @Route("/{slug}/documents-group/{id}/index", methods={"GET", "POST"}, name="docs_index")
     * 
     */ 
    public function docsIndex(Request $request, $slug, CenterDocGroup $centerDocGroup)
    {

        $center = $this->getUser()->getCenter();

        $this->denyAccessUnlessGranted('CENTER_VIEW', $center);

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(UserDoc::class);
        $docs = $repository->findBy(['centerDocGroup' => $centerDocGroup->getId()], ['name' => 'ASC']);

        $newDoc = new UserDoc();
        $newDoc->setUser($this->getUser());
        $newDoc->setCenterdocGroup($centerDocGroup);

        $formDoc = $this->createForm(UserDocType::class, $newDoc);
        $formDoc->handleRequest($request);
        if ($formDoc->isSubmitted() && $formDoc->isValid()) {

            $em->persist($newDoc);
            $em->flush();
                
            $this->addFlash('info', 'doc.up_suc');
            
    
            return $this->redirectToRoute('docs_index', ['slug' => $slug, 'id' => $centerDocGroup->getId() ] );
            
        }


        return $this->render('_admin_center/doc_groups/doc_index.html.twig', [
             
            'centerDocGroup' => $centerDocGroup,
            'docs' => $docs,
            'form' => $formDoc->createView(),
            
        ]);     

    }





}