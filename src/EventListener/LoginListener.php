<?php

namespace App\EventListener;

use App\Utils\AgentInfo;

use App\Entity\UserLog;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

//use App\EventListener\InteractiveLoginEvent;

class LoginListener
{
    private $em;
    private $session;
    
    public function __construct(EntityManagerInterface $em, SessionInterface $session)
    {
        $this->em = $em;
        $this->session = $session;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {   
        $user = $event->getAuthenticationToken()->getUser();
        
        $userLog = new UserLog;
        $userLog->setUser($user);
        $userLog->setIp($_SERVER['REMOTE_ADDR']);
        
        $agentInfo = new AgentInfo;
        $userLog->setAgent($agentInfo->check());
        
        $this->em->persist($userLog);
        $this->em->flush();

        $idLog = $userLog->getId();
        // Guardamos en la sesión el ID del log para encontrarlo y actualizarlo al hacerlogout
        $this->session->set('idLogin', $idLog);

    }    

}