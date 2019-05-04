<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Entity\Center;
use App\Entity\Sessions;

//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


use App\Form\CenterType;

use App\Repository\UserRepository;


//use FOS\ElasticaBundle\Manager\RepositoryManager;


/**
 * Controller used to manage ALL !!!
 *
 * @Route("/super")
 * @Security("is_granted('ROLE_SUPER_ADMIN')")
 *
 */
class SuperController extends AbstractController
{
    /**
    * @Route("/cpanel", methods={"GET"}, name="super_cpanel")
    */
    public function superCpanel(): Response
    {


        if(0) {

            $message = 'Sensitive information';
            $secret_key = sodium_crypto_secretbox_keygen();
    
            $encrypted_message = $this->encryptLIB($message, $secret_key);
            $decrypted_message = $this->decryptLIB($encrypted_message, $secret_key);
    

            $secret_key = sodium_crypto_secretbox_keygen();
            $message = 'Sensitive information';

            $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
            $encrypted_message = sodium_crypto_secretbox($message, $nonce, $secret_key);

            $decrypted_message = sodium_crypto_secretbox_open($encrypted_message, $nonce, $secret_key);

            /*

            var_dump([
                SODIUM_LIBRARY_MAJOR_VERSION,
                SODIUM_LIBRARY_MINOR_VERSION,
                SODIUM_LIBRARY_VERSION,
                $secret_key,
                $encrypted_message,
                $decrypted_message
            ]);die;

            */

        }

        return $this->render('_super_admin/cpanel.html.twig', [
            
        ]);
    }

    /**
    * @Route("/centers", methods={"GET"}, name="centers_index")
    */
    public function centerIndex()
    {
        $centers = $this->getDoctrine()
            ->getRepository(Center::class)
            ->findAll();

            if (!$centers) {
                throw $this->createNotFoundException(
                    'No centers found'
                );
            }

        return $this->render('_super_admin/center/index.html.twig', [
            'centers' => $centers,
        ]);
    }

    /**
     * @Route("/center/new", methods={"GET", "POST"}, name="center_new")
     */
    public function centerNew(Request $request): Response
    {
        $center = new Center();
        $center->setEnabled(true);
        
        $form = $this->createForm(CenterType::class, $center);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->persist($center);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'record.updated_successfully');

            return $this->redirectToRoute('centers_index');
        }

        return $this->render('_super_admin/center/new.html.twig', [
            'center' => $center,
            'form' => $form->createView(),
        ]);
    }    


}
