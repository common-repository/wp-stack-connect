<?php

namespace WPStack_Connect_Vendor\Aws\S3\Crypto;

use WPStack_Connect_Vendor\Aws\Crypto\MaterialsProvider;
use WPStack_Connect_Vendor\Aws\Crypto\MetadataEnvelope;
use WPStack_Connect_Vendor\Aws\Crypto\MetadataStrategyInterface;
trait CryptoParamsTrait
{
    protected function getMaterialsProvider(array $args)
    {
        if ($args['@MaterialsProvider'] instanceof \WPStack_Connect_Vendor\Aws\Crypto\MaterialsProvider) {
            return $args['@MaterialsProvider'];
        }
        throw new \InvalidArgumentException('An instance of MaterialsProvider' . ' must be passed in the "MaterialsProvider" field.');
    }
    protected function getInstructionFileSuffix(array $args)
    {
        return !empty($args['@InstructionFileSuffix']) ? $args['@InstructionFileSuffix'] : $this->instructionFileSuffix;
    }
    protected function determineGetObjectStrategy($result, $instructionFileSuffix)
    {
        if (isset($result['Metadata'][\WPStack_Connect_Vendor\Aws\Crypto\MetadataEnvelope::CONTENT_KEY_V2_HEADER])) {
            return new \WPStack_Connect_Vendor\Aws\S3\Crypto\HeadersMetadataStrategy();
        }
        return new \WPStack_Connect_Vendor\Aws\S3\Crypto\InstructionFileMetadataStrategy($this->client, $instructionFileSuffix);
    }
    protected function getMetadataStrategy(array $args, $instructionFileSuffix)
    {
        if (!empty($args['@MetadataStrategy'])) {
            if ($args['@MetadataStrategy'] instanceof \WPStack_Connect_Vendor\Aws\Crypto\MetadataStrategyInterface) {
                return $args['@MetadataStrategy'];
            }
            if (\is_string($args['@MetadataStrategy'])) {
                switch ($args['@MetadataStrategy']) {
                    case \WPStack_Connect_Vendor\Aws\S3\Crypto\HeadersMetadataStrategy::class:
                        return new \WPStack_Connect_Vendor\Aws\S3\Crypto\HeadersMetadataStrategy();
                    case \WPStack_Connect_Vendor\Aws\S3\Crypto\InstructionFileMetadataStrategy::class:
                        return new \WPStack_Connect_Vendor\Aws\S3\Crypto\InstructionFileMetadataStrategy($this->client, $instructionFileSuffix);
                    default:
                        throw new \InvalidArgumentException('Could not match the' . ' specified string in "MetadataStrategy" to a' . ' predefined strategy.');
                }
            } else {
                throw new \InvalidArgumentException('The metadata strategy that' . ' was passed to "MetadataStrategy" was unrecognized.');
            }
        } elseif ($instructionFileSuffix) {
            return new \WPStack_Connect_Vendor\Aws\S3\Crypto\InstructionFileMetadataStrategy($this->client, $instructionFileSuffix);
        }
        return null;
    }
}
