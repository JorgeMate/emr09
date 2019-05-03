<?php

namespace App\DataFixtures;

//use Doctrine\Bundle\FixturesBundle\Fixture;

use App\Entity\Center;
use App\Entity\User;
use App\Entity\Tag;



class UserFixtures extends BaseFixtures
{

    private $seedDb = 1;

    
    private $centersNumber = 4;
    private $usersNumber = 10;
    private $patientsNumber = 500;

    private $consultsNumber = 2000;
    private $historiasNumber = 1000;


    private $insurancesNumber = 80;
    private $sourcesNumber = 25;
    private $placesNumber = 20;


    private $typesNumber = 30;
    private $treatmentsNumber = 300;

    private $operasNumber = 1500;

    private $passUniversal = '4444';
    private $passUniversalEncoded;


    private function loadSuper(){

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



    public function load($loadSuper = 1)
    {
        if($loadSuper) {
            $this->loadSuper($loadSuper);
        }

        $password_requested_at = null;
        $confirmation_token = null;
        
        if($this->seedDb){

            $generator = $this->generator;

            $this->populator->addEntity('App:Center', $this->centersNumber,    
                array(
                'name' => function() use ($generator) { return $this->generator->company(); },
                'slug' => function() use ($generator) { return $this->generator->slug(); },
                'contactPerson' => function() use ($generator) { return $this->generator->name(); },
                'address' => function() use ($generator) { return $this->generator->streetName(); },
                'tel' => function() use ($generator) { return $this->generator->phoneNumber(); },
                )
            );

            $this->populator->addEntity('App:User', $this->usersNumber,
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

            $this->populator->addEntity('App:Insurance', $this->insurancesNumber,
                array(
                    'name' => function() use ($generator) { return $this->generator->company(); },
                    'tel' => function() use ($generator) { return $this->generator->phoneNumber(); },
                    'fax' => function() use ($generator) { return $this->generator->phoneNumber(); },
                    'contact' => function() use ($generator) { return $this->generator->name(); },
                    
                )
            );

            $this->populator->addEntity('App:Source', $this->sourcesNumber,
                array(
                    'name' => function() use ($generator) { return $this->generator->company(); },
                    'tel' => function() use ($generator) { return $this->generator->phoneNumber(); },
                    'fax' => function() use ($generator) { return $this->generator->phoneNumber(); },
                    'contact' => function() use ($generator) { return $this->generator->name(); },
                    
                )
            );

            $this->populator->addEntity('App:Place', $this->placesNumber,
                array(
                    'name' => function() use ($generator) { return $this->generator->company(); },
                    'tel' => function() use ($generator) { return $this->generator->phoneNumber(); },
                    'fax' => function() use ($generator) { return $this->generator->phoneNumber(); },
                    'contact' => function() use ($generator) { return $this->generator->name(); },
                    
                )
            );



            $this->populator->addEntity('App:Patient', $this->patientsNumber,
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

            $this->populator->addEntity('App:Consult', $this->consultsNumber,
                array(
                    'consult' => function() use ($generator) {return $this->generator->text($maxNbChars = 150); },
                    'treatment' => function() use ($generator) {return $this->generator->text($maxNbChars = 100); },
                    'notes' => function() use ($generator) {return $this->generator->paragraph($nbSentences = 2, $variableNbSentences = true); },
                )
            );

            $this->populator->addEntity('App:Historia', $this->historiasNumber,
                array(
                    
                    'problem' => function() use ($generator) {return $this->generator->text($maxNbChars = 100); },
                    'notes' => function() use ($generator) {return $this->generator->paragraph($nbSentences = 1, $variableNbSentences = true); },
                )
            );

            $this->populator->addEntity('App:Type', $this->typesNumber,
                array(            
                    'name' => function() use ($generator) {return $this->generator->bothify('Treatment Type ??##'); },
                )
            );
    
            $this->populator->addEntity('App:Treatment', $this->treatmentsNumber,
                array(            
                    'name' => function() use ($generator) {
                        $name = $this->generator->sentence($nbWords = 3, $variableNbWords = true);
                        return $name;
                    },
                    'value' => function() use ($generator) {return $this->generator->randomFloat($nbMaxDecimals = 2, $min = 40, $max = 5000); },
                )
            );

            $this->populator->addEntity('App:Opera', $this->operasNumber,
                array(            
                    'made_at' => null,
                )
            );

            $insertedPKs = $this->populator->execute();
            
            if (!$loadSuper) { // Entrando desde la aplicación, tareas especiales
                var_dump($insertedPKs);die; 
            }


            $tag = new Tag();
            $tag->setName('FGM');
            $this->manager->persist($tag);
            $tag = new Tag();
            $tag->setName('KLACHTEN');
            $this->manager->persist($tag);
            $tag = new Tag();
            $tag->setName('Free RE-OP');
            $this->manager->persist($tag);
            $tag = new Tag();
            $tag->setName('Stetisch');
            $this->manager->persist($tag);

            $this->manager->flush();

        }
    }
}
