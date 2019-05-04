<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class PatientVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['PATIENT_EDIT', 'PATIENT_VIEW'])
            && $subject instanceof \App\Entity\Patient;
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
            case 'PATIENT_EDIT':
                // logic to determine if the user can EDIT
                // return true or false
                return $subject->getUser()->getCenter() === $user->getCenter();  
                break;

            case 'PATIENT_VIEW':
                // logic to determine if the user can VIEW
                // return true or false
                return $subject->getUser()->getCenter() === $user->getCenter();  
                break;
        }

        return false;
    }
}




