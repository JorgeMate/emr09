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

use App\Form\NewPatientType;
use App\Form\ConsultType;
use App\Form\HistoriaType;
use App\Form\MedicatType;
use App\Form\StoredImgType;
use App\Form\StoredDocType;
use App\Form\OperaType;

use App\Entity\Center;
use App\Entity\Consult;
use App\Entity\Historia;
use App\Entity\Medicat;
use App\Entity\Opera;
use App\Entity\StoredImg;

use Doctrine\ORM\EntityManagerInterface;


/**
 * Controller used to manage current user.
 *
 * @Security("is_granted('ROLE_USER')")
 *
 */
class PatientController extends AbstractController
{
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

            $this->addFlash('success', 'record.updated_successfully');

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

        $center = $this->getUser()->getCenter();

        $repository = $em->getRepository(Center::class);
        
        $patId = $patient->getId();
                
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

            $this->addFlash('success', 'record.updated_successfully');
    
            return $this->redirectToRoute('patient_show', [  'id' => $patient->getId() ] );

        }

        $storedImg = new StoredImg();
        $storedImg->setPatient($patient);

        $formDoc = $this->createForm(StoredDocType::class, $storedImg);
        $formDoc->handleRequest($request);
        if ($formDoc->isSubmitted() && $formDoc->isValid()) {

            $em->persist($storedImg);
            $em->flush();
                
            $this->addFlash('success', 'doc.up_suc');
            $slug = $patient->getUser()->getCenter()->getSlug();
    
            return $this->redirectToRoute('patient_show', ['slug' =>$slug , 'id' => $patient->getId() ] );
            
        }

        $formImg = $this->createForm(StoredImgType::class, $storedImg);
        $formImg->handleRequest($request);
        if ($formImg->isSubmitted() && $formImg->isValid()) {
          
            $em->persist($storedImg);
            $em->flush();
                
            $this->addFlash('success', 'img.up_suc');
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


}
