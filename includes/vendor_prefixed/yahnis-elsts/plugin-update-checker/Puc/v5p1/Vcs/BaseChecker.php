<?php

namespace WPStack_Connect_Vendor\YahnisElsts\PluginUpdateChecker\v5p1\Vcs;

if (!\interface_exists(\WPStack_Connect_Vendor\YahnisElsts\PluginUpdateChecker\v5p1\Vcs\BaseChecker::class, \false)) {
    interface BaseChecker
    {
        /**
         * Set the repository branch to use for updates. Defaults to 'master'.
         *
         * @param string $branch
         * @return $this
         */
        public function setBranch($branch);
        /**
         * Set authentication credentials.
         *
         * @param array|string $credentials
         * @return $this
         */
        public function setAuthentication($credentials);
        /**
         * @return Api
         */
        public function getVcsApi();
    }
}
