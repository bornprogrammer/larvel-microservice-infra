<?php

namespace Laravel\Infrastructure\Services;

use Illuminate\Http\UploadedFile;
use Laravel\Infrastructure\Exceptions\InternalServerErrorException;

class ImageProcessorService extends BaseService
{
    /**
     * @throws \ImagickException
     * @throws InternalServerErrorException
     */
    public function convertFromHEICToJPEG(string $fileName)
    {
        if (class_exists("Imagick")) {
            $im = new \Imagick();
            $publicPath = storage_path('app/public') . "/" . $fileName;
            $imageSize = $this->getImageSize($publicPath);
            $im->setSize($imageSize[0], $imageSize[0]);
            $im->setFormat('heic');
            $im->readImage($publicPath);
            $im->cropThumbnailImage($imageSize[0], $imageSize[0]);
            $im->setImageCompressionQuality(80);
            $heicImageName = explode(".", $publicPath);
            array_pop($heicImageName);
            $heicImageName = implode("", $heicImageName) . ".jpeg";
            $im->writeImage($heicImageName);
            $im->destroy();
        } else {
            throw new InternalServerErrorException("Imagick extension not found,Please install it");
        }
    }

    public function getImageSize(UploadedFile|string $imageObject): array
    {
        $imagePath = $imageObject instanceof UploadedFile ? $imageObject->getPathname() : $imageObject;
        $imageSize = getimagesize($imagePath);
        return $imageSize ? $imageSize : [600, 600];
    }
}
