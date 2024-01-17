<?php

namespace App\Controller\Admin;

use App\Repository\TagRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class TagController
{

    public function search(string $query)
    {
        $tags = TagRepository::searchByName($query);

        return new JsonResponse($tags);
    }
}
