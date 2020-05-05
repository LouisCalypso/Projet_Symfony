<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use \DateTime;
use Faker;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\DataFixtures\UserFixtures;

class PostFixtures extends Fixture implements DependentFixtureInterface
{
    public const POST = 'post';
    public const USER = 'user';


    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();
        for ($i = 0; $i < 2; $i++) {

            $post = new Post();
            $post->setTitle($faker->text($maxNbChars = 10));
            $post->setBody($faker->paragraph($nbSentences = 3));
            $post->setCreatedAt($faker->dateTime($max = 'now', $timezone = null));
            $post->setNbVotes(0);
            $this->addReference(self::POST.$i,$post);
            $user = $this->getReference(UserFixtures::USER.$i);
            $post->setUser($user);
            $user->addPost($post);
            $manager->persist($post);
        }
        for ($i = 2; $i < 4; $i++) {

            $post = new Post();
            $post->setTitle($faker->text($maxNbChars = 10));
            $post->setImage($faker->imageUrl($width = 400, $height = 400));
            $post->setCreatedAt($faker->dateTime($max = 'now', $timezone = null));
            $post->setNbVotes(0);
            $this->addReference(self::POST.$i,$post);
            $user = $this->getReference(UserFixtures::USER.$i);
            $post->setUser($user);
            $user->addPost($post);
            $manager->persist($post);
        }
        $post = new Post();
        $post->setTitle($faker->text($maxNbChars = 10));
        $post->setLink("www.reddit.com");
        $post->setCreatedAt($faker->dateTime($max = 'now', $timezone = null));
        $post->setNbVotes(0);
        $this->addReference(self::POST.$i,$post);
        $user = $this->getReference(UserFixtures::USER.$i);
        $post->setUser($user);
        $user->addPost($post);
        $manager->persist($post);

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
        );
    }
}
