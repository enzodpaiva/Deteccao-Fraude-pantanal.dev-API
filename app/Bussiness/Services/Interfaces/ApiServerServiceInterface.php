<?php

namespace App\Bussiness\Services\Interfaces;

interface ApiServerServiceInterface
{
    public function sendSampleView(array $data);

    public function sendRandomTransaction(array $data = []);

    public function sendStoreFraud(array $data);
}
