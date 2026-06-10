<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
        style="box-shadow: rgba(17, 17, 26, 0.1) 0px 1px 0px, rgba(17, 17, 26, 0.1) 0px 8px 24px, rgba(17, 17, 26, 0.1) 0px 16px 48px;">
        <tbody>
            <tr>
                <td class="esd-container-frame" width="560" valign="top" align="center">
                    <table style="background-position: left top" width="100%" cellspacing="0" cellpadding="0">
                        <tbody>
                            <tr>
                                <td class="esd-block-text es-p40r es-p40l" align="left">
                                    <h1
                                        style="text-align: left; font-size: 22px; padding-left: 25px; padding-right: 25px; padding-top: 20px; padding-bottom: 20px; margin: 0;">
                                        NUEVA SOLICITUD
                                    </h1>
                                </td>
                            </tr>
                            <tr>
                                <td class="esd-block-text es-p25t es-p40r es-p40l" align="justify">
                                    <p
                                        style="margin: 0; letter-spacing: 0.1px; line-height: 1.6; padding-left: 25px; padding-right: 25px; padding-bottom: 30px; font-size: 15px; color: #444;">
                                        El usuario <strong>{{$data["usuario"]}}</strong> ha registrado la solicitud de
                                        desvinculación, identificada con el código
                                        <strong>{{$data["solicitud"]["codigo"]}}</strong>.
                                    </p>
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                        style="margin:0; padding-left:25px; padding-right:25px; padding-top:40px;">
                                        <tr>
                                            <td align="center">

                                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                                    <tr>
                                                        <td align="center">
                                                            <a href="{{ $data['linkAcceso'] }}" target="_blank" rel="noopener noreferrer"
                                                                style="background:#fd1111;border:1px solid #fd1111;border-radius:6px;color:#ffffff;display:inline-block;font-family:Arial,sans-serif;font-size:16px;font-weight:bold;line-height:54px;text-align:center;text-decoration:none;width:300px;">
                                                                Ver solicitud
                                                            </a>
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