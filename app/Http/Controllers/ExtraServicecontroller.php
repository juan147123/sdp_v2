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
    $URL_API = 'http://127.0.0.1:8005/api/v1/notifications/email-custom';
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

        // 📝 LOG 1: Ver exactamente qué le estamos intentando mandar a la API
        \Log::info("==================================================");
        \Log::info("🚀 PREPARANDO ENVÍO A: {$recipient}");
        \Log::info("Payload enviado a la API:", [
            'url'     => $URL_API,
            'headers' => ['x-api-key' => substr($apiKey, 0, 6) . '...'], // Ocultamos el resto por seguridad
            'data'    => [
                'recipient'  => $data['recipient'],
                'subject'    => $data['subject'],
                'url_header' => $data['url_header'],
                'custom_html_PREVIEW' => substr($data['custom_html'], 0, 250) . '...' // Primeros 250 caracteres del HTML
            ]
        ]);
        // Si necesitas ver TODO el HTML completo en el log, descomenta la línea de abajo:
        // \Log::info("HTML Completo enviado: " . $data['custom_html']);

        try {
            // Realizamos la petición HTTP
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
            ])->post($URL_API, $data);

            $lastResponse = $response;

            if ($response->successful()) {
                \Log::info("✅ Correo enviado exitosamente a: {$recipient} - Status: " . $response->status());
            } else {
                // 📝 LOG 2: Si el servidor nos rechaza (ej. 403), guardamos TODAS las cabeceras de respuesta de Apache
                \Log::error("🚨 API RECHAZÓ LA PETICIÓN (Status {$response->status()})");
                \Log::error("Cabeceras de respuesta de Apache:", $response->headers());
                \Log::error("Cuerpo crudo devuelto por el servidor: " . $response->body());
            }

        } catch (\Exception $e) {
            \Log::error("💥 Error crítico de red o TLS conectando a {$recipient}: " . $e->getMessage());
        }
        \Log::info("==================================================");
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
                'linkAcceso'         => 'https://desvinculaciones.grupoflesan.com/',
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
