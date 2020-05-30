<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use \DateTime;
use Faker;
use App\Entity\Comment;
use App\DataFixtures\UserFixtures;
use App\DataFixtures\PostFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class CommentFixtures
 * create fake comments
 * @package App\DataFixtures
 */
class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public const POST = 'post';
    public const USER = 'user';

    /**
     * function load
     * create and load fake comments
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();
        for ($i = 0; $i < 5; $i++) {
            for ($j = 0; $j < rand(1, 4); $j++) {
                $comment = new Comment();
                $comment->setCreatedAt($faker->dateTime($max = 'now', $timezone = null));
                $comment->setBody($faker->text($maxNbChars = 200));

                $post = $this->getReference(PostFixtures::POST.$i);
                $user = $this->getReference(UserFixtures::USER.$i);
                $comment->setUser($user);
                $comment->setPost($post);
                $post->addComment($comment);
                $user->addUserComment($comment);

                $manager->persist($comment);

            }
        }
        $manager->flush();
    }

    /**
     * function get dependencies
     * @return array
     */
    public function getDependencies()
    {
        return array(
            UserFixtures::class,
            PostFixtures::class,
        );
    }

}
