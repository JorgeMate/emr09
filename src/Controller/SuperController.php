<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Entity\Center;
use App\Entity\Sessions;
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
}
