<?php

namespace App\Controller\ImageCache;

use App\Service\UrlImageCacheService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ImageCacheController extends AbstractController
{
    /**
     * Logo URL
     */
    const UVDESK_LOGO = 'https://updates.uvdesk.com/uvdesk-logo.png';

    protected $urlImageCacheService;
    protected $urlGenerator;

    public function __construct(
        UrlImageCacheService $urlImageCacheService
    ) {
        $this->urlImageCacheService = $urlImageCacheService;
    }

    /**
     * Get the cached image response
     * @return Response
     */
    public function getCachedImage(Request $request): Response
    {
        $response = $this->showImage(self::UVDESK_LOGO);

        // Get the file and its real path
        $file = $response->getFile();
        $filePath = $file->getRealPath();

        // Get the relative path by removing the kernel.project_dir
        $relativePath = str_replace($this->getParameter('kernel.project_dir') . '/public', '', $filePath);

        // Get the base URL, including the project name
        $basePath = '/' . ltrim($request->getBasePath(), '/');
        $siteUrl = rtrim($this->getParameter('uvdesk.site_url'), '/');

        // Construct the image URL by combining the base URL and the relative path
        $imageUrl = $siteUrl . $basePath . $relativePath;

        // Return the image URL as JSON
        return new Response(json_encode($imageUrl));
    }

    public function showImage(string $url): Response
    {
        $cachedImagePath = $this->urlImageCacheService->getCachedImage($url);

        return $this->file($cachedImagePath);
    }
}
