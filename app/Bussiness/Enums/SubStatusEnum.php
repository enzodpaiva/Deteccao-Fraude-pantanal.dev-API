<?php

namespace App\Bussiness\Enums;

interface SubStatusEnum
{
    //SUBSTATUS
    const CADASTRO_EFETUADO = 'cadastroefetuado';
    const PAGAMENTO_NAO_EFETUADO = 'pagamentonaoefetuado';
    const PAGAMENTO_EFETUADO = 'pagamentoefetuado';
    const ENVIO_FOTO_NAO_EFETUADO = 'enviofotonaoefetuado';
    const CONCLUIDO = 'concluido';
    const ERRO_ENVIAR_PEDIDO_PITZI = 'erroenviarpedidopitzi';

    //LOST SUBSTATUS
    const DUPLICIDADE = 'duplicidade';
    const CLIENTE_SEM_INTERESSE = 'clienteseminteresse';
    const SEM_LIMITE_CARTAO = 'semlimitecartao';
    const CLIENTE_ACHOU_CARO = 'clienteachoucaro';
    const JA_E_CLIENTE = 'jaecliente';

}
