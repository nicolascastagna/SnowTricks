<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Picture;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TrickFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $trickMainImages = [
            '',
        ];

        // $trickImages = [
        //     '',
        // ];

        // $videoUrls = [
        //     'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        //     'https://www.dailymotion.com/video/x26bs2h',
        // ];

        $users = $manager->getRepository(User::class)->findAll();
        $categories = $manager->getRepository(Category::class)->findAll();

        for ($i = 0; $i < 14; $i++) {
            $trick = new Trick();
            $trick->setName($faker->words(3, true));
            $trick->setDescription($faker->paragraph());
            $trick->setCreationDate($faker->dateTimeBetween('-1 year', 'now'));

            $trick->setUser($faker->randomElement($users));
            $trick->setCategory($faker->randomElement($categories));

            $mainImage = $faker->randomElement($trickMainImages);
            $trick->setMainImage($mainImage);

            // for ($j = 0; $j < $faker->numberBetween(1, 3); $j++) {
            //     $picture = new Picture();
            //     $picture->setName($faker->randomElement($trickImages));
            //     $trick->addPicture($picture);
            // }

            // for ($j = 0; $j < $faker->numberBetween(1, 2); $j++) {
            //     $video = new Video();
            //     $video->setName($faker->randomElement($videoUrls));
            //     $trick->addVideo($video);
            // }

            $manager->persist($trick);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            CategoryFixtures::class,
        ];
    }
}
