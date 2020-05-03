<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use \DateTime;
use Faker;
use App\Entity\User;

class UserFixtures extends Fixture
{
    public const USER = 'user';

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();
        for ($i = 0; $i < 5; $i++) {   // Nombre d'utilisateur
            $user = new User();
            $user->setUsername($faker->name);
            $user->setEmail($faker->email);
            $user->setCreatedAt($faker->dateTime($max = 'now', $timezone = null));
            $user->setPassword($faker->text($maxNbChars = 20));
            $this->addReference(self::USER.$i,$user);
            $manager->persist($user);
        }
        $manager->flush();
    }
}
