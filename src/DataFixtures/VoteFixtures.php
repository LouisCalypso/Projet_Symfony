<?php

namespace App\DataFixtures;

use App\Entity\Vote;
use Faker;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class VoteFixtures
 * create fake votes for posts
 * @package App\DataFixtures
 */
class VoteFixtures extends Fixture implements DependentFixtureInterface
{
    public const POST = 'post';
    public const USER = 'user';

    /**
     * function load
     * create fake votes
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();
        for ($i = 0; $i < 5; $i++) {
            $post = $this->getReference(PostFixtures::POST.$i);
            for ($j = 0; $j < rand(1,5); $j++) {
                $user = $this->getReference(UserFixtures::USER.$j);
                $vote = new Vote();
                $bool = $faker->boolean;
                $vote->setType($bool);
                $vote->setUser($user);
                $vote->setPost($post);
                if($bool){
                    $post->incrementNbVotes();
                }else{
                    $post->decrementNbVotes();
                }
                
                $post->addVote($vote);
                $user->addVote($vote);

                $manager->persist($vote);
                $manager->persist($post);

            }
        }
        $manager->flush();
    }

    /**
     * function getDependencies
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
