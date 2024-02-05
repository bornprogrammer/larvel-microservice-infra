<?php

namespace Laravel\Infrastructure\Services;

use Illuminate\Http\UploadedFile;
use Laravel\Infrastructure\Exceptions\InternalServerErrorException;
use PDF;
use Illuminate\Support\Facades\Storage;
use Laravel\Infrastructure\Facades\AwsS3BucketServiceFacade;

class PdfCreationService extends BaseService
{

    public function converFromHTMLtoPDF(string $template, ?array $data, string $directoryName, $fileName = null)
    {
        $date = date('Y-m-d');
        $path = storage_path('app/public');
        $extension = '.pdf';
        if ($fileName) {
            $fileName .= $extension;
        } else {
            $fileName = time() . $extension;
        }
        // $fileName = $date . '-' . time() . '.pdf';
        $pdf = PDF::loadView($template, $data);
        $pdf->save($path . '/' . $fileName);

        if (Storage::disk('public')->exists($fileName)) {
            $s3fileName = AwsS3BucketServiceFacade::uploadDownloadablePDFFileFromPublicFolder($fileName, $directoryName);
            unlink($path . "/" . $fileName);
            return ['fileName' => $s3fileName];
        } else {
            throw new ResourceNotFoundException("File not found");
        }
    }
}
