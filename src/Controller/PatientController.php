<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use App\Entity\Patient;
use App\Form\PatientType;
use App\Repository\PatientRepository;

use App\Form\ConsultType;
use App\Form\HistoriaType;
use App\Form\MedicatType;
use App\Form\StoredImgType;
use App\Form\StoredDocType;
//use App\Form\OperaType;

use App\Entity\Consult;
use App\Entity\Historia;
use App\Entity\Medicat;
use App\Entity\Opera;
use App\Entity\StoredImg;

use App\Entity\User;
use App\Entity\Type;
use App\Entity\Place;

use Doctrine\ORM\EntityManagerInterface;
//use Symfony\Component\PropertyInfo\Type;

/**
 * Controller used to manage current user.
 *
 * @Security("is_granted('ROLE_USER')")
 *
 */
class PatientController extends AbstractController
{

    /**
     * @Route("/{slug}/consult/{id}", methods={"GET", "POST"}, name="consult_edit")
     * 
     */
    public function editCon()
    {

    }

     /**
     * @Route("/{slug}/new/patient", methods={"GET", "POST"}, name="patient_new")
     * 
     * NUEVO paciente (del usuario)
     */
    public function newPat(Request $request, $slug): Response
    {

        $user = $this->getUser();
        $center = $user->getCenter();

        $this->denyAccessUnlessGranted('CENTER_EDIT', $center);

        // Todos pueden insertar pacientes !?

        $patient = new Patient();
        $patient->setUser($user);

        $form = $this->createForm(PatientType::class, $patient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($patient);
            $em->flush();

            $this->addFlash('info', 'record.updated_successfully');

            return $this->redirectToRoute('patient_show', ['id' => $patient->getId()]);
        }

        return $this->render('/patient/new.html.twig', [
             
            'patient' => $patient,
            'form' => $form->createView(),
            
        ]);

    }

    /**
     * @Route("/patient/{slug}/search", methods={"GET"}, name="patient_search")
     */
    public function searchPat(Request $request, PatientRepository $patients, $slug): Response
    {

        $center = $this->getUser()->getCenter();

        $this->denyAccessUnlessGranted('CENTER_VIEW', $center);
        ///////////////////////////////////////////////////////

        if (!$request->isXmlHttpRequest()) {
            return $this->render('patient/search.html.twig',[
                'center' => $center,
            ]);
        }

        $centerId = $center->getId();

        $query = $request->query->get('q', '');

        //  $request->getSession()->set('query', $query); Recordamos la búsqueda anterior ?

        $limit = $request->query->get('l', 10);
        $foundPatients = $patients->findBySearchQuery($query, $limit, $centerId);

        $results = [];

        foreach ($foundPatients as $patient) {

            if($patient->getSex()) {
                $sex =' <i class="fas fa-venus text-danger"></i> ';
            } else {
                $sex =' <i class="fas fa-mars text-info"></i> ';
            };
 
            $dob = new \DateTime($patient->getDateOfBirth()->format('Y-m-d'));
            $today = new \DateTime('today');

            $age = $today->diff($dob)->y;
         
            $results[] = [

                'url' => $this->generateUrl('patient_show', ['id' => $patient->getId(), 'slug' => $center->getSlug()]),

                'id' => htmlspecialchars($patient->getId(), ENT_COMPAT | ENT_HTML5),
                'firstname' => htmlspecialchars($patient->getFirstname(), ENT_COMPAT | ENT_HTML5),
                'lastname' => htmlspecialchars($patient->getLastname(), ENT_COMPAT | ENT_HTML5),
                'birthdate' => $patient->getDateOfBirth()->format('d/m/Y'),
                'sex' => $sex,
                'age' => $age,

            ];
        }

        return $this->json($results);
    }  
    
    /**
     * @Route("{slug}/patient/{id}/show", methods={"GET", "POST"}, name="patient_show")
     * 
     * MOSTRAR el paciente id
     */
    public function showPat(Request $request, $slug, Patient $patient, 
                        EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('PATIENT_VIEW', $patient);

        $patId = $patient->getId();
        
        $center = $this->getUser()->getCenter();
                
        $repository = $em->getRepository(Consult::class);
        $consults = $repository->findBy(['patient' => $patId], ['created_at' => 'DESC']);

        $repository = $em->getRepository(Historia::class);
        $historias = $repository->findBy(['patient' => $patId], ['date' => 'DESC']);

        $repository = $em->getRepository(Medicat::class);
        $medicats = $repository->findBy(['patient' => $patId], ['created_at' => 'DESC']);

        $repository = $em->getRepository(Opera::class);
        $operas = $repository->findBy(['patient' => $patId], ['created_at' => 'DESC']);

        $repository = $em->getRepository(StoredImg::class);
        $imgs = $repository->findBy(['patient' => $patId, 'mime_type' => 'image/jpeg'], ['updated_at' => 'DESC']);
        $docs = $repository->findBy(['patient' => $patId, 'mime_type' => 'application/pdf'], ['updated_at' => 'DESC']);

        //var_dump($operas);die;
    
        $consult = new Consult();
        $formConsult = $this->createForm(ConsultType::class, $consult);
        $formConsult->handleRequest($request);
        if ($formConsult->isSubmitted() && $formConsult->isValid()) {

            $consult->setPatient($patient);
            $consult->setUser($this->getUser());

            $em->persist($consult);
            $em->flush();

            $this->addFlash('info', 'record.updated_successfully');
    
            return $this->redirectToRoute('patient_show', ['id' => $patient->getId() ] );

        }

        $storedImg = new StoredImg();
        $storedImg->setPatient($patient);

        $formDoc = $this->createForm(StoredDocType::class, $storedImg);
        $formDoc->handleRequest($request);
        if ($formDoc->isSubmitted() && $formDoc->isValid()) {

            $em->persist($storedImg);
            $em->flush();
                
            $this->addFlash('info', 'doc.up_suc');
            $slug = $patient->getUser()->getCenter()->getSlug();
    
            return $this->redirectToRoute('patient_show', ['id' => $patient->getId() ] );
            
        }

        $formImg = $this->createForm(StoredImgType::class, $storedImg);
        $formImg->handleRequest($request);
        if ($formImg->isSubmitted() && $formImg->isValid()) {
          
            $em->persist($storedImg);
            $em->flush();
                
            $this->addFlash('info', 'img.up_suc');
            $slug = $patient->getUser()->getCenter()->getSlug();
    
            return $this->redirectToRoute('patient_show', ['slug' =>$slug ,'id' => $patient->getId() ] );

        }

        return $this->render('patient/show.html.twig', [
           
            'center' => $center,
            'patient' => $patient,
            'consults' => $consults,
            'historias' => $historias,
            'medicats' => $medicats,
            'operas' => $operas,
            'imgs' => $imgs,
            'docs' => $docs,

            'formConsult' => $formConsult->createView(),
            'formDoc' => $formDoc->createView(),
            'formImg' => $formImg->createView(),
            
        ]);
    }
    
    /**
     * @Route("{slug}/patient/{id}/edit", methods={"GET", "POST"}, name="patient_edit")
     * 
     * EDITAR el paciente id
     */
    public function editPat($slug, Request $request, Patient $patient): Response
    {

        $this->denyAccessUnlessGranted('PATIENT_EDIT', $patient);

        $form = $this->createForm(PatientType::class, $patient);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            
            $this->addFlash('info', 'record.updated_successfully');
            
            return $this->redirectToRoute('patient_show', 
                ['id' => $patient->getId()] );
        }

        return $this->render('patient/edit.html.twig', [

            'patient' => $patient,
            'formPat' => $form->createView(),
        ]);

    }
    





        /**
     * @Route("/{slug}/patient/{id}/new/medicat", methods={"GET", "POST"}, name="medicat_new")
     * 
     */
    public function newMed(Request $request, $slug, Patient $patient): Response
    {
        $user = $this->getUser();
        $center = $user->getCenter();

        $this->denyAccessUnlessGranted('CENTER_EDIT', $center);

        $medicat = new Medicat();
        $medicat->setUser($user);
        $medicat->setPatient($patient);

        $form = $this->createForm(MedicatType::class, $medicat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();            
            $em->persist($medicat);
            $em->flush();

            $this->addFlash('info', 'record.updated_successfully');

            return $this->redirectToRoute('patient_show', ['slug' => $slug,  'id' => $patient->getId()]);
        } 

        return $this->render('/patient/medicat/new.html.twig', [
            'slug' => $slug,
            'patient' => $patient,
            'medicat' => $medicat,
            'form' => $form->createView(),          
        ]);

    }

    /**
     * @Route("/medicat/{id}", methods={"POST"}, name="medicat_stop")
     */
    public function medicatStop(Medicat $medicat)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $medicat->setStopAt(new \DateTime("now"));
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return new Response(null, 204);
    }

///////////////////////////////////////////////////////////////////////////////////////////



    /**
     * @Route("/{slug}/patient/{id}/new/consult", methods={"GET", "POST"}, name="consult_new")
     * 
     */
    public function newCon(Request $request, $slug, Patient $patient): Response
    {
        $user = $this->getUser();
        $center = $user->getCenter();
       
        $this->denyAccessUnlessGranted('CENTER_EDIT', $center);

        $consult = new Consult();
        $consult->setUser($user);
        $consult->setPatient($patient);

        $form = $this->createForm(ConsultType::class, $consult);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($consult);
            $em->flush();

            $this->addFlash('info', 'record.updated_successfully');

            return $this->redirectToRoute('patient_show', ['slug'=>$slug, 'id' => $patient->getId()]);
        } 
        
        return $this->render('/patient/consult/new.html.twig', [
            'wtab' => 2,
            'center' => $center,
            'patient' => $patient,
            'consult' => $consult,
            'form' => $form->createView(),
            
        ]);

    }

    /**
     * @Route("/{slug}/patient/{id}/new/historia", methods={"GET", "POST"}, name="historia_new")
     * 
     */
    public function newHis(Request $request, $slug, Patient $patient): Response
    {
        $user = $this->getUser();
        $center = $user->getCenter();

        $this->denyAccessUnlessGranted('CENTER_EDIT', $center);

        $historia = new Historia();
        $historia->setUser($user);
        $historia->setPatient($patient);

        $form = $this->createForm(HistoriaType::class, $historia);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($historia);
            $em->flush();

            $this->addFlash('info', 'record.updated_successfully');

            return $this->redirectToRoute('patient_show', [  'id' => $patient->getId()]);
        } 

        return $this->render('/patient/historia/new.html.twig', [
            'center' => $center,
            'patient' => $patient,
            'historia' => $historia,
            'form' => $form->createView(),          
        ]);

    }

    /**
     * @Route("/{slug}/patient/{id}/new/treatment", methods={"GET", "POST"}, name="opera_new")
     * 
     */
    public function newOpera(Request $request, $slug, Patient $patient,
         EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $centerId = $this->getUser()->getCenter()->getId();
        
        //var_dump($centerId);die;

        $this->denyAccessUnlessGranted('PATIENT_EDIT', $patient);

        $repository = $em->getRepository(Type::class);
        $types = $repository->findBy(['center' => $centerId], ['name' => 'ASC']);

        $repository = $em->getRepository(User::class);
        $medics = $repository->findBy(['center' => $centerId, 'medic' => true], ['lastname' => 'ASC']);

        $repository = $em->getRepository(Place::class);
        $places = $repository->findBy(['center' => $centerId], ['name' => 'ASC']);


        //var_dump($types);die;

        $opera = new Opera();
        $opera->setUser($user);
        $opera->setPatient($patient);

        if (false) {

            $em = $this->getDoctrine()->getManager();

            var_dump($opera);die;

            $em->persist($opera);
            $em->flush();

            $this->addFlash('success', 'record.updated_successfully');

            return $this->redirectToRoute('patient_show', ['slug' => $slug, 'id' => $patient->getId()]);        

        }

        return $this->render('/patient/opera/new.html.twig', [
        
            'types' => $types,
            'medics' => $medics,
            'places' => $places,

            'patient' => $patient,
            'opera' => $opera,
        
        ]);





    }





}
