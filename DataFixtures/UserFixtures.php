<?php

namespace App\DataFixtures;

//use Doctrine\Bundle\FixturesBundle\Fixture;

use App\Entity\Center;
use App\Entity\User;
//use App\Entity\Tag;

use Doctrine\ORM\EntityManagerInterface;



class UserFixtures extends BaseFixtures
{

    private $loadSuperUser = 1;
    private $loadCenterProbe = 1;
    /////////////////////////////
    private $seedDb = 0;

    private $centersNumber = 4
    ;

    private $usersxCenter = 3;
    private $insurancesxCenter = 20;
    private $sourcesxCenter = 5;
    private $placesxCenter = 4;

    private $typesxCenter = 8;
    private $treatmentsxType = 10;

    private $patientsxUser = 500;

    private $consultsxPatient = 5;
    private $historiasxPatient = 3;
    private $operasxPatient = 3;

    private $passUniversal = '4444';
    private $passUniversalEncoded;

  


    ///////////////////////////////
    public function load($load = 1)
    {

        if($this->loadSuperUser) {
            $this->loadSuper();
        } else { // necesitamos un pass universal para todos los faked users
            $user = new User;
            $this->passUniversalEncoded = $this->passwordEncoder->encodePassword(
                $user,
                $this->passUniversal
            );
        }

        if($this->loadCenterProbe) {
            $this->loadCenter();
        }        
        
        if($this->seedDb){

            if ($this->centersNumber){
                $this->seedCenters($this->centersNumber);
            }
        }
    }




    ////////////////////////////
    private function loadSuper()
    {

        $center = new Center();
        $center->setName('Kimberly Systems SLU');
        $center->setContactPerson('Jorge Maté Martínez');
        $center->setTel('(+34) 636 831 823');
        $center->setEmail('kimberly.systems@gmail.com');
        $center->setAddress('Piteres 2 1-C');
        $center->setPostcode('E-03590');
        $center->setCity('Altea');
        $center->setEnabled(true);
        
        $this->manager->persist($center);

        $user = new User();
        $user->setCenter($center);
        $user->setCenterUser(true);
        $user->setFirstName('Jorge');
        $user->setLastName('Maté');
        $user->setEmail('jorgematemartinez@gmail.com');
        $user->setTel('(+34) 636 831 823');

        $this->passUniversalEncoded = $this->passwordEncoder->encodePassword(
            $user,
            $this->passUniversal
        );

        $user->setPassword($this->passUniversalEncoded);
        $user->setEnabled(true);

        $roles[] = 'ROLE_SUPER_ADMIN';
        $user->setRoles($roles);
        
        $this->manager->persist($user);
        $this->manager->flush();
    }

    /////////////////////////////
    private function loadCenter()
    {

        $center = new Center();

        $center->setName('Karimed bv');
        $center->setContactPerson('R.B. Karim');
        $center->setTel('');
        $center->setEmail('rbkarim@gmail.com');
        $center->setAddress('');
        $center->setPostcode('');
        $center->setCity('Amstelveen');
        $center->setEnabled(true);
        
        $this->manager->persist($center);

        $user = new User();
        $user->setCenter($center);
        $user->setCenterUser(true);
        $user->setFirstName('R.B.');
        $user->setLastName('Karim');
        $user->setEmail('rbkarim@gmail.com');
        $user->setTel('');

        $this->passUniversalEncoded = $this->passwordEncoder->encodePassword(
            $user,
            $this->passUniversal
        );

        $user->setPassword($this->passUniversalEncoded);
        $user->setEnabled(true);

        $roles[] = 'ROLE_ADMIN';
        $user->setRoles($roles);
        
        $this->manager->persist($user);
        $this->manager->flush();


    }

    private function seedCenters()
    {
        $generator = $this->generator;

        for($x = 1; $x <= $this->centersNumber; $x++) {

            $this->populator->addEntity('App:Center', 1,    
                array(
                'name' => function() use ($generator) { return $this->generator->company(); },
                'slug' => function() use ($generator) { return $this->generator->slug(); },
                'contactPerson' => function() use ($generator) { return $this->generator->name(); },
                'address' => function() use ($generator) { return $this->generator->streetName(); },
                'tel' => function() use ($generator) { return $this->generator->phoneNumber(); },
                )
            );

            //$insertedPKs = $this->populator->execute();

            if($this->usersxCenter){
                $this->seedUsers();
            } else {
                $insertedPKs = $this->populator->execute();
            }

            if($this->treatmentsxType){
                $this->seedTypes();
            }

        }
    }

    private function seedUsers()
    {

        

        $password_requested_at = null;
        $confirmation_token = null;

        $generator = $this->generator;
        $this->populator->addEntity('App:User', $this->usersxCenter,
            array(
            'password' => $this->passUniversalEncoded,
            'tel' => function() use ($generator) { return $this->generator->phoneNumber(); },
            'passwordRequestedAt' => $password_requested_at,
            'confirmationToken' => $confirmation_token,
            'cod' => function() use ($generator) { return strtoupper($this->generator->lexify('???')); },
            'title' => function() use ($generator) { return $this->generator->randomElement($array = array ('Dr.','Dra.','Drs.')); },
            'tax_code' => function() use ($generator) { return $this->generator->bothify('??-########'); },
            'reg_code1' => function() use ($generator) { return $this->generator->bothify('?/########??'); },
            'reg_code2' => function() use ($generator) { return $this->generator->bothify('#? ####### ##'); },
            'speciality' => function() use ($generator) { return strtoupper($this->generator->lexify(' ?????????')); },
            )
        );

        if($this->insurancesxCenter){
            $this->seedInsurances();
        }

        if($this->sourcesxCenter){
            $this->seedSources();
        }

        if($this->placesxCenter){
            $this->seedPlaces();
        }

        if($this->typesxCenter){
            $this->seedTypes();
        }

        if($this->treatmentsxType){
            $this->seedTreatments();
        }

        if($this->patientsxUser){
            $this->seedPatients();
        }

        if($this->consultsxPatient){
            $this->seedConsults();
        }

        if($this->historiasxPatient){
            $this->seedHistorias();            
        }

        if($this->operasxPatient){
            $this->seedOperas();
        }
        

        $insertedPKs = $this->populator->execute();

    }

    private function seedInsurances()
    {
        $generator = $this->generator;
        $this->populator->addEntity('App:Insurance',$this->insurancesxCenter,
        array(
            'name' => function() use ($generator) { return $this->generator->company(); },
            'tel' => function() use ($generator) { return $this->generator->phoneNumber(); },
            'fax' => function() use ($generator) { return $this->generator->phoneNumber(); },
            'contact' => function() use ($generator) { return $this->generator->name(); },
            )
            
        );        

    }

    private function seedSources()
    {
        $generator = $this->generator;
        $this->populator->addEntity('App:Source', $this->sourcesxCenter,
            array(
                'name' => function() use ($generator) { return $this->generator->company(); },
                'tel' => function() use ($generator) { return $this->generator->phoneNumber(); },
                'fax' => function() use ($generator) { return $this->generator->phoneNumber(); },
                'contact' => function() use ($generator) { return $this->generator->name(); },
            )
        );        

    }

    private function seedPlaces()
    {

        $generator = $this->generator;
        $this->populator->addEntity('App:Place', $this->placesxCenter,
            array(
                'name' => function() use ($generator) { return $this->generator->company(); },
                'tel' => function() use ($generator) { return $this->generator->phoneNumber(); },
                'fax' => function() use ($generator) { return $this->generator->phoneNumber(); },
                'contact' => function() use ($generator) { return $this->generator->name(); },
                
            )
        );        
        
    }

    private function seedTypes()
    {
        $generator = $this->generator;
        $this->populator->addEntity('App:Type', $this->typesxCenter,
            array(            
                'name' => function() use ($generator) {return $this->generator->bothify('Treatment Type ??##'); },
            )
        );

    }

    private function seedTreatments()
    {
        $generator = $this->generator;
        $this->populator->addEntity('App:Treatment', $this->treatmentsxType * $this->typesxCenter,
            array(            
                'name' => function() use ($generator) {
                    $name = $this->generator->sentence($nbWords = 3, $variableNbWords = true);
                    return $name;
                },
                'value' => function() use ($generator) {return $this->generator->randomFloat($nbMaxDecimals = 2, $min = 40, $max = 5000); },
            )
        );

    }

    private function seedPatients()
    {
        $generator = $this->generator;
        $this->populator->addEntity('App:Patient', $this->patientsxUser,
            array(
                'tel' => function() use ($generator) { return $this->generator->phoneNumber(); },
                'cel' => function() use ($generator) { return $this->generator->phoneNumber(); },
                'address' => function() use ($generator) { return $this->generator->streetName(); },
                'house_no' => function() use ($generator) { return $this->generator->numberBetween($min = 1, $max = 300); },
                'house_txt' => function() use ($generator) { return $this->generator->randomLetter(); },
                'contact' => function() use ($generator) { return $this->generator->name(); },
                'tel_con' => function() use ($generator) { return $this->generator->phoneNumber(); },
                'doctor' => function() use ($generator) { return $this->generator->name(); },
                'tel_doc' => function() use ($generator) { return $this->generator->phoneNumber(); },
            )
        );
    }

    private function seedConsults()
    {
        $generator = $this->generator;
        $this->populator->addEntity('App:Consult', $this->consultsxPatient * $this->patientsxUser,
            array(
                'consult' => function() use ($generator) {return $this->generator->text($maxNbChars = 150); },
                'treatment' => function() use ($generator) {return $this->generator->text($maxNbChars = 100); },
                'notes' => function() use ($generator) {return $this->generator->paragraph($nbSentences = 2, $variableNbSentences = true); },
            )
        );

    }

    private function seedHistorias()
    {
        $generator = $this->generator;
        $this->populator->addEntity('App:Historia', $this->historiasxPatient * $this->patientsxUser,
        array(
                'problem' => function() use ($generator) {return $this->generator->text($maxNbChars = 100); },
                'notes' => function() use ($generator) {return $this->generator->paragraph($nbSentences = 1, $variableNbSentences = true); },
            )
        );



    }

    private function seedOperas()
    {
        $this->populator->addEntity('App:Opera', $this->operasxPatient * $this->patientsxUser,
            array(            
                'made_at' => null,
                'value' => 0,
            )
        );
    }


}
