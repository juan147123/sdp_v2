<?php

namespace App\Http\Controllers;

use App\Models\ExtraService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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

    public static function send_email_gf($body, $subject, $emails_to)
    {
        $URL_API = 'https://api.grupoflesan.com/api/';
        $token = self::get_token_api_gf();
        $headers = [
            'Authorization' => "Bearer $token"
        ];
        $data = [
            'cuerpo' => $body,
            'asunto' => $subject,
            'destinatarios' => $emails_to,
            'cabecera_img' => 'https://api.grupoflesan.com/img/LogoSDP.png'
        ];
        $response = Http::withHeaders($headers)->post(
            "{$URL_API}sendNotificacionVacia",
            $data
        );
        return $response;
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
