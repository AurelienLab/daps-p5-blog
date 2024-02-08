<?php

namespace App\Controller;

use App\Core\Abstracts\AbstractController;
use App\Model\Comment;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Validator\NotEmptyValidator;
use Symfony\Component\HttpFoundation\Request;

class CommentController extends AbstractController
{

    public function postComment(Request $request, int $postId)
    {
        $post = PostRepository::getOrError($postId);

        $data = $this->validateForm($request, 'comment_form', [
            'content' => [NotEmptyValidator::class]
        ]);

        $comment = new Comment();

        $comment
            ->setUser($this->getUser())
            ->setPost($post)
            ->setContent($data->get('content'));

        CommentRepository::save($comment);

        return $this->redirect('articles.show', ['slug' => $post->getSlug()]);
    }
}
