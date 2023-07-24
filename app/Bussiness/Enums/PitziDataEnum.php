<?php

namespace App\Bussiness\Enums;

interface PitziDataEnum
{
    const VERIFICACAO_FOTO_APARELHO_PENDENTE = 'pending';
    const VERIFICACAO_FOTO_APARELHO_ANALISE = 'in_analysis';
    const VERIFICACAO_FOTO_APARELHO_CONFIRMADO = 'confirmed';
    const VERIFICACAO_FOTO_APARELHO_REJEITADO = 'rejected';

    const FORMA_PAGAMENTO_A_VISTA = "upfront";
    const FORMA_PAGAMENTO_PARCELADO = "financed";

    const STATUS_VERIFICACAO_APARELHO = [
        '' => '',
        self::VERIFICACAO_FOTO_APARELHO_CONFIRMADO => true,
        self::VERIFICACAO_FOTO_APARELHO_PENDENTE => false,
        self::VERIFICACAO_FOTO_APARELHO_ANALISE => false,
        self::VERIFICACAO_FOTO_APARELHO_REJEITADO => false,

    ];

    const FORMA_PAGAMENTO_NAME = [
        '' => '',
        self::FORMA_PAGAMENTO_A_VISTA => "À Vista",
        self::FORMA_PAGAMENTO_PARCELADO => "Parcelado",
    ];
}
