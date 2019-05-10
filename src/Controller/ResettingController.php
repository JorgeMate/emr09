<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Request;

use App\Utils\TokenGeneratorInterface;

use App\Form\ResettingRequestFormType;

use App\Entity\User;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

/**
 *
 * @Route("/resetting")
 * @Security("not is_granted('ROLE_USER')")
 *
 */
class ResettingController extends AbstractController
{
  
    private $entityManager;
    private $tokenGenerator;

    /**
     * @var int
     */
    private $retryTtl;

    private $form;
    private $mailer;
    private $emailSends;

    private $emailToCheck;

    public function __construct(EntityManagerInterface $entityManager, TokenGeneratorInterface $tokenGenerator, \Swift_Mailer $mailer){

        $this->retryTtl = 7200; // 2 horas

        $this->entityManager = $entityManager;
        $this->tokenGenerator = $tokenGenerator;
        $this->mailer = $mailer;

        $this->emailSends = 'kimberly.systems@gmail.com';

        // Parametrizarlo desde el config !!!

    }

    /**
    * @Route("/request", name="reset_request")
    */
    public function requestAction(Request $request)
    {
        $this->form = $this->createForm(ResettingRequestFormType::class);        

        $this->form->handleRequest($request);

        if($this->form->isSubmitted() && $this->form->isValid()) {

            $formData = $this->form->getData();

            /////////////////////////////////////////
            $this->emailToCheck = $formData['email'];
            
            return $this->sendEmailAction();

        } else {

            return $this->render('resetting/request.html.twig', [   
                'resetting' => $this->form->createView(),
            ]);

        }
        
    }

   /**
    * @Route("/reset/{token}", methods={"GET", "POST"}, name="reset_reset")
    */
    public function resetAction(Request $request, $token)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['confirmationToken' => $token]);
        if (null === $user) {
            return new RedirectResponse($this->container->get('router')->generate('app_login'));
        }

        $this->form = $this->createForm(ResettingFormType::class, $user);        

        $this->form->handleRequest($request);

        if($this->form->isSubmitted() && $this->form->isValid()) {

            $user->setConfirmationToken(null);
            $user->setPasswordRequestedAt(null);
            $user->setEnabled(true);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', 'contraseña actualizada con éxito');

            return new RedirectResponse($this->generateUrl('app_login'));

        } else {

            return $this->render('resetting/reset.html.twig', [   
                'resetting' => $this->form->createView(),
            ]);

        }

    }


    public function sendEmailAction()
    {    
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $this->emailToCheck]);

        if (null == $user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('Email could not be found.');
            // TODO i18n en EXCEPTIONS !   
        } else {

            if (!$user->isPasswordRequestNonExpired($this->retryTtl)) {
                // es un email válido y no ha expirado la solicitud anterior, solo reenviamos el token
            //} else {
                //    if (null === $user->getConfirmationToken() {
                $user->setConfirmationToken($this->tokenGenerator->generateToken());
                $user->setPasswordRequestedAt(new \DateTime());
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }
             
            $token = $user->getConfirmationToken();

            $returnPath = 'http://';

            $message = (new \Swift_Message('Link de verificación'))
                ->setFrom($this->emailSends)
                //->setTo($user->getEmail())
                ->setTo('jorge65@mailinator.com')
                ->setSubject('Password recovery')
                ->setBody(

                    $this->renderView(
                        'emails/recovery-link.html.twig',
                        ['token' => $token]
                    ),
                    'text/html'

                );

            $this->mailer->send($message);

            $this->addFlash('success', 'label.resetting_sent');

            return $this->checkEmailAction();

            //return $this->redirectToRoute('logout');
            //return new RedirectResponse($this->generateUrl('logout', array('email' => $this->emailRequests)));
        }

        return;
                
    }

    public function checkEmailAction()
    {
        $email = $this->emailToCheck;

        if(empty($email)){
            return new RedirectResponse($this->generateUrl('reset_request'));
        }

        //dump($email);die;

        return $this->render('resetting/check_email.html.twig', array(
            'tokenLifetime' => ceil($this->retryTtl / 3600),
        ));



    }


 
    



}
