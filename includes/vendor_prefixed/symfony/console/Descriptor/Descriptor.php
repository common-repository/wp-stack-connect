<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WPStack_Connect_Vendor\Symfony\Component\Console\Descriptor;

use WPStack_Connect_Vendor\Symfony\Component\Console\Application;
use WPStack_Connect_Vendor\Symfony\Component\Console\Command\Command;
use WPStack_Connect_Vendor\Symfony\Component\Console\Exception\InvalidArgumentException;
use WPStack_Connect_Vendor\Symfony\Component\Console\Input\InputArgument;
use WPStack_Connect_Vendor\Symfony\Component\Console\Input\InputDefinition;
use WPStack_Connect_Vendor\Symfony\Component\Console\Input\InputOption;
use WPStack_Connect_Vendor\Symfony\Component\Console\Output\OutputInterface;
/**
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 *
 * @internal
 */
abstract class Descriptor implements \WPStack_Connect_Vendor\Symfony\Component\Console\Descriptor\DescriptorInterface
{
    /**
     * @var OutputInterface
     */
    protected $output;
    /**
     * {@inheritdoc}
     */
    public function describe(\WPStack_Connect_Vendor\Symfony\Component\Console\Output\OutputInterface $output, $object, array $options = [])
    {
        $this->output = $output;
        switch (\true) {
            case $object instanceof \WPStack_Connect_Vendor\Symfony\Component\Console\Input\InputArgument:
                $this->describeInputArgument($object, $options);
                break;
            case $object instanceof \WPStack_Connect_Vendor\Symfony\Component\Console\Input\InputOption:
                $this->describeInputOption($object, $options);
                break;
            case $object instanceof \WPStack_Connect_Vendor\Symfony\Component\Console\Input\InputDefinition:
                $this->describeInputDefinition($object, $options);
                break;
            case $object instanceof \WPStack_Connect_Vendor\Symfony\Component\Console\Command\Command:
                $this->describeCommand($object, $options);
                break;
            case $object instanceof \WPStack_Connect_Vendor\Symfony\Component\Console\Application:
                $this->describeApplication($object, $options);
                break;
            default:
                throw new \WPStack_Connect_Vendor\Symfony\Component\Console\Exception\InvalidArgumentException(\sprintf('Object of type "%s" is not describable.', \get_class($object)));
        }
    }
    /**
     * Writes content to output.
     *
     * @param string $content
     * @param bool   $decorated
     */
    protected function write($content, $decorated = \false)
    {
        $this->output->write($content, \false, $decorated ? \WPStack_Connect_Vendor\Symfony\Component\Console\Output\OutputInterface::OUTPUT_NORMAL : \WPStack_Connect_Vendor\Symfony\Component\Console\Output\OutputInterface::OUTPUT_RAW);
    }
    /**
     * Describes an InputArgument instance.
     */
    protected abstract function describeInputArgument(\WPStack_Connect_Vendor\Symfony\Component\Console\Input\InputArgument $argument, array $options = []);
    /**
     * Describes an InputOption instance.
     */
    protected abstract function describeInputOption(\WPStack_Connect_Vendor\Symfony\Component\Console\Input\InputOption $option, array $options = []);
    /**
     * Describes an InputDefinition instance.
     */
    protected abstract function describeInputDefinition(\WPStack_Connect_Vendor\Symfony\Component\Console\Input\InputDefinition $definition, array $options = []);
    /**
     * Describes a Command instance.
     */
    protected abstract function describeCommand(\WPStack_Connect_Vendor\Symfony\Component\Console\Command\Command $command, array $options = []);
    /**
     * Describes an Application instance.
     */
    protected abstract function describeApplication(\WPStack_Connect_Vendor\Symfony\Component\Console\Application $application, array $options = []);
}
