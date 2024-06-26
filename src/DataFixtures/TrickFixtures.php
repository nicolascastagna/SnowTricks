<?php

namespace App\DataFixtures;

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
        private readonly string $tricksDirectory,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $this->cleanDirectory();

        $trickMainImages = [
            'trick1.jpeg',
            'trick2.jpeg',
            'trick3.jpeg',
            'trick4.jpeg',
            'trick5.jpeg',
            'trick6.jpeg',
            'trick7.jpeg',
            'trick8.jpeg',
            'trick9.jpeg',
            'trick10.jpeg',
            'trick11.jpeg',
        ];

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

        $users = [];

        // admin user
        $adminUser = new User();
        $adminUser->setUsername('admin')
            ->setEmail('admin@admin.com')
            ->setPassword($this->passwordHasher->hashPassword($adminUser, 'admin1234'))
            ->setRole(['ROLE_ADMIN'])
            ->setVerified(true)
            ->setUserPicture($this->fakeUploadImage('admin.jpeg', $this->userDirectory));
        $manager->persist($adminUser);
        $users[] = $adminUser;

        // user
        $userTest = new User();
        $userTest->setUsername('utilisateur')
            ->setEmail('user@user.com')
            ->setPassword($this->passwordHasher->hashPassword($userTest, 'user1234'))
            ->setRole(['ROLE_USER'])
            ->setVerified(true)
            ->setUserPicture($this->fakeUploadImage('user.jpeg', $this->userDirectory));
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
                ->setUserPicture($this->fakeUploadImage('image' . $i . '.jpeg', $this->userDirectory));

            $manager->persist($user);
            $users[] = $user;
        }

        // 15 tricks
        for ($i = 0; $i < 14; $i++) {
            $trick = new Trick();
            $trick->setName($faker->words(3, true))
                ->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Diam ut venenatis tellus in metus vulputate eu scelerisque felis. Ultrices gravida dictum fusce ut placerat. Sit amet facilisis magna etiam tempor orci. Diam phasellus vestibulum lorem sed risus ultricies tristique nulla. Quam adipiscing vitae proin sagittis nisl rhoncus mattis rhoncus urna. Mattis enim ut tellus elementum. Proin gravida hendrerit lectus a. Lacus suspendisse faucibus interdum posuere lorem ipsum dolor sit amet. Varius sit amet mattis vulputate. Convallis convallis tellus id interdum velit laoreet id donec. Mattis enim ut tellus elementum sagittis vitae et. Leo vel fringilla est ullamcorper eget. Feugiat nibh sed pulvinar proin gravida hendrerit lectus. Proin fermentum leo vel orci porta non.')
                ->setCreationDate($faker->dateTimeBetween('-1 year', 'now'));

            $trick->setUser($faker->randomElement($users));
            $trick->setCategory($faker->randomElement($categories));

            $randomMainImage = $faker->randomElement($trickMainImages);

            $trick->setMainImage($this->fakeUploadImage($randomMainImage, $this->tricksDirectory));

            $usedPictures = [];
            for ($j = 0; $j < $faker->numberBetween(1, 3); $j++) {
                do {
                    $randomPicture = $faker->randomElement($trickMainImages);
                } while (in_array($randomPicture, $usedPictures));

                $usedPictures[] = $randomPicture;

                $picture = new Picture();
                $picture->setName($this->fakeUploadImage($randomPicture, $this->tricksDirectory));
                $trick->addPicture($picture);
            }

            for ($j = 0; $j < $faker->numberBetween(1, 2); $j++) {
                $video = new Video();
                $video->setName($faker->randomElement($videoUrls));
                $trick->addVideo($video);
            }

            $manager->persist($trick);
        }

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
     * @param  string $targetDirectory
     * @return string
     */
    private function fakeUploadImage(string $imageFileName, string $targetDirectory): string
    {
        $fileSystem = new Filesystem();
        $sourcePath = __DIR__ . '/assets/' . ($targetDirectory === $this->tricksDirectory ? 'tricks/' : 'users/') . $imageFileName;
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

        return $this->uploadImage($uploadedFile, $targetDirectory);
    }

    /**
     * Uploads an image and returns the new file name
     *
     * @param UploadedFile $image   
     * @param string $targetDirectory
     * @return string
     */
    private function uploadImage(UploadedFile $image, string $targetDirectory): string
    {
        $fileName = md5(uniqid()) . self::FIXTURE_FILE_SUFFIX . '.' . $image->guessExtension();

        try {
            $image->move($targetDirectory, $fileName);
        } catch (FileException $e) {
            throw new HttpException('Une erreur est survenue lors de l\'upload de l\'image.', $e);
        }

        return $fileName;
    }

    /**
     * cleanDirectory
     *
     * @return void
     */
    private function cleanDirectory(): void
    {
        $fileSystem = new Filesystem();

        $this->removeFixtureFiles($this->userDirectory, $fileSystem);
        $this->removeFixtureFiles($this->tricksDirectory, $fileSystem);
    }

    /**
     * removeFixtureFiles
     * 
     * @param  string $directory
     * @param  Filesystem $fileSystem
     * @return void
     */
    private function removeFixtureFiles(string $directory, Filesystem $fileSystem): void
    {
        $files = glob($directory . '/*' . self::FIXTURE_FILE_SUFFIX . '*');
        foreach ($files as $file) {
            if (is_file($file)) {
                $fileSystem->remove($file);
            }
        }
    }
}
