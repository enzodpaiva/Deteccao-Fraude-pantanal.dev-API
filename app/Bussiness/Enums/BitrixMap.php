<?php

namespace App\Bussiness\Enums;

use App\Bussiness\Enums\OpportunityDataEnum;

trait BitrixMap
{
    protected $booleanField = [
        true => 1,
        false => 0,
    ];

    protected $opportunityStatus = [
        'deal' => [
            '' => '',
            OpportunityDataEnum::PENDENTE => 'NEW',
            OpportunityDataEnum::ANALISE_FALHA => 'APOLOGY',
            OpportunityDataEnum::GANHA => 'WON',
            OpportunityDataEnum::PERDIDA => 'LOSE',
            OpportunityDataEnum::ANDAMENTO => 'UC_A3NMKI',
        ],
        'lead' => [
            '' => '',
            OpportunityDataEnum::CARRINHO_ABANDONADO => 'NEW',
            OpportunityDataEnum::ANDAMENTO => '1',
            OpportunityDataEnum::CANCELADO => 'JUNK',
        ],
    ];

    protected $opportunitySubStatus = [
        'deal' => [
            '' => '',
            OpportunityDataEnum::CADASTRO_EFETUADO => 706,
            OpportunityDataEnum::PAGAMENTO_NAO_EFETUADO => 708,
            OpportunityDataEnum::PAGAMENTO_EFETUADO => 722,
            OpportunityDataEnum::ENVIO_FOTO_NAO_EFETUADO => 712,
            OpportunityDataEnum::CONCLUIDO => 714,
        ],
        'lead' => [
            '' => '',
            OpportunityDataEnum::CADASTRO_EFETUADO => 558,
            OpportunityDataEnum::PAGAMENTO_NAO_EFETUADO => 560,
            OpportunityDataEnum::PAGAMENTO_EFETUADO => 724,
            OpportunityDataEnum::ENVIO_FOTO_NAO_EFETUADO => 718,
            OpportunityDataEnum::CONCLUIDO => 720,
        ],
    ];

    protected $opportunityLostSubStatus = [
        'deal' => [
            '' => '',
            OpportunityDataEnum::DUPLICIDADE => 626,
            OpportunityDataEnum::CLIENTE_SEM_INTERESSE => 628,
            OpportunityDataEnum::SEM_LIMITE_CARTAO => 630,
            OpportunityDataEnum::CLIENTE_ACHOU_CARO => 632,
            OpportunityDataEnum::JA_E_CLIENTE => 666,
            OpportunityDataEnum::ERRO_ENVIAR_PEDIDO_PITZI => 738,
        ],
        'lead' => [
            '' => '',
            OpportunityDataEnum::DUPLICIDADE => 588,
            OpportunityDataEnum::CLIENTE_SEM_INTERESSE => 590,
            OpportunityDataEnum::SEM_LIMITE_CARTAO => 592,
            OpportunityDataEnum::CLIENTE_ACHOU_CARO => 594,
            OpportunityDataEnum::JA_E_CLIENTE => 664,
            OpportunityDataEnum::ERRO_ENVIAR_PEDIDO_PITZI => 740,
        ],
    ];

    protected $sourceNameCode = [
        'deal' => [
            '' => '',
            OpportunityDataEnum::GOOGLE_SEARCH => 644,
            OpportunityDataEnum::GOOGLE_MAX => 646,
            OpportunityDataEnum::MET_INTERESSE => 648,
            OpportunityDataEnum::META_REMKT => 650,
            OpportunityDataEnum::META_SEMELHANTE => 652,
            OpportunityDataEnum::WHATS_SITE => 702,
            OpportunityDataEnum::WHATS_META => 704,
            OpportunityDataEnum::CALL => 742,
            OpportunityDataEnum::EMAIL => 744,
            OpportunityDataEnum::WEBSITE => 746,
            OpportunityDataEnum::ADVERTISING => 748,
            OpportunityDataEnum::EXISTING_CLIENT => 750,
            OpportunityDataEnum::BY_RECOMENDATION => 752,
            OpportunityDataEnum::SHOW_EXIBITION => 754,
            OpportunityDataEnum::CRM_FORM => 756,
            OpportunityDataEnum::CALLBACK => 758,
            OpportunityDataEnum::SALES_BOOST => 760,
            OpportunityDataEnum::ONLINE_STORE => 762,
            OpportunityDataEnum::OTHER => 764,
            OpportunityDataEnum::WHATSAPP_PITZI => 766,
            OpportunityDataEnum::FACEBOOK_ADS_WHATSAPP_PITZI => 768,
        ],
        'lead' => [
            '' => '',
            OpportunityDataEnum::GOOGLE_SEARCH => 612,
            OpportunityDataEnum::GOOGLE_MAX => 614,
            OpportunityDataEnum::MET_INTERESSE => 616,
            OpportunityDataEnum::META_REMKT => 618,
            OpportunityDataEnum::META_SEMELHANTE => 620,
            OpportunityDataEnum::WHATS_SITE => 698,
            OpportunityDataEnum::WHATS_META => 700,
            OpportunityDataEnum::CALL => 770,
            OpportunityDataEnum::EMAIL => 772,
            OpportunityDataEnum::WEBSITE => 774,
            OpportunityDataEnum::ADVERTISING => 776,
            OpportunityDataEnum::EXISTING_CLIENT => 778,
            OpportunityDataEnum::BY_RECOMENDATION => 780,
            OpportunityDataEnum::SHOW_EXIBITION => 782,
            OpportunityDataEnum::CRM_FORM => 784,
            OpportunityDataEnum::CALLBACK => 786,
            OpportunityDataEnum::SALES_BOOST => 788,
            OpportunityDataEnum::ONLINE_STORE => 790,
            OpportunityDataEnum::OTHER => 792,
            OpportunityDataEnum::WHATSAPP_PITZI => 794,
            OpportunityDataEnum::FACEBOOK_ADS_WHATSAPP_PITZI => 796,
        ],
    ];

    protected $planNameCode = [
        '' => '',
        OpportunityDataEnum::PLANO_PROTECAO_TOTAL => OpportunityDataEnum::PROTECAO_TOTAL,
        OpportunityDataEnum::PLANO_ACIDENTES => OpportunityDataEnum::PROTECAO_ACIDENTES,
        OpportunityDataEnum::PLANO_PROTECAO_TELA => OpportunityDataEnum::PROTECAO_TELA,
    ];

    protected $paymentForm = [
        'deal' => [
            '' => '',
            OpportunityDataEnum::FORMA_PAGAMENTO_A_VISTA => 726,
            OpportunityDataEnum::FORMA_PAGAMENTO_PARCELADO => 728,
        ],
        'lead' => [
            '' => '',
            OpportunityDataEnum::FORMA_PAGAMENTO_A_VISTA => 730,
            OpportunityDataEnum::FORMA_PAGAMENTO_PARCELADO => 732,
        ],

    ];

}
