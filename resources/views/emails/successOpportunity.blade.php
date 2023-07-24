<!-- nome, orderId, aparelho, plano, valor  -->
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>

<head>
    <meta charset="utf-8">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&amp;display=swap"
        rel="stylesheet">
</head>

<body style="font-family:'Roboto', Arial, sans-serif">
    <table align="center" width="660" cellspacing="0" cellpadding="0"
        style="display:block;background-color:#E8F1F2;margin:0 auto;font-family:Roboto, Arial, sans-serif;">
        <tr>
            <td>
                <img src="https://mcusercontent.com/1a5defb90e08d4d25edff1906/images/e5adcb70-ecb9-3de1-3f42-d417614feb04.png"
                    width="660" height="70" style="display: block;" alt="Pitzi">
            </td>
        </tr>
        <tr>
            <td
                style="font:18px roboto;color:#2F43B3;padding:0 60px;vertical-align:middle;font-family:Roboto, Arial, sans-serif;">
                <p style="font-size:36px;margin-top:60px;margin-bottom:0;font-family:Roboto, Arial, sans-serif;">
                    Parabéns, <b style="color:#2F43B3;">{{ $nome }}!</b>
                </p>
                <p style="font:18px roboto, arial;margin-top:30px;">
                    Recebemos a foto do seu aparelho e todas as etapas de contratação do seu plano de seguro Pitzi estão
                    concluídas.<br>Agora seu telefone está protegido!
                </p>
                <p style="font:16px roboto, arial;margin-top:60px;text-align:center;">
                    <strong>Confira abaixo as informações do seu Plano:</strong>
                </p>
            </td>
        </tr>
        <tr>
            <td>
                <table align="center" width="420" cellspacing="0" cellpadding="0">
                    <tr style="height:80px;">
                        <td align="center" width="380"
                            style="font:14px roboto, arial;color:#333;text-align:left;padding:2px 0px 20px;">
                            <p
                                style="color:#000;background-color:#ffffff;font:16px roboto, arial;text-align:left;padding:15px 0px 15px 20px;">
                                Número do pedido:<strong style="color:#004691;"> {{ $orderId }}</strong>
                            </p>
                            <p
                                style="color:#000;background-color:#ffffff;font:16px roboto, arial;text-align:left;padding:15px 0px 15px 20px;">
                                Plano de seguro PITZI contratado:<strong style="color:#004691;">
                                    {{ $plano }}</strong>
                            </p>
                            <p
                                style="color:#000;background-color:#ffffff;font:16px roboto, arial;text-align:left;padding:15px 0px 15px 20px;">
                                Aparelho segurado:<strong style="color:#004691;"> {{ $aparelho }}</strong>
                            </p>
                            <p
                                style="color:#000;background-color:#ffffff;font:16px roboto, arial;text-align:left;padding:15px 0px 15px 20px;">
                                Valor:<strong style="color:#004691;"> R$ {{number_format($valor / 100, 2, ',', '')}} / mês</strong>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td align="center" style="padding:10px 90px;">
                <p style="font:14px roboro, arial;line-height:20px;color:#333;text-align:center;">
                    Ah, e não esqueça de baixar nosso App <strong>Pitzi - Proteção para Celulares</strong>.<br>São
                    muitas funcionalidades para ajudar você a ficar por dentro de tudo o que acontece com a sua
                    proteção! É super simples, faça agora mesmo o download clicando em um dos botões abaixo.
                </p>
            </td>
        </tr>
        <tr>
            <td>
                <table align="center" width="660" cellspacing="0" cellpadding="0">
                    <tr>
                        <td align="center" valign="top" width="80"
                            style="font:14px roboto, arial;color:#333;padding:20px 0px;">
                        </td>
                        <td align="center" valign="top" width="160"
                            style="font:14px roboto, arial;color:#333;padding:20px 0px;">
                            <a href="https://play.google.com/store/apps/details?id=br.com.pitzi.pitzionboarding&amp;hl"
                                style="text-decoration:none;" target="_blank">
                                <img src="https://mcusercontent.com/1a5defb90e08d4d25edff1906/images/de2f0c4c-f809-d2a2-1895-cc8ffeb1aeb2.png"
                                    width="200" height="60" style="display: block; margin:0 auto;"
                                    alt="Google Play"></a>
                        </td>
                        <td align="center" valign="top" width="160"
                            style="font:14px roboto, arial;color:#333;padding:20px 0px;">
                            <a href="https://apps.apple.com/br/app/pitzi-prote%C3%A7%C3%A3o-para-celulares/id777770110"
                                style="text-decoration:none;" target="_blank">
                                <img src="https://mcusercontent.com/1a5defb90e08d4d25edff1906/images/5de5193a-a4d7-74a4-562d-fcec394eb2dd.png"
                                    width="200" height="60" style="display: block; margin:0 auto;"
                                    alt="Apple Store"></a>
                        </td>
                        <td align="center" valign="top" width="80"
                            style="font:14px roboto, arial;color:#333;padding:20px 0px;">

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td align="center" style="padding:10px 30px;">
                <p style="font:14px roboro, arial;line-height:20px;color:#333;text-align:center;">
                    Caso haja alguma divergência, acesse nossos canais de atendimento online<br>ou ligue <strong>(21)
                        3037-9991</strong>, de segunda a sábado, entre 10h e 16h.
                </p>
            </td>
        </tr>
        <tr>
            <td>
                <img src="https://mcusercontent.com/1a5defb90e08d4d25edff1906/images/68d978da-be04-843e-74f7-9d8062256472.png"
                    width="660" height="70" style="display: block;" alt="Pitzi">
            </td>
        </tr>
    </table>
</body>

</html>
