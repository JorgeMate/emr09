<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use App\Repository\PatientRepository;

use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

use App\Form\PatientType;

use App\Form\ConsultType;
use App\Form\HistoriaType;
use App\Form\MedicatType;
use App\Form\StoredImgType;
use App\Form\StoredDocType;

use App\Form\OperaType;


use App\Entity\Patient;

use App\Entity\Consult;
use App\Entity\Historia;
use App\Entity\Medicat;
use App\Entity\Opera;
use App\Entity\StoredImg;

use App\Entity\User;
use App\Entity\Type;
use App\Entity\Place;
use App\Entity\Treatment;

use Doctrine\ORM\EntityManagerInterface;


use Aws\S3\S3Client;
//use Gaufrette\Adapter\AwsS3 as AwsS3Adapter;
//use Gaufrette\Filesystem;


use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;


//use Symfony\Component\PropertyInfo\Type;

/**
 * Controller used to manage current user.
 *
 * @Security("is_granted('ROLE_USER')")
 *
 */
class PatientController extends AbstractController
{

    private $s3client;
    private $filesystem;

    public function __construct()
    {
        $this->s3client = new S3Client([
            'credentials' => [
                'key'     => '%env(AWS_KEY)%',
                'secret'  => '%env(AWS_SECRET_KEY)%',
            ],
            'version' => 'latest',
            'region'  => 'eu-west-3',
        ]);

        $adapter = new AwsS3Adapter($this->s3client,'%env(AWS_BUCKET_NAME)%');
        $this->filesystem = new Filesystem($adapter);

    }

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

            return $this->redirectToRoute('patient_show', ['slug' => $slug, 'id' => $patient->getId()]);
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

        ///////////////////////////////////////////////
        $debts = $repository->findNotPaidOpera($patId); 

        //var_dump($debts);die;

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

        // Entramos al mismo repositorio por los 2 lados

        $formDoc = $this->createForm(StoredDocType::class, $storedImg);
        $formDoc->handleRequest($request);
        if ($formDoc->isSubmitted() && $formDoc->isValid()) {

            $em->persist($storedImg);
            $em->flush();
                
            $this->addFlash('info', 'doc.up_suc');
            $slug = $patient->getUser()->getCenter()->getSlug();
    
            return $this->redirectToRoute('patient_show', ['slug' => $slug, 'id' => $patient->getId() ] );
            
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

            'debts' => $debts,

            'imgs' => $imgs,
            'docs' => $docs,

            'formConsult' => $formConsult->createView(),
            'formDoc' => $formDoc->createView(),
            'formImg' => $formImg->createView(),

            'show_confirmation' => true,
            
        ]);
    }
    
    /**
     * @Route("{slug}/patient/{id}/edit", methods={"GET", "POST"}, name="patient_edit")
     * 
     * EDITAR el paciente id
     */
    public function editPat($slug, Request $request, Patient $patient, EntityManagerInterface $em): Response
    {

        $this->denyAccessUnlessGranted('PATIENT_EDIT', $patient);

        $form = $this->createForm(PatientType::class, $patient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            
            $this->addFlash('info', 'record.updated_successfully');
            
            return $this->redirectToRoute('patient_show', 
                ['slug' => $slug, 'id' => $patient->getId()] );
        }

        return $this->render('patient/edit.html.twig', [
           
            'patient' => $patient,
            'formPat' => $form->createView(),
        ]);

    }
    

    /**
     * @Route("/{slug}/patients", methods={"GET"}, name="patient_index")
     */
    public function indexPat(Request $request)
    {

        //$lastIdPat = $this->getDoctrine()->getRepository(Patient::class)->lastInsertedId();
        //$lastIdPat = $lastIdPat['lastIdPat'];

        //var_dump($lastIdPat);die;

        $group = '';

        $entity = $request->get('entity');
        $entity_id = $request->get('id');

        $center = $this->getUser()->getCenter();

        $em = $this->getDoctrine()->getManager();

        $queryBuilder = $em->createQueryBuilder($center->getId())
            ->select('p', 's', 'i') 
            ->from('App\Entity\Patient', 'p')
            ->innerJoin('p.user','u')
            ->innerJoin('u.center','c')
            ->leftJoin('p.source', 's')
            ->leftjoin('p.insurance', 'i')
            
            ->orderBy('p.id', 'DESC');
            
            // ->leftjoin('p.place', 'pl')

            // ->setParameter('last', $lastIdPat);
            // ->andWhere('p.id + 100 > :last')

        if($center->getId()){
            $queryBuilder
                ->andWhere('c.id = :val')
                ->setParameter('val', $center->getId());
        };

        if($entity && $entity_id){
            switch($entity){
            case 'ins':
                $queryBuilder
                    ->andWhere('p.insurance = :insuranceId')
                    ->setParameter('insuranceId', $entity_id);
            break;    
            case 'source':
                $queryBuilder
                    ->andWhere('p.source = :sourceId')
                    ->setParameter('sourceId', $entity_id);
            break;    
            case 'place':
                $queryBuilder
                    ->andWhere('p.place = :placeId')
                    ->setParameter('placeId', $entity_id);
            break;    

            }

        };

        $queryBuilder->setMaxResults('100');
            
        $adapter = new DoctrineORMAdapter($queryBuilder);

        $pagerfanta = new Pagerfanta($adapter);

        $pagerfanta->setMaxPerPage(10); // 10 by default
        $maxPerPage = $pagerfanta->getMaxPerPage();

        $pagerfanta->getCurrentPageOffsetStart(3);
        $pagerfanta->getCurrentPageOffsetEnd(3);

        if (isset($_GET["page"])) {
            //  $t = $pagerfanta->getNbPages();
            //  var_dump($t); die;
            $page = min($_GET["page"], $pagerfanta->getNbPages());
            $pagerfanta->setCurrentPage($page);
        }

        return $this->render('patient/index.html.twig', [
             
            'group' => $group,
            'center' => $center,
            'my_pager' => $pagerfanta,
            'order' => 'íd',
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

        //$opera = new Opera();
        //$opera->setUser($user);
        //$opera->setPatient($patient);


        return $this->render('/patient/opera/new.html.twig', [

            'slug' => $slug,
        
            'types' => $types,
            'medics' => $medics,
            'places' => $places,
            'patient' => $patient,
        
        ]);





    }

    /**
     * @Route("/{slug}/save/treatment-of-patient", methods={"POST"}, name="opera_save")
     * 
     */
    public function saveOpera(Request $request, EntityManagerInterface $em, $slug): Response
    {

        $patientId = $request->request->get('patientId');
        $treatmentId = $request->request->get('treatmentId');
        $userId = $request->request->get('userId');
        $placeId = $request->request->get('placeId');

        $madeAt = $request->request->get('madeAt');

        $repository = $em->getRepository(User::class);
        $user = $repository->find($userId);

        $repository = $em->getRepository(Patient::class);
        $patient = $repository->find($patientId);        

        $repository = $em->getRepository(Place::class);
        $place = $repository->find($placeId);

        $repository = $em->getRepository(Treatment::class);
        $treatment = $repository->find($treatmentId);


        $dateMod = substr($madeAt,6,4) . '/' . substr($madeAt,3,2) . '/' . substr($madeAt,0,2);

        //var_dump($dateMod);die;
        
        $mod = new \DateTime($dateMod);

        $opera = new Opera();

        $opera->setUser($user);
        $opera->setPatient($patient);
        $opera->setPlace($place);
        $opera->setTreatment($treatment);

        $opera->setValue($treatment->getValue());

        $opera->setMadeAt($mod);

        $em->persist($opera);
        $em->flush();

        return $this->redirectToRoute('patient_show', ['slug' => $slug, 'id' => $patientId]);        

    }

    /**
     * @Route("/{slug}/treatment/{id}/edit", methods={"GET","POST"}, name="opera_edit")
     * 
     */

    public function editOpera(Request $request, Opera $opera, EntityManagerInterface $em, $slug): Response
    {

        $patient = $opera->getPatient();

        $this->denyAccessUnlessGranted('PATIENT_EDIT', $patient);

        $formOpera = $this->createForm(OperaType::class, $opera);
        $formOpera->handleRequest($request);

        if ($formOpera->isSubmitted() && $formOpera->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('info', 'record.updated_successfully');

            return $this->redirectToRoute('patient_show', ['slug' => $slug, 'id' => $patient->getId()] );
        }

        return $this->render('patient/opera/edit.html.twig', [
             
            'opera' => $opera,
            'form' => $formOpera->createView(),
        ]);
    }        


    /**
     * @param string $imgName
     *
     * @return \Aws\Result|bool
     */
    public function getDocumentFromPrivateBucket($ImgName)
    {

        //var_dump($ImgName);die;

        try {

            return $this->filesystem->get(
                [
                    'Bucket' => '%env(AWS_BUCKET_NAME)%',
                    'Key'    => 'IMGS/'.$ImgName,
                ]
            );

        } catch (S3Exception $e) {
            // Handle your exception here
        }
    }

    /**
     * @param Document $document
     * @Route("/{id}/show-document", name="show_document")
     * @return RedirectResponse|Response
     */
    public function showDocumentAction(StoredImg $document)
    {
        //$awsS3Uploader  = $this->get('app.service.s3_uploader');

        //$result = $awsS3Uploader->getDocumentFromPrivateBucket($document->getDocumentFileName());

        $result = $this->getDocumentFromPrivateBucket($document->getImageName());

        if ($result) {
            // Display the object in the browser
            header("Content-Type: {$result['ContentType']}");
            echo $result['Body'];

            return new Response();
        }

        return new Response('', 404);
    }


}
