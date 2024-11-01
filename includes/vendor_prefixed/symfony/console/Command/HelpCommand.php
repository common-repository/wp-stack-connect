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
use WPStack_Connect_Vendor\Symfony\Component\Console\Input\InputInterface;
use WPStack_Connect_Vendor\Symfony\Component\Console\Input\InputOption;
use WPStack_Connect_Vendor\Symfony\Component\Console\Output\OutputInterface;
/**
 * HelpCommand displays the help for a given command.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class HelpCommand extends \WPStack_Connect_Vendor\Symfony\Component\Console\Command\Command
{
    private $command;
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->ignoreValidationErrors();
        $this->setName('help')->setDefinition([new \WPStack_Connect_Vendor\Symfony\Component\Console\Input\InputArgument('command_name', \WPStack_Connect_Vendor\Symfony\Component\Console\Input\InputArgument::OPTIONAL, 'The command name', 'help'), new \WPStack_Connect_Vendor\Symfony\Component\Console\Input\InputOption('format', null, \WPStack_Connect_Vendor\Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'The output format (txt, xml, json, or md)', 'txt'), new \WPStack_Connect_Vendor\Symfony\Component\Console\Input\InputOption('raw', null, \WPStack_Connect_Vendor\Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'To output raw command help')])->setDescription('Display help for a command')->setHelp(<<<'EOF'
The <info>%command.name%</info> command displays help for a given command:

  <info>php %command.full_name% list</info>

You can also output the help in other formats by using the <comment>--format</comment> option:

  <info>php %command.full_name% --format=xml list</info>

To display the list of available commands, please use the <info>list</info> command.
EOF
);
    }
    public function setCommand(\WPStack_Connect_Vendor\Symfony\Component\Console\Command\Command $command)
    {
        $this->command = $command;
    }
    /**
     * {@inheritdoc}
     */
    protected function execute(\WPStack_Connect_Vendor\Symfony\Component\Console\Input\InputInterface $input, \WPStack_Connect_Vendor\Symfony\Component\Console\Output\OutputInterface $output)
    {
        if (null === $this->command) {
            $this->command = $this->getApplication()->find($input->getArgument('command_name'));
        }
        $helper = new \WPStack_Connect_Vendor\Symfony\Component\Console\Helper\DescriptorHelper();
        $helper->describe($output, $this->command, ['format' => $input->getOption('format'), 'raw_text' => $input->getOption('raw')]);
        $this->command = null;
        return 0;
    }
}
