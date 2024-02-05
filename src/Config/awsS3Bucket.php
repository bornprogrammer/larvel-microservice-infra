<?php

return [
    'baseUrl' => env('AWS_S3_BUCKET_BASEURL', ""),
    'region' => env('AWS_DEFAULT_REGION', ""),
    'key' => env('AWS_ACCESS_KEY_ID', ""),
    'secret' => env('AWS_SECRET_ACCESS_KEY', ""),
    'bucket' => env('AWS_BUCKET', "")
];