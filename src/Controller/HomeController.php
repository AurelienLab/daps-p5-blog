<?php

namespace App\Controller;

use App\Core\Abstracts\AbstractController;
use App\Core\Config\Config;
use App\Core\Database\Database;
use App\Core\Database\Query;
use App\Model\Test;
use App\Repository\TestRepository;

class HomeController extends AbstractController
{


    public function index(): string
    {
        $results = TestRepository::getAll();

        $entity = $results[1];
        $entity->setTest('Vive la vie !');

        TestRepository::save($entity);
        dd(TestRepository::getAll());

        return $this->render(
            'homepage/index.html.twig',
            [
                'test' => ''
            ]
        );
    } // end index()


    public function test($identifier): string
    {
        return 'Hi '.$identifier;
    }
}
