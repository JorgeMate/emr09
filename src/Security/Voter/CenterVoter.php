<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CenterVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['CENTER_EDIT', 'CENTER_VIEW'])
            && $subject instanceof \App\Entity\Center;
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

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'CENTER_EDIT':
                // logic to determine if the user can EDIT
                // return true or false
                return $subject === $user->getCenter();  //  || in_array('ROLE_SUPER_ADMIN', $user->getRoles())

                break;
            case 'CENTER_VIEW':
                // logic to determine if the user can VIEW
                // return true or false
                return $subject === $user->getCenter(); // || in_array('ROLE_SUPER', $user->getRoles());
                
                break;
        }

        return false;
    }
}




