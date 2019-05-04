<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['USER_EDIT', 'USER_VIEW', 'USER_DEACTIVATE'])
            && $subject instanceof \App\Entity\User;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if (in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            return true;
        }

        $userAsking = $user;
        $userToView = $subject;

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'USER_EDIT':
                // logic to determine if the user can EDIT
                // return true or false

                if($userAsking->getId() === $userToView->getId()) return true;

                if ($userAsking->getCenter()->getId() === $userToView->getCenter()->getId()){
                    // Solo accedemos a los usuarios de nuestro centro
                    if ($userAsking->getCenterUser() 
                        || !(in_array('ROLE_ADMIN', $userToView->getRoles()))) return true;
                        // Solo accedo si soy el propietario o no es otro administrador.
                }
                     

                // Un administrador no puede editar a otros administradores
                break;

            case 'USER_VIEW':
                // logic to determine if the user can VIEW
                // return true or false
                return $userAsking->getCenter()->getId() === $userToView->getCenter()->getId();                
                break;

            //case 'USER_DEACTIVATE':
            //  return in_array('ROLE_ADMIN', $userToView->getRoles());
                // Los administradores no pueden desactivar a otros administradores (o al propietario).
        }

        return false;
    }
}
