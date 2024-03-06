<?php

namespace App\Controller\Admin;

use App\Core\Abstracts\AbstractController;
use App\Core\Utils\Str;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class EditorController extends AbstractController
{


    /**
     * Upload a file from EditorJS Image tool
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function uploadFile(Request $request): JsonResponse
    {
        $image = $request->files->get('image');

        if ($image === null) {
            return $this->generateError('Unable to find image in post data');
        }

        $filename = Str::rand(16);
        $filename .= '.'.$image->getClientOriginalExtension();
        $imagePath = '/'.$image->move(config('uploads.editor.image'), $filename);

        return new JsonResponse([
            'success' => 1,
            'file' => [
                'url' => $imagePath
            ]
        ]);
    }


    /**
     * Upload a file from image url paste in EditorJS
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function fetchUrl(Request $request): JsonResponse
    {
        $url = $request->getPayload()->get('url');
        $info = pathinfo($url);
        $contents = file_get_contents($url);

        $file = config('uploads.editor.image').'/'.Str::rand(16).'.'.$info['extension'];
        file_put_contents($file, $contents);

        return new JsonResponse([
            'success' => 1,
            'file' => [
                'url' => '/'.$file
            ]
        ]);
    }


    /**
     * Return error in json format
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    private function generateError(string $message)
    {
        return new JsonResponse([
            'success' => 0,
            'message' => $message,
            'file' => null
        ], 500);
    }


}
