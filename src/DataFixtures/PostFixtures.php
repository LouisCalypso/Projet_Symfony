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

            //we get a random image (of a good girl) from a local dir
            // we scan the dir and we remove the .. and . references
            // a random index is selected
            $directory = 'public/storage/uploads';
            $files = array_diff(scandir($directory), array('..', '.'));
            $random_index = array_rand($files);
            $path = "storage/uploads/" . $files[$random_index];
            $post->setImage($path);


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
