<?php

namespace App\Bussiness\Enums;

interface BitrixEnum
{
    const BITRIX_STATUS_UPSERT = [
        'deal' => [
            '' => '',
            'Pendente' => "NEW",
            'Analyze failure' => "APOLOGY",
            'Ganha' => "WON",
            'Negócio perdido' => "LOSE",
            'Em andamento' => "UC_A3NMKI",
        ],
        'lead' => [
            '' => '',
            'Carrinho Abandonado' => "NEW",
            'Em andamento' => "1",
            'Cancelado' => "JUNK",
        ],
    ];

    const BITRIX_SUBSTATUS_UPSERT = [
        'deal' => [
            '' => '',
            'Cadastro efetuado' => 706,
            'Pagamento não efetuado' => 708,
            'Pagamento efetuado' => 722,
            'Envio foto não efetuado' => 712,
            'Concluido' => 714,
        ],
        'lead' => [
            '' => '',
            'Cadastro efetuado' => 558,
            'Pagamento não efetuado' => 560,
            'Pagamento efetuado' => 724,
            'Envio foto não efetuado' => 718,
            'Concluido' => 720,
        ],
    ];

    const BITRIX_LOSTSUBSTATUS_UPSERT = [
        'deal' => [
            '' => '',
            'Duplicidade' => 626,
            'Cliente sem interesse' => 628,
            'Sem limite no cartão' => 630,
            'Cliente achou caro' => 632,
            'Ja é cliente' => 666,
        ],

        'lead' => [
            '' => '',
            'Duplicidade' => 588,
            'Cliente sem interesse' => 590,
            'Sem limite no cartão' => 592,
            'Cliente achou caro' => 594,
            'Ja é cliente' => 662,
        ],
    ];

    const BITRIX_USER_UPSERT = [
        '' => '',
        'user_36' => 36,
        'user_7820' => 7820,
    ];
}
