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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TrickFixtures extends Fixture
{
    private const FIXTURE_FILE_SUFFIX = '_fixture';

    public function __construct(
        private readonly string $userDirectory,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $this->cleanProfileDirectory();

        $trickMainImages = [
            '',
        ];

        // $trickImages = [
        //     '',
        // ];

        $videoUrls = [
            'https://www.youtube.com/embeb/dQw4w9WgXcQ',
            'https://www.dailymotion.com/embed/video/x26bs2h',
            'https://www.dailymotion.com/embed/video/x26bs2h',
            'https://www.dailymotion.com/embed/video/x26bs2h',
            'https://www.dailymotion.com/embed/video/x26bs2h',
            'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'https://www.youtube.com/embeb/dQw4w9WgXcQ',
            'https://www.youtube.com/embeb/dQw4w9WgXcQ',
            'https://www.youtube.com/embeb/dQw4w9WgXcQ',
            'https://www.youtube.com/embeb/dQw4w9WgXcQ',
            'https://www.youtube.com/embeb/dQw4w9WgXcQ',
        ];

        $categories = [];
        for ($i = 0; $i < 10; ++$i) {
            $categories[] = $this->getReference('category_' . $i);
        }
        $category = $faker->randomElement($categories);

        $users = [];

        // admin user
        $adminUser = new User();
        $adminUser->setUsername('admin')
            ->setEmail('admin@admin.com')
            ->setPassword($this->passwordHasher->hashPassword($adminUser, 'admin1234'))
            ->setRole(['ROLE_ADMIN'])
            ->setVerified(true)
            ->setUserPicture($this->fakeUploadImage('admin.jpeg'));
        $manager->persist($adminUser);
        $users[] = $adminUser;

        // user
        $userTest = new User();
        $userTest->setUsername('utilisateur')
            ->setEmail('user@user.com')
            ->setPassword($this->passwordHasher->hashPassword($userTest, 'user1234'))
            ->setRole(['ROLE_USER'])
            ->setVerified(true)
            ->setUserPicture($this->fakeUploadImage('user.jpeg'));
        $manager->persist($userTest);
        $users[] = $userTest;

        // 10 users
        for ($i = 1; $i < 9; $i++) {
            $user = new User();
            $user->setUsername($faker->userName())
                ->setEmail($faker->unique()->email())
                ->setPassword($this->passwordHasher->hashPassword($user, 'password'))
                ->setRole(['ROLE_USER'])
                ->setVerified(true)
                ->setUserPicture($this->fakeUploadImage('image' . $i . '.jpeg'));

            $manager->persist($user);
            $users[] = $user;
        }

        // 15 tricks
        // for ($i = 0; $i < 14; $i++) {
        //     $trick = new Trick();
        //     $trick->setName($faker->words(3, true));
        //     $trick->setDescription($faker->paragraph());
        //     $trick->setCreationDate($faker->dateTimeBetween('-1 year', 'now'));

        //     $trick->setUser($faker->randomElement($user));
        //     $trick->setCategory($faker->randomElement($category));

        //     // $mainImage = $faker->randomElement($trickMainImages);
        //     // $trick->setMainImage($mainImage);

        //     // for ($j = 0; $j < $faker->numberBetween(1, 3); $j++) {
        //     //     $picture = new Picture();
        //     //     $picture->setName($faker->randomElement($trickImages));
        //     //     $trick->addPicture($picture);
        //     // }

        //     for ($j = 0; $j < $faker->numberBetween(1, 2); $j++) {
        //         $video = new Video();
        //         $video->setName($faker->randomElement($videoUrls));
        //         $trick->addVideo($video);
        //     }

        //     $manager->persist($trick);
        // }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoryFixtures::class
        ];
    }

    /**
     * fakeUploadImage
     *
     * @param  string $imageFileName
     * @return string
     */
    private function fakeUploadImage(string $imageFileName): string
    {
        $fileSystem = new Filesystem();
        $sourcePath = __DIR__ . '/assets/users/' . $imageFileName;
        $targetPath = sys_get_temp_dir() . '/' . $imageFileName;
        $fileSystem->copy($sourcePath, $targetPath, true);

        $file = new File($targetPath);
        $uploadedFile = new UploadedFile(
            $file->getPathname(),
            $file->getFilename(),
            $file->getMimeType(),
            null,
            true
        );

        return $this->uploadImage($uploadedFile);
    }

    /**
     * Uploads an image and returns the new file name
     *
     * @param UploadedFile $image   
     * @return string
     */
    private function uploadImage(UploadedFile $image): string
    {
        $fileName = md5(uniqid()) . self::FIXTURE_FILE_SUFFIX . '.' . $image->guessExtension();

        try {
            $image->move($this->userDirectory, $fileName);
        } catch (FileException $e) {
            throw new HttpException('Une erreur est survenue lors de l\'upload de l\'image.', $e);
        }

        return $fileName;
    }

    /**
     * cleanProfileDirectory
     *
     * @return void
     */
    private function cleanProfileDirectory(): void
    {
        $fileSystem = new Filesystem();
        $files = glob($this->userDirectory . '/*' . self::FIXTURE_FILE_SUFFIX . '*');

        foreach ($files as $file) {
            if (is_file($file)) {
                $fileSystem->remove($file);
            }
        }
    }
}
