<?php
/**
 * lib-ftp-ssh server tester
 * @package lib-ftp-ssh
 * @version 0.0.1
 */

namespace LibFtpSsh\Server;

class PHP
{
    static function ssh2(){
        return [
            'success' => function_exists('ssh2_connect'),
            'info' => ''
        ];
    }
}