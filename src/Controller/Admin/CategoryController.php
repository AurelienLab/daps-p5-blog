<?php

namespace App\Controller\Admin;

use App\Core\Abstracts\AbstractController;
use App\Repository\PostCategoryRepository;

class CategoryController extends AbstractController
{

    public function index()
    {
        $categories = PostCategoryRepository::getAll();

        return $this->render('Admin/category/index.html.twig', [
            'categories' => $categories
        ]);
    }
}
