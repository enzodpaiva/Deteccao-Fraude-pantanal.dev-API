<?php

namespace App\Bussiness\Enums;

interface StatusEnum
{
    //STATUS
    const PENDENTE = 'pendente';
    const GANHA = 'ganha';
    const PERDIDA = 'perdida';
    const ANALISE_FALHA = 'analisedefalha';
    const ANDAMENTO = 'andamento';
    const CARRINHO_ABANDONADO = 'carrinhoabandonado';
    const CANCELADO = 'cancelado';
}
