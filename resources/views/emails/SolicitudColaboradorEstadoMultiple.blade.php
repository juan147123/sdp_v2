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
    <table width="100%" cellspacing="0" cellpadding="0" style="box-shadow: rgba(17, 17, 26, 0.1) 0px 1px 0px, rgba(17, 17, 26, 0.1) 0px 8px 24px, rgba(17, 17, 26, 0.1) 0px 16px 48px;">
        <tbody>
            <tr>
                <td class="esd-container-frame" width="560" valign="top" align="center">
                    <table style="background-position: left top" width="100%" cellspacing="0" cellpadding="0">
                        <tbody>
                            <tr>
                                <td class="esd-block-text es-p40r es-p40l" align="left">
                                    <h1 style="text-align: left; font-size: 19px; padding-left: 25px; padding-right: 25px; padding-top: -25px;">
                                        Estado de la Solicitud de Desvinculación
                                    </h1>
                                </td>
                            </tr>
                            <tr>
                                <td class="esd-block-text es-p25t es-p40r es-p40l" align="justify">
                                    <p style="margin: 0; letter-spacing: 0.1px; line-height: 1.5; padding-left: 25px; padding-right: 25px; padding-top: -25px;">
                                        La solicitud desvinculación con código {{$data["solicitud"]["codigo"]}} a sido {{$data["estado_cabecera"]}} completamente.
                                    </p>
                                    <p style="margin: 0; font-size: 12px; letter-spacing: 0.1px; line-height: 1.5; padding-left: 25px; padding-right: 25px; padding-top: 25px;">
                                        Para ve la solicitud haga click en el siguiente enlace: <a href="{{ $data['linkAcceso'] }}">{{ $data['linkAcceso'] }}</a>
                                    </p>
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