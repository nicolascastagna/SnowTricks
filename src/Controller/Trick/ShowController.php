<?php

namespace App\Controller\Trick;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\CommentFormType;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ShowController extends AbstractController
{
    public function __construct(
        private readonly TrickRepository $trickRepository,
        private readonly CommentRepository $commentRepository,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/trick/{id}/{slug}', name: 'app_trick_show', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    /**
     * show
     *
     * @param  Request $request
     * @param  Trick $trick
     * @return Response
     */
    public function show(Request $request, Trick $trick): Response
    {
        $slug = $trick->getSlug();

        if (!$trick) {
            throw $this->createNotFoundException('Le trick n\'a pas été trouvé.');
        }

        $category = $trick->getCategory();
        $pictures = $trick->getPictures();
        $videos = $trick->getVideos();
        $user = $trick->getUser()->getUsername();

        $page = $request->query->getInt('page', 1);
        $limit = 10;

        $comments = $this->commentRepository->findPaginatedComments($trick->getId(), $page, $limit);
        $totalComments = $this->commentRepository->countComments($trick->getId());

        $comment = new Comment();
        $commentForm = $this->createForm(CommentFormType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setTrick($trick);
            $comment->setUser($this->getUser());
            $comment->setCommentDate(new \DateTime());

            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre commentaire a bien été publié !');

            return $this->redirectToRoute('app_trick_show', [
                '_fragment' => 'comment-form',
                'id' => $trick->getId(),
                'slug' => $trick->getSlug(),
            ]);
        }

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'slug' => $slug,
            'category' => $category,
            'pictures' => $pictures,
            'videos' => $videos,
            'user' => $user,
            'comments' => $comments,
            'totalComments' => $totalComments,
            'limit' => $limit,
            'currentPage' => $page,
            'commentForm' => $commentForm->createView(),
        ]);
    }
}
