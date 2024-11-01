<?php

namespace WPStack_Connect_Vendor\YahnisElsts\PluginUpdateChecker\v5p1\DebugBar;

use WPStack_Connect_Vendor\YahnisElsts\PluginUpdateChecker\v5p1\Theme\UpdateChecker;
if (!\class_exists(\WPStack_Connect_Vendor\YahnisElsts\PluginUpdateChecker\v5p1\DebugBar\ThemePanel::class, \false)) {
    class ThemePanel extends \WPStack_Connect_Vendor\YahnisElsts\PluginUpdateChecker\v5p1\DebugBar\Panel
    {
        /**
         * @var UpdateChecker
         */
        protected $updateChecker;
        protected function displayConfigHeader()
        {
            $this->row('Theme directory', \htmlentities($this->updateChecker->directoryName));
            parent::displayConfigHeader();
        }
        protected function getUpdateFields()
        {
            return \array_merge(parent::getUpdateFields(), array('details_url'));
        }
    }
}
