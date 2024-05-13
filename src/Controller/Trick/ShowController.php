<?php

namespace App\Controller\Trick;

use App\Entity\Trick;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ShowController extends AbstractController
{

    public function __construct(private readonly TrickRepository $trickRepository)
    {
    }

    #[Route('/trick/{id}/{slug}', name: 'app_trick_show', methods: [Request::METHOD_GET])]
    /**
     * show
     *
     * @param  Trick $trick
     * @return Response
     */
    public function show(Trick $trick): Response
    {
        $slug = $trick->getSlug();

        if (!$trick) {
            throw $this->createNotFoundException('Le trick n\'a pas été trouvé.');
        }

        $category = $trick->getCategory();
        $pictures = $trick->getPictures();
        $videos = $trick->getVideos();
        $user = $trick->getUser()->getUsername();

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'slug' => $slug,
            'category' => $category,
            'pictures' => $pictures,
            'videos' => $videos,
            'user' => $user,

        ]);
    }
}
