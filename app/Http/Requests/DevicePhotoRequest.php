<?php

namespace App\Http\Requests;

use Pearl\RequestValidate\RequestAbstract;

class DevicePhotoRequest extends RequestAbstract
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'device_diagnostics.VOLUME_DOWN' => 'required|integer',
            'device_diagnostics.VOLUME_UP' => 'required|integer',
            'device_diagnostics.KEYCODE_BACK' => 'required|integer',
            'device_diagnostics.HEADSET_PLUGGED' => 'required|integer',
            'device_diagnostics.USB_CHARGE' => 'required|integer',
            'device_diagnostics.AC_CHARGE' => 'required|integer',
            'device_metadata.0.BOARD' => 'required|string',
            'device_metadata.0.BOOTLOADERTLOADER' => 'required|string',
            'device_metadata.0.CPU_ABI' => 'required|string',
            'device_metadata.0.CPU_ABI2' => 'required|string',
            'device_metadata.0.DEVICE' => 'required|string',
            'device_metadata.0.DISPLAY' => 'required|string',
            'device_metadata.0.FINGERPRINT' => 'required|string',
            'device_metadata.0.HARDWARE' => 'required|string',
            'device_metadata.0.HOST' => 'required|string',
            'device_metadata.0.ID' => 'required|string',
            'device_metadata.0.MANUFACTURER' => 'required|string',
            'device_metadata.0.MODEL' => 'required|string',
            'device_metadata.0.PRODUCT' => 'required|string',
            'device_metadata.0.SERIAL' => 'required|string',
            'device_metadata.0.USER' => 'required|string',
            'device_metadata.1.DEVICE_ID' => 'required|string',
            'device_metadata.1.PHONE_NUMBER' => 'required|string',
            'device_metadata.1.SOFTWARE_VERSION' => 'required|string',
            'device_metadata.1.OPERATOR_NAME' => 'required|string',
            'device_metadata.1.SIM_COUNTRY_CODE' => 'required|string',
            'device_metadata.1.SIM_OPERATOR' => 'required|string',
            'device_metadata.1.SIM_SERIAL_NO' => 'required|string',
            'device_metadata.1.SUBSCRIBER_ID' => 'required|string',
            'device_metadata.1.NETWORK_TYPE' => 'required|string',
            'device_metadata.1.PHONE_TYPE' => 'required|string',
            'device_metadata.2.HEALTH' => 'required|integer',
            'device_metadata.2.ICON_SMALL' => 'required|integer',
            'device_metadata.2.LEVEL' => 'required|integer',
            'device_metadata.2.PLUGGED' => 'required|integer',
            'device_metadata.2.PRESENT' => 'required|boolean',
            'device_metadata.2.SCALE' => 'required|integer',
            'device_metadata.2.STATUS' => 'required|integer',
            'device_metadata.2.TECHNOLOGY' => 'required|string',
            'device_metadata.2.TEMPERATURE' => 'required|integer',
            'device_metadata.2.VOLTAGE' => 'required|integer',
            'device_picture_urls.*' => 'required|url',
            'device_params.product_name' => 'required|string',
            'device_params.imei' => 'required|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'device_diagnostics.VOLUME_DOWN' => 'device_diagnostics.VOLUME_DOWN',
            'device_diagnostics.VOLUME_UP' => 'device_diagnostics.VOLUME_UP',
            'device_diagnostics.KEYCODE_BACK' => 'device_diagnostics.KEYCODE_BACK',
            'device_diagnostics.HEADSET_PLUGGED' => 'device_diagnostics.HEADSET_PLUGGED',
            'device_diagnostics.USB_CHARGE' => 'device_diagnostics.USB_CHARGE',
            'device_diagnostics.AC_CHARGE' => 'device_diagnostics.AC_CHARGE',
            'device_metadata.0.BOARD' => 'device_metadata.0.BOARD',
            'device_metadata.0.BOOTLOADERTLOADER' => 'device_metadata.0.BOOTLOADERTLOADER',
            'device_metadata.0.CPU_ABI' => 'device_metadata.0.CPU_ABI',
            'device_metadata.0.CPU_ABI2' => 'device_metadata.0.CPU_ABI2',
            'device_metadata.0.DEVICE' => 'device_metadata.0.DEVICE',
            'device_metadata.0.DISPLAY' => 'device_metadata.0.DISPLAY',
            'device_metadata.0.FINGERPRINT' => 'device_metadata.0.FINGERPRINT',
            'device_metadata.0.HARDWARE' => 'device_metadata.0.HARDWARE',
            'device_metadata.0.HOST' => 'device_metadata.0.HOST',
            'device_metadata.0.ID' => 'device_metadata.0.ID',
            'device_metadata.0.MANUFACTURER' => 'device_metadata.0.MANUFACTURER',
            'device_metadata.0.MODEL' => 'device_metadata.0.MODEL',
            'device_metadata.0.PRODUCT' => 'device_metadata.0.PRODUCT',
            'device_metadata.0.SERIAL' => 'device_metadata.0.SERIAL',
            'device_metadata.0.USER' => 'device_metadata.0.USER',
            'device_metadata.1.DEVICE_ID' => 'device_metadata.1.DEVICE_ID',
            'device_metadata.1.PHONE_NUMBER' => 'device_metadata.1.PHONE_NUMBER',
            'device_metadata.1.SOFTWARE_VERSION' => 'device_metadata.1.SOFTWARE_VERSION',
            'device_metadata.1.OPERATOR_NAME' => 'device_metadata.1.OPERATOR_NAME',
            'device_metadata.1.SIM_COUNTRY_CODE' => 'device_metadata.1.SIM_COUNTRY_CODE',
            'device_metadata.1.SIM_OPERATOR' => 'device_metadata.1.SIM_OPERATOR',
            'device_metadata.1.SIM_SERIAL_NO' => 'device_metadata.1.SIM_SERIAL_NO',
            'device_metadata.1.SUBSCRIBER_ID' => 'device_metadata.1.SUBSCRIBER_ID',
            'device_metadata.1.NETWORK_TYPE' => 'device_metadata.1.NETWORK_TYPE',
            'device_metadata.1.PHONE_TYPE' => 'device_metadata.1.PHONE_TYPE',
            'device_metadata.2.HEALTH' => 'device_metadata.2.HEALTH',
            'device_metadata.2.ICON_SMALL' => 'device_metadata.2.ICON_SMALL',
            'device_metadata.2.LEVEL' => 'device_metadata.2.LEVEL',
            'device_metadata.2.PLUGGED' => 'device_metadata.2.PLUGGED',
            'device_metadata.2.PRESENT' => 'device_metadata.2.PRESENT',
            'device_metadata.2.SCALE' => 'device_metadata.2.SCALE',
            'device_metadata.2.STATUS' => 'device_metadata.2.STATUS',
            'device_metadata.2.TECHNOLOGY' => 'device_metadata.2.TECHNOLOGY',
            'device_metadata.2.TEMPERATURE' => 'device_metadata.2.TEMPERATURE',
            'device_metadata.2.VOLTAGE' => 'device_metadata.2.VOLTAGE',
            'device_picture_urls.*' => 'device_picture_urls',
            'device_params.product_name' => 'device_params.product_name',
            'device_params.imei' => 'device_params.imei',
        ];
    }

    public function isPrecognitive()
    {
        // Método vazio, apenas para satisfazer a exigência do pacote
    }

    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'integer' => 'O campo :attribute deve ser um número inteiro.',
            'string' => 'O campo :attribute deve ser uma string.',
            'boolean' => 'O campo :attribute deve ser true ou false.',
        ];
    }
}
