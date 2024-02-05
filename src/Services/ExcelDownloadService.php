<?php

namespace Laravel\Infrastructure\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Infrastructure\Facades\AwsS3BucketServiceFacade;
use Laravel\Infrastructure\Exceptions\ResourceNotFoundException;

class ExcelDownloadService extends BaseService
{

    public function generateExcelFile(string $fileName, string $directoryName)
    {
        if (Storage::disk('public')->exists($fileName)) {
            $s3fileName = AwsS3BucketServiceFacade::uploadDownloadableExcelFileFromPublicFolder($fileName, $directoryName);
            $link = AwsS3BucketServiceFacade::buildS3BucketURL($directoryName, $s3fileName);
            unlink(storage_path('app/public') . "/" . $fileName);
            return ['download-link' => $link];
        } else {
            throw new ResourceNotFoundException("File not found");
        }
    }
}
