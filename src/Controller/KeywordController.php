<?php

namespace App\Controller;

use App\Keyword\KeywordGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class KeywordController extends AbstractController
{
    public function __construct(
        private readonly KeywordGenerator $generator,
    ) {}

    #[Route('/', name: 'keyword_generate', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $input = '';
        $keywords = [];

        if ($request->isMethod('POST')) {
            $input = (string) $request->request->get('input', '');
            $keywords = $this->generator->generate($input);
        }

        return $this->render('keyword/index.html.twig', [
            'input' => $input,
            'keywords' => $keywords,
        ]);
    }
}
