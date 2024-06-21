<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserFixtures extends Fixture
{
    private const FIXTURE_FILE_SUFFIX = '_fixture';

    public function __construct(
        private readonly string $userDirectory,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $this->cleanProfileDirectory();

        $faker = Factory::create();

        $adminUser = new User();
        $adminUser->setUsername('admin');
        $adminUser->setEmail('admin@admin.com');
        $adminUser->setPassword($this->passwordHasher->hashPassword($adminUser, 'admin1234'));
        $adminUser->setRole(['ROLE_ADMIN']);
        $adminUser->setVerified(true);
        $adminUser->setUserPicture($this->fakeUploadImage('admin.jpeg'));
        $manager->persist($adminUser);

        $userTest = new User();
        $userTest->setUsername('utilisateur');
        $userTest->setEmail('user@user.com');
        $userTest->setPassword($this->passwordHasher->hashPassword($userTest, 'user1234'));
        $userTest->setRole(['ROLE_USER']);
        $userTest->setVerified(true);
        $userTest->setUserPicture($this->fakeUploadImage('user.jpeg'));
        $manager->persist($userTest);

        // Other users
        for ($i = 1; $i <= 9; $i++) {
            $user = new User();
            $user->setUsername($faker->userName());
            $user->setEmail($faker->unique()->email());
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $user->setRole(['ROLE_USER']);
            $user->setVerified(true);
            $user->setUserPicture($this->fakeUploadImage('image' . $i . '.jpeg'));
            $manager->persist($user);

            $this->addReference('user' . $i, $user);
        }

        $manager->flush();
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
