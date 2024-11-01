<?php

namespace WPStack_Connect_Vendor\Aws\S3\Crypto;

use WPStack_Connect_Vendor\Aws\Crypto\MaterialsProviderInterfaceV2;
trait CryptoParamsTraitV2
{
    use CryptoParamsTrait;
    protected function getMaterialsProvider(array $args)
    {
        if ($args['@MaterialsProvider'] instanceof \WPStack_Connect_Vendor\Aws\Crypto\MaterialsProviderInterfaceV2) {
            return $args['@MaterialsProvider'];
        }
        throw new \InvalidArgumentException('An instance of MaterialsProviderInterfaceV2' . ' must be passed in the "MaterialsProvider" field.');
    }
}
