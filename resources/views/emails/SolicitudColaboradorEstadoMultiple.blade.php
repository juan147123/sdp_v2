<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <meta charset="UTF-8" />
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta name="x-apple-disable-message-reformatting" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta content="telephone=no" name="format-detection" />
    <title></title>
</head>

<body>
    <table width="100%" cellspacing="0" cellpadding="0"
        style="box-shadow: rgba(17, 17, 26, 0.1) 0px 1px 0px,
               rgba(17, 17, 26, 0.1) 0px 8px 24px,
               rgba(17, 17, 26, 0.1) 0px 16px 48px;">
        <tbody>
            <tr>
                <td class="esd-container-frame" width="560" valign="top" align="center">
                    <table style="background-position: left top" width="100%" cellspacing="0" cellpadding="0">
                        <tbody>
                            <tr>
                                <td class="esd-block-text es-p40r es-p40l" align="left">
                                    <h1 style="text-align: left; font-size: 19px; padding-left: 25px; padding-right: 25px;">
                                        Estado de la Solicitud de Desvinculación
                                    </h1>
                                </td>
                            </tr>
                            <tr>
                                <td class="esd-block-text es-p25t es-p40r es-p40l" align="justify">
                                    <p style="margin: 0; letter-spacing: 0.1px; line-height: 1.5;
                                              padding-left: 25px; padding-right: 25px;">
                                        La solicitud con código
                                        <strong>{{ $data["solicitud"]["codigo"] }}</strong>
                                        ha sido <strong>{{ $data["estado_cabecera"] }}</strong> masivamente.
                                    </p>

                                    {{-- Colaboradores --}}
                                    @if (!empty($data["colaboradores"]))
                                        <p style="margin: 0; letter-spacing: 0.1px; line-height: 1.5;
                                                  padding-left: 25px; padding-right: 25px; padding-top: 10px;">
                                            Colaboradores afectados:
                                        </p>
                                        <ul style="margin: 0; padding-left: 50px; line-height: 1.6;">
                                            @foreach (explode(',', $data["colaboradores"]) as $colaborador)
                                                <li>{{ trim($colaborador) }}</li>
                                            @endforeach
                                        </ul>

                                    @endif

                                    <p style="margin: 0; letter-spacing: 0.1px; line-height: 1.5;
                                              padding-left: 25px; padding-right: 25px; padding-top: 10px;">
                                        Acción realizada por:
                                        <strong>{{ $data["usuario"] }}</strong>

                                        Comentarios: 
                                        <strong>{{$data["comentarios"] }}</strong>
                                    </p>

                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0; padding-left:25px; padding-right:25px; padding-top:25px;">
                                        <tr>
                                            <td align="center">
                                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                                    <tr>
                                                        <td align="center">
                                                            <!--[if mso]>
                                                            <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" href="{{ $data['linkAcceso'] }}" style="height:54px;v-text-anchor:middle;width:300px;" arcsize="12%" strokecolor="#fd1111" fillcolor="#fd1111">
                                                                <w:anchorlock/>
                                                                <center style="color:#ffffff;font-family:Arial,sans-serif;font-size:16px;font-weight:bold;">Ver solicitud</center>
                                                            </v:roundrect>
                                                            <![endif]-->
                                                            <!--[if !mso]><!-- -->
                                                            <a href="{{ $data['linkAcceso'] }}" target="_blank" rel="noopener noreferrer"
                                                                style="background:#fd1111;border:1px solid #fd1111;border-radius:6px;color:#ffffff;display:inline-block;font-family:Arial,sans-serif;font-size:16px;font-weight:bold;line-height:54px;text-align:center;text-decoration:none;width:300px;">
                                                                Ver solicitud
                                                            </a>
                                                            <!--<![endif]-->
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center" style="padding-top: 40px;">
                                                            <div style="font-size:12px; line-height:1.5; color:#6c757d;">
                                                                Si el botón no funciona, copie y pegue este enlace en su navegador:
                                                                <br><br>
                                                                <a href="{{ $data['linkAcceso'] }}" target="_blank" rel="noopener noreferrer" style="color:#0d6efd;">{{ $data['linkAcceso'] }}</a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>
