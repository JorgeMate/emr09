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

    /**
     * @Route("/search", methods={"GET"}, name="user_search")
     */
    public function search(Request $request, UserRepository $users): Response
    {

        $center = $this->getUser()->getCenter();

        if (!$request->isXmlHttpRequest()) {
            return $this->render('_super_admin/user/search.html.twig');
        }

        $query = $request->query->get('q', '');
        $limit = $request->query->get('l', 10);
        $foundUsers = $users->findBySearchQuery($query, $limit);

        // var_dump($foundUsers);die;
        
        $results = [];
        foreach ($foundUsers as $user) {

            $roles = $user->getRoles();
            foreach ($roles as $role){
                $roleX = '';
                if($role == 'ROLE_ADMIN') { 
                    $roleX ='- <i class="fas fa-key"></i>';
                     break;
                }
            }

            if($user->getEnabled()) {
                $activo =' <i class="fa fa-sun"></i> ';
            } else {
                $activo =' <i class="fas fa-cloud"></i> ';
            };

       //     'url' => $this->generateUrl('blog_post', ['slug' => $post->getSlug()]),

            $results[] = [

                'url' => $this->generateUrl('user_edit', ['slug' => $user->getcenter()->getSlug(), 'id' => $user->getId()]),

                'email' => htmlspecialchars($user->getEmail(), ENT_COMPAT | ENT_HTML5),
                'from' => $user->getCreatedAt()->format('d/m/y'),
                'id' => htmlspecialchars($user->getId(), ENT_COMPAT | ENT_HTML5),
                'firstname' => htmlspecialchars($user->getFirstname(), ENT_COMPAT | ENT_HTML5),
                'lastname' => htmlspecialchars($user->getLastname(), ENT_COMPAT | ENT_HTML5),

                'role' => $roleX,
                'enabled' => $activo,

            ];
        }

        return $this->json($results);



    }

    /**
     * @Route("/logs", methods={"GET"}, name="logs")
     */
    public function logs(): Response
    {
    
        $conn = $this->getDoctrine()->getManager()->getConnection();
        $sql = '
        SELECT user_log.*, user.email, TIMESTAMPDIFF(MINUTE, login_time, logout_time) AS enabled
        FROM user_log 
        INNER JOIN user ON user_log.user_id = user.id
        ORDER BY user_log.id DESC LIMIT 50
        ';
        $stmt = $conn->prepare($sql);
        //$stmt->bindValue(':email', $logged_in_user_array[$key]['user']);
        $stmt->execute();
        $logs = $stmt->fetchAll();

        return $this->render('_super_admin/logs.html.twig', [
            'logs' => $logs
        ]);

    }

    private static function unserialize_php($session_data, $delimiter) {
        $return_data = array();
        $offset = 0;
        while ($offset < strlen($session_data)) {
            if (!strstr(substr($session_data, $offset), $delimiter)) {
                throw new \Exception("invalid data, remaining: " . substr($session_data, $offset));
            }
            $pos = strpos($session_data, $delimiter, $offset);
            $num = $pos - $offset;
            $varname = substr($session_data, $offset, $num);
            $offset += $num + 1;
            $data = unserialize(substr($session_data, $offset));
            $return_data[$varname] = $data;
            $offset += strlen(serialize($data));
        }
        return $return_data;
    }

    
    /**
     * @Route("/sessions", methods={"GET"}, name="sessions")
     */
    public function sessions(): Response
    {
        $conn = $this->getDoctrine()->getManager()->getConnection();
        $sql = '
        SELECT sessions.* FROM sessions
        ORDER BY sess_time DESC
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $sessions = $stmt->fetchAll();

        //  var_dump($sessions);die;
    
        $logged_in_user_array = array();

        foreach($sessions as $key => $session) {
            $logged_in_user_array[$key]['updated'] = date('d-m-y H:i:s',$session['sess_time']);
            $decoded_array = $this->unserialize_php( $session['sess_data'], '|' );

            //  var_dump($decoded_array);die;

            $logged_in_user_array[$key]['user'] = '';
            if(isset($decoded_array['_sf2_attributes']['_security.last_username'])){
                $logged_in_user_array[$key]['user'] = $decoded_array['_sf2_attributes']['_security.last_username'];
            }
          
            $sql = '
            SELECT center.id, center.name, center.slug FROM user 
            LEFT OUTER JOIN center ON user.center_id = center.id
            WHERE user.email = :email
            ';
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':email', $logged_in_user_array[$key]['user']);
            $stmt->execute();
            $center = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            //  var_dump($center);die;
    
            $logged_in_user_array[$key]['center'] = $center['name'];
            $logged_in_user_array[$key]['center_id'] = $center['id'];

            $logged_in_user_array[$key]['login'] = date('d-m-y H:i:s',$decoded_array['_sf2_meta']['c']); 

            $logged_in_user_array[$key]['activity'] = round((time() - $decoded_array['_sf2_meta']['c']) / 60) . ' min.' ;
            $logged_in_user_array[$key]['iddle'] = round((time() - $session['sess_time']) / 60) . ' min.' ;

            $logged_in_user_array[$key]['id'] = $session['sess_id'];

        }   
                    
        // var_dump($logged_in_user_array);die;

         return $this->render('_super_admin/sessions.html.twig', [
            'centerSlug' => $center['slug'],
            'sessions' => $logged_in_user_array
        ]);  

    }

    /**
     * @Route("/session/{id}", methods={"DELETE"}, name="session_delete")
     */
    public function deleteSession(Sessions $session)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $em = $this->getDoctrine()->getManager();
        $em->remove($session);
        $em->flush();
        return new Response(null, 204);
    }    



}
