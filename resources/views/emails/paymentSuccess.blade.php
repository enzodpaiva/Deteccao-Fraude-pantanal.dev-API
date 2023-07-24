<!-- nome, orderId, plano, aparelho, valor -->
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

<body style="font-family:Roboto, Arial, sans-serif; background-color:#f1f3f6">
    <center>
        <table width="660" cellspacing="0" cellpadding="0" style="display:block;background:#364FD9;margin:0 auto;">
            <tr>
                <td align="center" style="padding:30px 30px;">
                    <img src="https://mcusercontent.com/1a5defb90e08d4d25edff1906/images/fd6bb97f-14c1-848d-91cc-c4ccd1461303.png"
                        width="114" height="48" style="display: block; margin: 0 auto;" alt="Pitzi">
                </td>
            </tr>
            <tr>
                <td>
                    <img src="https://mcusercontent.com/1a5defb90e08d4d25edff1906/images/b63dc298-962e-4b50-0091-7cf29948e0a6.png"
                        width="660" height="133" style="display: block;" alt="Pitzi - Compra concluída">
                    <hr style="border:1px solid #ffffff;width:600px;margin-top:30px;">
                </td>
            </tr>
            <tr>
                <td align="center" style="padding:0px 60px;">
                    <p
                        style="text-align:center;font-size:24px;font-family:Roboto, Arial, sans-serif;color:#FFFFFF;line-height:26px;">
                        Atenção, <strong>{{ $nome }}!</strong>
                    </p>
                    <p
                        style="text-align:center;font-size:18px;font-family:Roboto, Arial, sans-serif;color:#FFFFFF;line-height:24px;">
                        <strong>Seu aparelho ainda está desprotegido.</strong>
                        <br>Responda este e-mail enviando a foto do seu telefone para comercial@pitzi.com.br e usufrua de
                        100% do seu plano.
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p
                        style="text-align:center;font-size:16px;font-family:Roboto, Arial, sans-serif;color:#1D2760;padding:30px 40px;margin-top:10px;background-color:#52D4BA;font-weight:400;">
                        Caso já tenha enviado, <strong>em até 48hs você será informado por e-mail</strong> sobre a
                        confirmação de ativação do seu plano, seu link de acesso ao seguro PITZI escolhido e todas os
                        dados necessários para quando precisar do nosso suporte.
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <table align="center" width="420" cellspacing="0" cellpadding="0">
                        <tr style="height:80px;">
                            <td align="center" width="380"
                                style="font:14px roboto, arial;color:#333;text-align:left;padding:20px 0;">
                                <p
                                    style="color:#000;background-color:#f6f6f6;font:16px roboto, arial;text-align:left;padding:15px 0px 15px 20px;">
                                    Número do pedido:<strong style="color:#004691;"> {{ $orderId }}</strong>
                                </p>
                                <p
                                    style="color:#000;background-color:#f6f6f6;font:16px roboto, arial;text-align:left;padding:15px 0px 15px 20px;">
                                    Plano de seguro PITZI escolhido:<strong style="color:#004691;">
                                        {{ $plano }}</strong>
                                </p>
                                <p
                                    style="color:#000;background-color:#f6f6f6;font:16px roboto, arial;text-align:left;padding:15px 0px 15px 20px;">
                                    Aparelho a ser segurado:<strong style="color:#004691;"> {{ $aparelho }}</strong>
                                </p>
                                <p
                                    style="color:#000;background-color:#f6f6f6;font:16px roboto, arial;text-align:left;padding:15px 0px 15px 20px;">
                                    Valor:<strong style="color:#004691;"> R$ {{number_format($valor / 100, 2, ',', '')}} / mês</strong>
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <p
                        style="text-align:center;font-size:15px;font-family:Roboto, Arial, sans-serif;color:#ffffff;padding:10px 60px;margin-top:10px;">
                        Em caso de dúvidas, acesse nossos canais de atendimento online ou
                        ligue<br><strong>(21) 3037-9991</strong>, de segunda a sábado, entre 10h e 16h.
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
    </center>
</body>

</html>
