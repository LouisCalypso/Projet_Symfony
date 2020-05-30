<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use \DateTime;
use Faker;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserFixtures
 * create fake users
 * @package App\DataFixtures
 */
class UserFixtures extends Fixture
{
    public const USER = 'user';

    /**
     * @var UserPasswordEncoderInterface encode fake password
     */
    private $encoder;

    /**
     * UserFixtures constructor.
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * function load
     * create fake users
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();
        for ($i = 0; $i < 5; $i++) {   // Nombre d'utilisateur
            $user = new User();
            $user->setUsername($faker->name);
            $user->setEmail($faker->email);
            $user->setCreatedAt($faker->dateTime($max = 'now', $timezone = null));
            $password = $this->encoder->encodePassword($user, 'pass_1234');
            $user->setPassword($password);
            $this->addReference(self::USER.$i,$user);
            $manager->persist($user);
        }
        $manager->flush();
    }
}
