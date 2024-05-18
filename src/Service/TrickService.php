<?php

namespace App\Service;

use App\Entity\Trick;
use App\Entity\Picture;
use App\Entity\Video;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TrickService
{
    public function __construct(
        private readonly string $tricksDirectory,
        private readonly CategoryRepository $categoryRepository
    ) {
    }

    /**
     * setCategory for a trick
     *
     * @param mixed $categoryData
     * @param Trick $trick
     * @return void
     */
    public function handleCategory($categoryData, Trick $trick): void
    {
        $category = $categoryData ?? $this->categoryRepository->findOneBy(['name' => 'Autres']);

        if ($category === null) {
            throw new \Exception('La catégorie "Autres" n\'a pas été trouvée.');
        }

        $trick->setCategory($category);
    }

    /**
     * Handles the main image for a trick
     *
     * @param UploadedFile|null $mainImage
     * @param array $images
     * @param Trick $trick
     * @return void
     */
    public function handleMainImage(?UploadedFile $mainImage, Trick $trick): void
    {
        if ($mainImage) {
            $fileName = $this->uploadImage($mainImage);
            $trick->setMainImage($fileName);
        } else {
            $trick->setMainImage('image-placeholder.jpg');
        }
    }

    /**
     * Handles additional images for a trick
     *
     * @param array $images
     * @param Trick $trick
     * @return void
     */
    public function handleImages(array $images, Trick $trick): void
    {
        foreach ($images as $image) {
            if ($image instanceof UploadedFile) {
                $fileName = $this->uploadImage($image);
                $picture = new Picture();
                $picture->setName($fileName);
                $trick->addPicture($picture);
            }
        }
    }

    /**
     * Handles additional videos for a trick
     *
     * @param array $videos
     * @param Trick $trick
     * @return void
     */
    public function handleVideos(array $videos, Trick $trick): void
    {
        foreach ($videos as $videoUrl) {
            $embedCode = $this->generateEmbedCode($videoUrl);
            if ($embedCode) {
                $video = new Video();
                $video->setName($embedCode);
                $video->setTrick($trick);
                $trick->addVideo($video);
            }
        }
    }

    /**
     * Generate embed code for YouTube or Dailymotion URL
     *
     * @param string $url
     * @return string|null
     */
    private function generateEmbedCode(string $url): ?string
    {
        if (strpos($url, 'youtube.com') !== false) {
            return $this->getYouTubeEmbedCode($url);
        } elseif (strpos($url, 'dailymotion.com') !== false) {
            return $this->getDailymotionEmbedCode($url);
        }
        return null;
    }

    /**
     * getYouTubeEmbedCode
     *
     * @param  string $url
     * @return string
     */
    private function getYouTubeEmbedCode(string $url): string
    {
        preg_match('/(youtube\.com\/(watch\?(.*&)?v=|(embed|v)\/))([^?&"\'<>]+)/', $url, $matches);
        return $matches ? 'https://www.youtube.com/embed/' . $matches[5] : '';
    }

    /**
     * getDailymotionEmbedCode
     *
     * @param  string $url
     * @return string
     */
    private function getDailymotionEmbedCode(string $url): string
    {
        preg_match('/(dailymotion\.com\/(video|hub)\/([^_]+))/', $url, $matches);
        return $matches ? 'https://www.dailymotion.com/embed/video/' . $matches[3] : '';
    }

    /**
     * Uploads an image and returns the new file name
     *
     * @param UploadedFile $image   
     * @return string
     */
    private function uploadImage(UploadedFile $image): string
    {
        $fileName = md5(uniqid()) . '.' . $image->guessExtension();

        try {
            $image->move($this->tricksDirectory, $fileName);
        } catch (FileException $e) {
            throw new HttpException('Une erreur est survenue lors de l\'upload de l\'image.', $e);
        }

        return $fileName;
    }
}
