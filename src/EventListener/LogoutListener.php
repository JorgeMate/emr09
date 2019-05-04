<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\UserLog;


class LogoutListener implements LogoutHandlerInterface {

    private $em;
    private $idLogin;

    private $session;
    
    public function __construct(EntityManagerInterface $entityManager, SessionInterface $session)
    {
        $this->em = $entityManager;
        $this->idLogin = $session->get('idLogin');  
        // el Id del login que hemos almacenado en la sesión
        $this->session = $session;
    }
    
    /**
    * @{inheritDoc}
    */
    public function logout(Request $request, Response $response, TokenInterface $token)
    {

        if($this->idLogin){ // Exite una variable de sesión almacenando idLogin

            $logRecord = $this->em->getRepository(UserLog::class)->find($this->idLogin);

            if (!$logRecord) {
                throw $this->createNotFoundException(
                    'No LOG found for id '.$this->idLogin
                );
            }

            if ($logRecord->getLogoutType() == null) {
                // no ha sido manipulado por el SessionIdleHandler
                $logRecord->setLogoutType(1);
                $this->em->flush();
            } else {
                $this->session->getFlashBag()->set('danger', 'user.iddle_logout');
            }
        }    


    }
}
