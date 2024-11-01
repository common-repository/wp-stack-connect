<?php

namespace WPStack_Connect_Vendor;

// Don't redefine the functions if included multiple times.
if (!\function_exists('WPStack_Connect_Vendor\\GuzzleHttp\\Promise\\promise_for')) {
    require __DIR__ . '/functions.php';
}
