<?php

namespace App\Security;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\UserLog;


      
class SessionIdleHandler
{
    protected $session;
    protected $securityToken;
    protected $router;
    protected $maxIdleTime;

    private $em;
    private $idLogin;

    public function __construct($maxIdleTime, SessionInterface $session, 
        TokenStorageInterface $securityToken, RouterInterface $router, EntityManagerInterface $em)
    {
        $this->maxIdleTime = $maxIdleTime;

        $this->session = $session;
        $this->securityToken = $securityToken;
        $this->router = $router;

        $this->idLogin = $session->get('idLogin');  
        // el Id del login que hemos almacenado en la sesión
        $this->em = $em;        

    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST != $event->getRequestType()) {
            return;
        }

        if ($this->maxIdleTime > 0) {

            $this->session->start();
            $lapse = time() - $this->session->getMetadataBag()->getLastUsed();

            if ($lapse > $this->maxIdleTime) {

                // 2 timeout
                // 1 normal ... enlogoutListener
                // null ... sin logout normal

                $logRecord = $this->em->getRepository(UserLog::class)->find($this->idLogin);
                if($logRecord){
                    $logRecord->setLogoutType(2);
                    $this->em->flush();    
                }

                $this->securityToken->setToken(null);
                $this->session->getFlashBag()->set('info', 'user.iddle_logout');


                // logout is defined in security.yaml.  See 'Logging Out' section here:
                // https://symfony.com/doc/4.1/security.html
                $event->setResponse(new RedirectResponse($this->router->generate('logout')));
            }
        }
    }
}  