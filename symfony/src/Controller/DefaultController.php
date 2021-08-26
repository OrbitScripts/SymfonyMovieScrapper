<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Form\MovieType;
use App\Service\MovieService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    private MovieService $movieService;

    /**
     * DefaultController constructor.
     * @param MovieService $movieService
     */
    public function __construct(MovieService $movieService)
    {
        $this->movieService = $movieService;
    }

    /**
     * @return JsonResponse
     *
     * @Route("/index", name="main_page", methods={"GET", "POST"})
     */
    public function index(Request $request): Response
    {
        $movie = new Movie();

        $form = $this->createForm(MovieType::class, $movie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Movie $movie */
            $movie = $form->getData();
            $movieLinks = $this->movieService->getMovieLinks($movie->getLink());
            return new JsonResponse($movieLinks);
        }

        return $this->render('main/index.html.twig', [
            'form' => $form->createView(),
        ]);

    }

}