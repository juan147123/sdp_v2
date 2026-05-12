<?php

namespace App\Http\Controllers;

use App\Models\ExtraService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Log;

class ExtraServicecontroller extends Controller
{
    public static function get_token_api_gf()
    {
        $URL_API = 'https://api.grupoflesan.com/api/';
        $response = Http::post(
            "{$URL_API}login",
            [
                'username' => 'rgutierrez@flesan.com.pe',
                'password' => 'm#st3R!'
            ]
        );
        $data = $response->json();
        $access_token = $data['access_token'];
        return $access_token;
    }

    public static function send_email_gf($body, $subject, $emails_to, $url_header = null)
    {
        $URL_API = 'https://apinotificaciones.grupoflesan.com/api/v1/notifications/email-custom';
        $apiKey = '6a503aa284cfbd36593fa35e3bf9b0';

        if (empty($url_header)) {
            $url_header = 'https://i-c-flesan.github.io/assets-flesan/headers_aplicativos/header_rojo_sdd_estadodesolicitud.png';
        }

        $recipients = explode(',', $emails_to);
        $lastResponse = null;

        foreach ($recipients as $recipient) {
            $recipient = trim($recipient);
            if (empty($recipient)) continue;

            $data = [
                'custom_html' => $body,
                'recipient' => $recipient,
                'subject' => $subject,
                'url_header' => $url_header
            ];

            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
            ])->post($URL_API, $data);

            $lastResponse = $response;
            \Log::info("Enviando correo a: {$recipient} - Status: " . $response->status());
        }

        return $lastResponse;
    }


    public static function SendDocumentsInDrive($files, $app)
    {
        $URL_API = env('URL_BACKEND_API_GF');
        $token = ExtraServicecontroller::get_token_api_gf();
        $mutipart = ExtraServicecontroller::BuildMultipart($files, $app);
        $client = new \GuzzleHttp\Client();
        $response = $client->request(
            'POST',
            $URL_API . 'sendFileDriveV2',
            [
                'headers' => [
                    'Authorization' => "Bearer $token"
                ],
                'multipart' =>  $mutipart,
            ]
        );

        $bodyContents = $response->getBody()->getContents();
        $data = json_decode($bodyContents, true);

        return $data;
    }
    public static function BuildMultipart($files, $app)
    {
        $multipart = [];
        $multipart[] = [
            'name' => 'app',
            'contents' => $app
        ];

        foreach ($files as $file) {
            $fileContents = file_get_contents($file);
            $multipart[] = [
                'name' => 'files[]',
                'contents' => $fileContents,
                'filename' => $file->getClientOriginalName(),
            ];
        }
        return $multipart;
    }
}
