<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WPStack_Connect_Vendor\Symfony\Component\Console\Command;

use WPStack_Connect_Vendor\Symfony\Component\Console\Helper\DescriptorHelper;
use WPStack_Connect_Vendor\Symfony\Component\Console\Input\InputArgument;
use WPStack_Connect_Vendor\Symfony\Component\Console\Input\InputDefinition;
use WPStack_Connect_Vendor\Symfony\Component\Console\Input\InputInterface;
use WPStack_Connect_Vendor\Symfony\Component\Console\Input\InputOption;
use WPStack_Connect_Vendor\Symfony\Component\Console\Output\OutputInterface;
/**
 * ListCommand displays the list of all available commands for the application.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ListCommand extends \WPStack_Connect_Vendor\Symfony\Component\Console\Command\Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('list')->setDefinition($this->createDefinition())->setDescription('List commands')->setHelp(<<<'EOF'
The <info>%command.name%</info> command lists all commands:

  <info>php %command.full_name%</info>

You can also display the commands for a specific namespace:

  <info>php %command.full_name% test</info>

You can also output the information in other formats by using the <comment>--format</comment> option:

  <info>php %command.full_name% --format=xml</info>

It's also possible to get raw list of commands (useful for embedding command runner):

  <info>php %command.full_name% --raw</info>
EOF
);
    }
    /**
     * {@inheritdoc}
     */
    public function getNativeDefinition()
    {
        return $this->createDefinition();
    }
    /**
     * {@inheritdoc}
     */
    protected function execute(\WPStack_Connect_Vendor\Symfony\Component\Console\Input\InputInterface $input, \WPStack_Connect_Vendor\Symfony\Component\Console\Output\OutputInterface $output)
    {
        $helper = new \WPStack_Connect_Vendor\Symfony\Component\Console\Helper\DescriptorHelper();
        $helper->describe($output, $this->getApplication(), ['format' => $input->getOption('format'), 'raw_text' => $input->getOption('raw'), 'namespace' => $input->getArgument('namespace')]);
        return 0;
    }
    private function createDefinition() : \WPStack_Connect_Vendor\Symfony\Component\Console\Input\InputDefinition
    {
        return new \WPStack_Connect_Vendor\Symfony\Component\Console\Input\InputDefinition([new \WPStack_Connect_Vendor\Symfony\Component\Console\Input\InputArgument('namespace', \WPStack_Connect_Vendor\Symfony\Component\Console\Input\InputArgument::OPTIONAL, 'The namespace name'), new \WPStack_Connect_Vendor\Symfony\Component\Console\Input\InputOption('raw', null, \WPStack_Connect_Vendor\Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'To output raw command list'), new \WPStack_Connect_Vendor\Symfony\Component\Console\Input\InputOption('format', null, \WPStack_Connect_Vendor\Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'The output format (txt, xml, json, or md)', 'txt')]);
    }
}
