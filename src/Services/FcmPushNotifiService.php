<?php

namespace Laravel\Infrastructure\Services;

use Laravel\Infrastructure\Models\NotificationsLogs;
use Illuminate\Http\UploadedFile;
use Laravel\Infrastructure\Exceptions\InternalServerErrorException;
use PDF;
use Illuminate\Support\Facades\Storage;
use Laravel\Infrastructure\Facades\AwsS3BucketServiceFacade;

class FcmPushNotifiService extends BaseService
{

    public function sendNotification(string $token, string $title, string $body)
    {
        $firebaseToken = $token;
        $SERVER_API_KEY = env('FCM_SERVER_KEY', "");
        $data = [
            "to" => $firebaseToken,
            "notification" => [
                "title" => $title,
                "body" => $body
            ]
        ];

        $dataString = json_encode($data);
        $headers = [
            'authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        $response = curl_exec($ch);

        $array = [
            'fcm_token' => $firebaseToken,
            'response' => json_encode($response)
        ];

        NotificationsLogs::create($array);

        return true;
    }
}
