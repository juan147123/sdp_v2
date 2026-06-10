<?php

namespace App\Http\Controllers;

use App\Models\ExtraService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Log;
use Illuminate\Support\Facades\View;

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

            try {
            // Realizamos la petición HTTP
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
            ])->post($URL_API, $data);

            $lastResponse = $response;

            // Si el estatus es exitoso (2xx)
            if ($response->successful()) {
                \Log::info("Correo enviado exitosamente a: {$recipient} - Status: " . $response->status());
            } else {
                // SI DA ERROR (como el 403), capturamos los detalles exactos
                \Log::error("🚨 FALLÓ el envío de correo a: {$recipient}");
                \Log::error("Status Code recibido: " . $response->status());
                \Log::error("Cuerpo del error de la API: " . $response->body());
            }

        } catch (\Exception $e) {
            // Captura errores de red crípticos (errores de timeout, caídas de DNS, SSL inválidos, etc.)
            \Log::error("💥 Error crítico de conexión al intentar enviar correo a {$recipient}: " . $e->getMessage());
        }
    }

    return $lastResponse;
}

    public function enviarCorreoPersonalChile(Request $request)
    {
        $emails_to = 'victor.guerra@flesan.cl';
        $subject = 'Notificación desde SDP - Personal Chile';

        $userName = auth()->user() ? auth()->user()->name : 'Usuario Desconocido';
        $userEmail = auth()->user() ? auth()->user()->username : 'Sin Correo';

        $body = View::make('emails.NuevaSolicitud', [
            'data' => [
                'solicitud' => [
                    'codigo' => '1'
                ],
                'estado_cabecera'    => 'nueva',
                'estado_descripcion' => 'descripcion',
                'usuario'            => strtoupper('Usuario Ejemplo'),
                'colaborador'        => 'Colaborador ejemplo',
                'comentarios'        => 'Comentarios ejemplo',
            ],
        ])->render();

        $response = self::send_email_gf($body, $subject, $emails_to,'https://i-c-flesan.github.io/assets-flesan/headers_aplicativos/header_rojo_sdd_nuevasolicitud.png' );

        if ($response && $response->successful()) {
            return response()->json([
                'success' => true,
                'message' => 'Correo enviado exitosamente.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'El correo no pudo ser enviado.',
            'status' => $response ? $response->status() : null,
            'body' => $response ? $response->body() : null
        ], 500);
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
