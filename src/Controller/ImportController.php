<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Request;

use App\Entity\Center;

use App\Entity\Insurance;
use App\Entity\Source;
use App\Entity\Place;

use App\Entity\Type;
use App\Entity\Treatment;


/**
 *
 * @Route("/super")
 * @Security("is_granted('ROLE_SUPER_ADMIN')")
 *
 */
class ImportController extends AbstractController
{
    /**
     * @Route("/import-data-for-center/{id}", name="import")
     */
    public function index(Request $request, Center $center)
    {

        $items = null;

        $action = $request->get('action');

        if($center->getId() == 2){ // solo para el centro 2

            $conn = new \PDO('mysql:host=localhost;dbname=a01_kam', 'admin', 'pass');

            if($action == 'insurances'){

                $sql = '
                    SELECT id, cia, UZOVI FROM seguro ORDER BY id
                ';
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $items = $stmt->fetchAll();

                foreach($items as $key => $item) {

                    $ins = new Insurance;
                    $ins->setName($item['cia']);
                    $ins->setCode($item['UZOVI']);
                    $ins->setCenter($center);
                    $ins->setEnabled(true);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($ins);

                }

                $em->flush();

                //var_dump($seguros);die;
            }

            if($action == 'sources'){

                $sql = '
                    SELECT id, cia, tel, contact, notas, email, seleccionar 
                    FROM partner ORDER BY id
                ';
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $items = $stmt->fetchAll();

                foreach($items as $key => $item) {

                    $sou = new Source;
                    $sou->setName($item['cia']);
                    $sou->setTel($item['tel']);
                    $sou->setContact($item['contact']);
                    $sou->setNotes($item['notas']);
                    $sou->setEmail($item['email']);

                    $sou->setCenter($center);

                    $sou->setEnabled(false);
                    if($item['seleccionar']){
                        $sou->setEnabled(true);
                    }

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($sou);

                }
                


                if(0) {

                    $conn = $this->getDoctrine()->getManager()->getConnection();
                    $sql = "
                    INSERT INTO sources (name, enabled) VALUES 
                    ('RBK'),
                    ('RBK-VZ'),
                    ('RBK-SPANJE')
                    ";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();

                }

                $em->flush();

            }

            if($action == 'places'){

                $sql = '
                SELECT id, place, place AS cia FROM opera_loc ORDER BY id
                ';
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $items = $stmt->fetchAll();

                foreach($items as $key => $item) {

                    $pla = new Place;
                    $pla->setName($item['place']);
                    $pla->setCenter($center);
                    $pla->setEnabled(true);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($pla);
                }

                $em->flush();
            }

            if($action == 'types'){

                $sql = '
                SELECT id, tipo, tipo AS cia FROM tratamiento_tipo ORDER BY id
                ';
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $items = $stmt->fetchAll();

                foreach($items as $key => $item) {

                    $typ = new Type;
                    $typ->setName($item['tipo']);
                    $typ->setCenter($center);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($typ);

                }

                $em->flush();                
            }

            if($action == 'treatments'){

                $sql = "
                    SELECT id, tipo_id, concepto, importe, notas, concepto as cia FROM tratamiento WHERE 1
                ";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $items = $stmt->fetchAll();

                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(Type::class);

                foreach($items as $key => $item) {

                    $typeId = intval($item['tipo_id']);

                    //var_dump($typeId);die;

                    $type = $repository->find($typeId);

                    $tra = new Treatment;

                    $tra->setEnabled(true);
                    $tra->setType($type);
                    $tra->setName($item['concepto']);
                    $tra->setValue($item['importe']);
                    $tra->setNotes($item['notas']);

                    $em->persist($tra);                

                }

                $em->flush();          

            }

        }

        return $this->render('import/index.html.twig', [
            'id' => $center->getId(),
            'items' => $items,
        ]);
    }
}
