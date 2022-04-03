<?php

namespace App\Controller;

use App\Entity\Book;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class BooksController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
       $this->security = $security;
    }

    #[Route('/api/books', methods: 'GET')]
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_1');
        
        $repository = $doctrine->getRepository(Book::class);

        $serializer = new Serializer([new ObjectNormalizer()]);

        return new JsonResponse($serializer->normalize($repository->findAll(), null, [
            ObjectNormalizer::ENABLE_MAX_DEPTH => true,
            ObjectNormalizer::ATTRIBUTES => ['id', 'name', 'author'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            },
        ]));
    }

    #[Route('/api/book/{id<\d+>}', name: 'app_books')]
    public function show(ManagerRegistry $doctrine, $id): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_1');
        
        $repository = $doctrine->getRepository(Book::class);
        
        $serializer = new Serializer([new ObjectNormalizer()]);
        
        return new JsonResponse($serializer->normalize($repository->find($id), null, [
            ObjectNormalizer::ENABLE_MAX_DEPTH => true,
            ObjectNormalizer::ATTRIBUTES => ['id', 'name', 'author'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            },
        ]));
    }
}
