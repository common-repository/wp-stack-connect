<?php

namespace WPStack_Connect_Vendor\YahnisElsts\PluginUpdateChecker\v5p1;

use WPStack_Connect_Vendor\YahnisElsts\PluginUpdateChecker\v5\PucFactory as MajorFactory;
use WPStack_Connect_Vendor\YahnisElsts\PluginUpdateChecker\v5p1\PucFactory as MinorFactory;
require __DIR__ . '/Puc/v5p1/Autoloader.php';
new \WPStack_Connect_Vendor\YahnisElsts\PluginUpdateChecker\v5p1\Autoloader();
require __DIR__ . '/Puc/v5p1/PucFactory.php';
require __DIR__ . '/Puc/v5/PucFactory.php';
//Register classes defined in this version with the factory.
foreach (array('WPStack_Connect_Vendor\\Plugin\\UpdateChecker' => \WPStack_Connect_Vendor\YahnisElsts\PluginUpdateChecker\v5p1\Plugin\UpdateChecker::class, 'WPStack_Connect_Vendor\\Theme\\UpdateChecker' => \WPStack_Connect_Vendor\YahnisElsts\PluginUpdateChecker\v5p1\Theme\UpdateChecker::class, 'WPStack_Connect_Vendor\\Vcs\\PluginUpdateChecker' => \WPStack_Connect_Vendor\YahnisElsts\PluginUpdateChecker\v5p1\Vcs\PluginUpdateChecker::class, 'WPStack_Connect_Vendor\\Vcs\\ThemeUpdateChecker' => \WPStack_Connect_Vendor\YahnisElsts\PluginUpdateChecker\v5p1\Vcs\ThemeUpdateChecker::class, 'GitHubApi' => \WPStack_Connect_Vendor\YahnisElsts\PluginUpdateChecker\v5p1\Vcs\GitHubApi::class, 'BitBucketApi' => \WPStack_Connect_Vendor\YahnisElsts\PluginUpdateChecker\v5p1\Vcs\BitBucketApi::class, 'GitLabApi' => \WPStack_Connect_Vendor\YahnisElsts\PluginUpdateChecker\v5p1\Vcs\GitLabApi::class) as $pucGeneralClass => $pucVersionedClass) {
    \WPStack_Connect_Vendor\YahnisElsts\PluginUpdateChecker\v5\PucFactory::addVersion($pucGeneralClass, $pucVersionedClass, '5.1');
    //Also add it to the minor-version factory in case the major-version factory
    //was already defined by another, older version of the update checker.
    \WPStack_Connect_Vendor\YahnisElsts\PluginUpdateChecker\v5p1\PucFactory::addVersion($pucGeneralClass, $pucVersionedClass, '5.1');
}
