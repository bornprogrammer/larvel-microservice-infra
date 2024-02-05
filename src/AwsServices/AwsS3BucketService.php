<?php

namespace Laravel\Infrastructure\AwsServices;

use Illuminate\Support\Facades\Storage;
use JetBrains\PhpStorm\ArrayShape;
use Laravel\Infrastructure\Exceptions\DirectoryNotFoundException;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Laravel\Infrastructure\Exceptions\AwsException;
use Illuminate\Http\UploadedFile;
use Laravel\Infrastructure\Facades\ExceptionReporterServiceFacade;
use Laravel\Infrastructure\Facades\ImageProcessorServiceFacade;

class AwsS3BucketService extends BaseAWSService
{
    protected static $awsClientType = S3Client::class;

    protected function initiateSDK()
    {
        parent::initiateSDK();
        $this->baseUrl = $this->sdkConfiguration['baseUrl'];
    }
    /**
     * @throws AwsException
     * @throws DirectoryNotFoundException
     */
    #[ArrayShape(["fileName" => "string"])]
    public function upload(?UploadedFile $uploadedFile, string $bucketDirectory): ?string
    {
        if ($uploadedFile) {
            $fileName = $this->generateFileName($uploadedFile);
            $filePath = $uploadedFile->getPathname();
            if ($uploadedFile->getClientOriginalExtension() === "heic") {
                $fileName = $this->uploadHEICFromPublicFolderToS3($fileName, $uploadedFile, $bucketDirectory);
            } else {
                $this->uploadToS3($filePath, $fileName, $bucketDirectory, $uploadedFile->getClientMimeType());
            }
            return $fileName;
        }
        return null;
    }
    protected function uploadHEICFromPublicFolderToS3(string $fileName, ?UploadedFile $uploadedFile, string $bucketDirectory): ?string
    {
        $uploadedFile->storePubliclyAs("/public", $fileName);
        ImageProcessorServiceFacade::convertFromHEICToJPEG($fileName);
        $filePath = storage_path('app/public');
        $fileName = explode(".", $fileName);
        array_pop($fileName);
        $fileName = implode("", $fileName) . ".jpeg";
        $filePath = $filePath . "/" . $fileName;
        $this->uploadToS3($filePath, $fileName, $bucketDirectory, "image/jpeg");
        return $fileName;
    }
    protected function checkExistingDirectory(string $directory): void
    {
        $isDirectoryExists = Storage::disk('s3')->exists($directory);
        if (!$isDirectoryExists) {
            ExceptionReporterServiceFacade::report(throw new DirectoryNotFoundException());
        }
    }
    protected function generateFileName(UploadedFile $uploadedFile): string
    {
        $fileName = explode(".", $uploadedFile->getClientOriginalName());
        array_pop($fileName);
        $fileName = implode("", $fileName);
        $string = str_replace('', '-', $fileName); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '_', $string); // Removes special chars.
        $string = preg_replace('/_+/', '_', $string); // Replaces multiple hyphens with single one.
        return time() . '-' . $string . "." . $uploadedFile->getClientOriginalExtension();
    }
    public function uploadDownloadableExcelFileFromPublicFolder(string $fileName, string $bucketDirectory): ?string
    {
        return $this->uploadFileFromPublicFolder(
            $fileName,
            $bucketDirectory,
            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
        );
    }
    public function uploadDownloadablePDFFileFromPublicFolder(string $fileName, string $bucketDirectory): ?string
    {
        return $this->uploadFileFromPublicFolder(
            $fileName,
            $bucketDirectory,
            "application/pdf"
        );
    }
    public function uploadFileFromPublicFolder(string $fileName, string $bucketDirectory, string $clientMimeType): ?string
    {
        if (Storage::disk('public')->exists($fileName)) {
            $publicPath = storage_path('app/public') . "/" . $fileName;
            $result = $this->uploadToS3($publicPath, $fileName, $bucketDirectory, $clientMimeType);
            return $result;
        }
        return null;
    }
    public function uploadToS3(string $filePath, string $fileName, string $bucketDirectory, ?string $clientMimeType = null): ?string
    {
        $this->checkExistingDirectory($bucketDirectory);
        $key = $bucketDirectory . '/' . $fileName;
        try {
            // TODO : Public read setting to be passed
            $object = [
                'Bucket' => $this->sdkConfiguration['bucket'],
                'Key' => $key,
                'Body' => fopen($filePath, 'r')
            ];
            if ($clientMimeType) {
                $object['ContentType'] = $clientMimeType;
            }
            $this->awsClient->putObject($object);
            return $fileName;
        } catch (S3Exception $e) {
            $this->handleSDKException($e);
        }
    }
    public function buildS3BucketURL(string $bucketDirectory, string|null $fileName): ?string
    {
        if ($fileName) {
            return $this->baseUrl . "/" . $bucketDirectory . "/" . $fileName;
        }
        return null;
    }
}
