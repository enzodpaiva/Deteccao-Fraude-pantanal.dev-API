<?php

namespace Tests\Integrations;

use App\Bussiness\Models\Sale;
use App\Bussiness\Services\LeadService;
use DateTime;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PitziIntegrationTest extends TestCase
{
    /**
     * @test
     */
    public function testGetProductsByTacV2IsSuccess()
    {
        $query = [
            'description' => 'Iphone 8',
            'tac' => '35940208',
            'internal_memory' => '64GB',
            'description' => 'Iphone 12',
        ];

        $mockedResponseJson = json_decode('
        {
            "data": {
                "products": [
                    {
                        "product": {
                            "id": 1642,
                            "name": "Apple iPhone 8 Plus 64GB",
                            "price": 72.9,
                            "product_fee": 349.9,
                            "internal_memory": "64GB"
                        }
                    },
                    {
                        "product": {
                            "id": 1643,
                            "name": "Apple iPhone 8 128GB",
                            "price": 72.9,
                            "product_fee": 349.9,
                            "internal_memory": "128GB"
                        }
                    }
                ]
            }
        }', true);

        $response = $this->client->get('v2/products/tac_search', $query);
        $this->assertEquals(Response::HTTP_OK, $response->status());
        $this->assertEquals($mockedResponseJson, ['data' => $response->json()]);
    }

    /**
     * @test
     */
    public function testConsultFullSearchProductsIsSuccess()
    {
        $query = [
            'imei' => '354830098697248',
            'description' => 'Iphone 12',
            'plan' => '260',
            'ean' => '7892509075701',
        ];

        $mockedResponseJson = json_decode('
        {
            "data": {
                "product": {
                    "id": 1642,
                    "name": "Apple iPhone 8 Plus 64GB",
                    "price": 72.9,
                    "product_fee": 349.9
                }
            }
        }', true);

        $response = $this->client->get('products/full_search', $query);
        $this->assertEquals(Response::HTTP_OK, $response->status());
        $this->assertEquals($mockedResponseJson, ['data' => $response->json()]);
    }

    private function createSale(): Sale
    {
        $leadService = LeadService::factory();

        $sale = $leadService->upsertSale(json_decode('{
            "_id": "",
            "canal": "whatsmeta",
            "aparelho": {
                "modelo": "Apple iPhone 8 Plus 64GB",
                "imei": "354830098697248",
                "notaFiscal": "123456",
                "dataCompra": "22032023",
                "preco": 139090,
                "id": "2224",
                "usado": false,
                "validado": false
            },
            "seguro": {
                "planoId": "331",
                "precoTotal": 249972,
                "precoParcelado": 20831,
                "formaPagamento": "financed",
                "numeroParcelas": 12
            },
            "consumidor": {
                "nome": "enzo paiva teste",
                "filiacao": "Nome da mãe",
                "telefone": {
                    "contato": "21991976858",
                    "servico": "21991976858"
                },
                "email": "e.paiva@vertex.vertexdigital.co",
                "cpf": "68699897537",
                "nascimento": "10062000",
                "endereco": {
                    "logradouro": "rua x",
                    "numero": "44",
                    "complemento": "teste",
                    "bairro": "Ieda",
                    "cidade": "campo grande",
                    "uf": "MS",
                    "cep": "79050620",
                    "referencia": "teste"
                }
            }
        }', true));
        return $sale;
    }

    public function testCreateSignaturePitziSiteIsSuccess()
    {
        $sale = $this->createSale();

        $order = [
            "representant_order" => [
                "model" => $sale->getAparelho()->getModelo(), //"Apple iPhone 4 32GB"
                "phonenumber" => $sale->getConsumidor()->getTelefoneServico(), //"11999991010"
                "email" => $sale->getConsumidor()->getEmail(), //"cliente@email.com.br"
                "imei" => $sale->getAparelho()->getImei(), //"457384958710298"
                "price" => number_format(((Integer) $sale->getSeguro()->getPrecoTotal()) / 100, 2, '.', ''), //300.5
                "external_id" => $sale->getId(), //"12020"
                "installments" => $sale->getSeguro()->getNumeroParcelas(),
                "plan" => $sale->getSeguro()->getPlanoId(), //1
                "customer_document_number" => $sale->getConsumidor()->getCpf(), //"1234567890"
                "customer_birthdate" => DateTime::createFromFormat('dmY', $sale->getConsumidor()->getNascimento())->format('Y-m-d'), //"1987-09-27"
                "invoice_number" => $sale->getAparelho()->getNotaFiscal(), //"48502"
                "invoice_date" => DateTime::createFromFormat('dmY', $sale->getAparelho()->getDataCompra())->format('Y-m-d'), //"2018-05-22"
                "customer_name" => $sale->getConsumidor()->getNome(), //nomecliente
                "customer_address_street" => $sale->getConsumidor()->getEndereco()->getLogradouro(), //"Rua Marte"
                "customer_address_state" => $sale->getConsumidor()->getEndereco()->getUf(), //"MG"
                "customer_address_city" => $sale->getConsumidor()->getEndereco()->getCidade(), //"Poços de Caldas"
                "customer_address_complement" => $sale->getConsumidor()->getEndereco()->getComplemento(), //"casa 2"
                "customer_address_number" => $sale->getConsumidor()->getEndereco()->getNumero(), //"71"
                "customer_address_zipcode" => $sale->getConsumidor()->getEndereco()->getCep(), //"37713172"
                "customer_address_neighborhood" => $sale->getConsumidor()->getEndereco()->getBairro(), //"Centro"
                "device_value_paid" => number_format(((Integer) $sale->getAparelho()->getPreco()) / 100, 2, '.', ''), // 1390.90
                "used_device" => $sale->getAparelho()->getUsado() ? true : false, //"false"
            ],
        ];

        $response = $this->client->post('representant_orders', $order);
        $this->assertEquals(Response::HTTP_CREATED, $response->status());
        $this->assertArrayHasKey('payment_url', $response->json());
        $this->assertStringContainsString('representante/assinatura/', $response->json()['payment_url']);
    }

    // verificação de foto do aparelho via endpoint não será mais feita
    // public function testDevicePhotoVerificationIsSuccess()
    // {
    //     $orderId = 1322825;
    //     $data = json_decode('{
    //         "device_diagnostics": {
    //             "VOLUME_DOWN": 0,
    //             "VOLUME_UP": 0,
    //             "KEYCODE_BACK": 0,
    //             "HEADSET_PLUGGED": 0,
    //             "USB_CHARGE": 0,
    //             "AC_CHARGE": 1
    //         },
    //         "device_metadata": [
    //             {
    //                 "BOARD": "universal7870",
    //                 "BOOTLOADERTLOADER": "G610MUBU1AQB3",
    //                 "CPU_ABI": "armeabi-v7a",
    //                 "CPU_ABI2": "armeabi",
    //                 "DEVICE": "on7xelte",
    //                 "DISPLAY": "MMB29K.G610MUBU1AQB3",
    //                 "FINGERPRINT": "samsung/on7xelteub/on7xelte:6.0.1/MMB29K/G610MUBU1AQB3:user/release-keys",
    //                 "HARDWARE": "samsungexynos7870",
    //                 "HOST": "SWHC3813",
    //                 "ID": "MMB29K",
    //                 "MANUFACTURER": "samsung",
    //                 "MODEL": "SM-G610M",
    //                 "PRODUCT": "on7xelteub",
    //                 "SERIAL": "330022c36d17b303",
    //                 "USER": "dpi"
    //             },
    //             {
    //                 "DEVICE_ID": "354015085193761",
    //                 "PHONE_NUMBER": "+5519588976764",
    //                 "SOFTWARE_VERSION": "01",
    //                 "OPERATOR_NAME": "Oi",
    //                 "SIM_COUNTRY_CODE": "br",
    //                 "SIM_OPERATOR": "Oi",
    //                 "SIM_SERIAL_NO": "8955311929925234487",
    //                 "SUBSCRIBER_ID": "724311927467187",
    //                 "NETWORK_TYPE": "UMTS",
    //                 "PHONE_TYPE": "GSM"
    //             },
    //             {
    //                 "HEALTH": 2,
    //                 "ICON_SMALL": 17303454,
    //                 "LEVEL": 88,
    //                 "PLUGGED": 0,
    //                 "PRESENT": true,
    //                 "SCALE": 100,
    //                 "STATUS": 3,
    //                 "TECHNOLOGY": "Li-ion",
    //                 "TEMPERATURE": 234,
    //                 "VOLTAGE": 4192
    //             }
    //         ],
    //         "device_picture_urls": [
    //             "https://s3.amazonaws.com/foo/bar1.jpg",
    //             "https://s3.amazonaws.com/foo/bar2.jpg"
    //         ],
    //         "device_params": {
    //             "product_name": "Samsung SM-G610M",
    //             "imei": "353490005916877"
    //         }
    //     }', true);

    //     $response = $this->client->post('orders/' . $orderId . '/device_verification', $data);
    //     $this->assertEquals(Response::HTTP_CREATED, $response->status());
    // }

    public function testConsultSubscriptionByImeiIsSuccess()
    {
        $query = [
            'imei' => '354830098697248',
        ];

        $data = [
            "order" => [
                "id" => 1322825,
                "user_auth_token" => "-Py-WAyVy9gt6oZsP4Lf",
                "subscribed_at" => "27-04-2023",
                "active" => true,
                "payment_confirmed" => true,
                "device_verification_status" => "in_analysis",
                "imei" => "353490005916877",
                "plan_id" => 71,
                "plan_name" => 71,
                "store_external_id" => "644ad3105bcce3359003a3a2",
                "seller_code" => "",
                "seller_document_number" => "",
                "payment_method" => "financed",
                "installments" => 12,
                "device_value_paid" => 1390.9,
                "plan_price" => 874.8,
                "invoice_number" => "123456",
                "invoice_date" => "22-03-2023",
                "customer_document_number" => "68699897537",
                "customer_name" => "enzo paiva teste",
                "customer_email" => "e.paiva@vertex.vertexdigital.co",
                "customer_phonenumber" => "21991976858",
                "customer_birthdate" => "2000-06-10",
                "customer_address_street" => "rua x",
                "customer_address_state" => "MS",
                "customer_address_city" => "campo grande",
                "customer_address_complement" => "teste",
                "customer_address_number" => "44",
                "customer_address_zipcode" => "79050620",
                "customer_address_neighborhood" => "Ieda",
                "product_model" => "Apple iPhone 8 Plus 64GB",
                "product_eans" => [
                    "190198454348",
                    "190198746054",
                    "190198454654",
                    "19019845139",
                    "190198454706",
                    "190198453976",
                ],
                "product_tim_codes" => [
                    "4012371",
                    "4012369",
                    "4012604",
                    "4012982",
                    "4013000",
                ],
            ],
        ];

        $response = $this->client->get('orders/search', $query);

        $this->assertEquals(Response::HTTP_OK, $response->status());
        $this->assertEquals($data, $response->json());
    }

    public function testConsultTermSignatureIsSuccess()
    {
        $response = $this->client->get('/service_terms/1322825', []);

        $this->assertEquals(Response::HTTP_OK, $response->status());
    }

    public function testConsultTermSignatureCancelIsSuccess()
    {
        $response = $this->client->get('/cancellation_term/1322872.pdf', []);

        $this->assertEquals(Response::HTTP_OK, $response->status());
    }

    public function testSignatureNotCancelIsSuccess()
    {
        $response = $this->client->delete('/orders/1322825', []);

        $this->assertEquals(Response::HTTP_CONFLICT, $response->status());
    }

    public function testSignatureCancelIsSuccess()
    {
        $response = $this->client->delete('/orders/1322872', []);

        $this->assertEquals(Response::HTTP_OK, $response->status());
    }
}
