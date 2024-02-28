<?php

namespace App\Controller\Admin;

use App\Repository\TagRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class TagController
{

    /**
     * Retrieve a list of tags that matches the query
     *
     * @param string $query
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function search(string $query)
    {
        $tags = TagRepository::searchByName($query);

        return new JsonResponse($tags);
    }


}
