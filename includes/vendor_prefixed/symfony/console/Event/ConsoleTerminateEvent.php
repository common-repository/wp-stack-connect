<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WPStack_Connect_Vendor\Symfony\Component\Console\Event;

use WPStack_Connect_Vendor\Symfony\Component\Console\Command\Command;
use WPStack_Connect_Vendor\Symfony\Component\Console\Input\InputInterface;
use WPStack_Connect_Vendor\Symfony\Component\Console\Output\OutputInterface;
/**
 * Allows to manipulate the exit code of a command after its execution.
 *
 * @author Francesco Levorato <git@flevour.net>
 *
 * @final since Symfony 4.4
 */
class ConsoleTerminateEvent extends \WPStack_Connect_Vendor\Symfony\Component\Console\Event\ConsoleEvent
{
    private $exitCode;
    public function __construct(\WPStack_Connect_Vendor\Symfony\Component\Console\Command\Command $command, \WPStack_Connect_Vendor\Symfony\Component\Console\Input\InputInterface $input, \WPStack_Connect_Vendor\Symfony\Component\Console\Output\OutputInterface $output, int $exitCode)
    {
        parent::__construct($command, $input, $output);
        $this->setExitCode($exitCode);
    }
    /**
     * Sets the exit code.
     *
     * @param int $exitCode The command exit code
     */
    public function setExitCode($exitCode)
    {
        $this->exitCode = (int) $exitCode;
    }
    /**
     * Gets the exit code.
     *
     * @return int The command exit code
     */
    public function getExitCode()
    {
        return $this->exitCode;
    }
}
