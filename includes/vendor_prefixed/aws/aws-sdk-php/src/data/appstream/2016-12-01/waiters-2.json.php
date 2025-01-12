<?php

namespace WPStack_Connect_Vendor;

// This file was auto-generated from sdk-root/src/data/appstream/2016-12-01/waiters-2.json
return ['version' => 2, 'waiters' => ['FleetStarted' => ['delay' => 30, 'maxAttempts' => 40, 'operation' => 'DescribeFleets', 'acceptors' => [['state' => 'success', 'matcher' => 'pathAll', 'argument' => 'Fleets[].State', 'expected' => 'RUNNING'], ['state' => 'failure', 'matcher' => 'pathAny', 'argument' => 'Fleets[].State', 'expected' => 'STOPPING'], ['state' => 'failure', 'matcher' => 'pathAny', 'argument' => 'Fleets[].State', 'expected' => 'STOPPED']]], 'FleetStopped' => ['delay' => 30, 'maxAttempts' => 40, 'operation' => 'DescribeFleets', 'acceptors' => [['state' => 'success', 'matcher' => 'pathAll', 'argument' => 'Fleets[].State', 'expected' => 'STOPPED'], ['state' => 'failure', 'matcher' => 'pathAny', 'argument' => 'Fleets[].State', 'expected' => 'STARTING'], ['state' => 'failure', 'matcher' => 'pathAny', 'argument' => 'Fleets[].State', 'expected' => 'RUNNING']]]]];
