<?php

namespace App\Exceptions;

use Exception;

class ApiLeadException extends Exception
{
    protected $message;
    protected $response;

    public function __construct($message, int $code, array $response = null)
    {
        parent::__construct($message, $code);

        $this->response = $response;
    }

    public function render($request)
    {
        // message default
        $return = ['mensagem' => 'Erro ao realizar solicitação'];

        // custom message
        if (!empty($this->message)) {
            $return = ['mensagem' => $this->message];
        }

        // custom response
        if (!empty($this->response)) {
            $return = $this->response;
        }

        return response()->json($return, $this->getCode());
    }
}
