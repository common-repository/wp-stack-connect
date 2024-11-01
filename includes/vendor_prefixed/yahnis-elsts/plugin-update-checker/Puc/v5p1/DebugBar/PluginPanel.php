<?php

namespace WPStack_Connect_Vendor\YahnisElsts\PluginUpdateChecker\v5p1\DebugBar;

use WPStack_Connect_Vendor\YahnisElsts\PluginUpdateChecker\v5p1\Plugin\UpdateChecker;
if (!\class_exists(\WPStack_Connect_Vendor\YahnisElsts\PluginUpdateChecker\v5p1\DebugBar\PluginPanel::class, \false)) {
    class PluginPanel extends \WPStack_Connect_Vendor\YahnisElsts\PluginUpdateChecker\v5p1\DebugBar\Panel
    {
        /**
         * @var UpdateChecker
         */
        protected $updateChecker;
        protected function displayConfigHeader()
        {
            $this->row('Plugin file', \htmlentities($this->updateChecker->pluginFile));
            parent::displayConfigHeader();
        }
        protected function getMetadataButton()
        {
            $requestInfoButton = '';
            if (\function_exists('WPStack_Connect_Vendor\\get_submit_button')) {
                $requestInfoButton = get_submit_button('Request Info', 'secondary', 'puc-request-info-button', \false, array('id' => $this->updateChecker->getUniqueName('request-info-button')));
            }
            return $requestInfoButton;
        }
        protected function getUpdateFields()
        {
            return \array_merge(parent::getUpdateFields(), array('homepage', 'upgrade_notice', 'tested'));
        }
    }
}
