<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\ORM\Doctrine\Populator;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

abstract class BaseFixtures extends Fixture
{

    private $locale = 'nl_NL';
    private $repeatData = true;

    protected $manager;
    protected $generator;
    protected $populator;
    protected $passwordEncoder;

    public function __construct(ObjectManager $manager, UserPasswordEncoderInterface $passwordEncoder)
    {

        $this->manager = $manager;

        $this->passwordEncoder = $passwordEncoder;

        $this->generator = \Faker\Factory::create($this->locale);
        if ($this->repeatData){
            $this->generator->seed(1234);
        }
        $this->populator = new Populator($this->generator, $manager);

    }
    
}  