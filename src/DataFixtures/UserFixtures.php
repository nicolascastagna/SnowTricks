<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $adminUser = new User();
        $adminUser->setUsername('admin');
        $adminUser->setEmail('admin@admin.com');
        $adminUser->setPassword($this->passwordHasher->hashPassword($adminUser, 'admin1234'));
        $adminUser->setRole(['ROLE_ADMIN']);
        $adminUser->setVerified(true);
        $adminUser->setUserPicture($faker->imageUrl());
        $manager->persist($adminUser);

        $userTest = new User();
        $userTest->setUsername('utilisateur');
        $userTest->setEmail('user@user.com');
        $userTest->setPassword($this->passwordHasher->hashPassword($userTest, 'user1234'));
        $userTest->setRole(['ROLE_USER']);
        $userTest->setVerified(true);
        $userTest->setUserPicture($faker->imageUrl());
        $manager->persist($userTest);

        // Other users
        for ($i = 1; $i <= 9; $i++) {
            $user = new User();
            $user->setUsername($faker->userName());
            $user->setEmail($faker->unique()->email());
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $user->setRole(['ROLE_USER']);
            $user->setVerified(true);
            $user->setUserPicture($faker->imageUrl());
            $manager->persist($user);

            $this->addReference('user' . $i, $user);
        }

        $manager->flush();
    }
}
