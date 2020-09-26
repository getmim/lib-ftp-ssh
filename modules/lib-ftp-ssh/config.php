<?php

return [
    '__name' => 'lib-ftp-ssh',
    '__version' => '0.0.2',
    '__git' => 'git@github.com:getmim/lib-ftp-ssh.git',
    '__license' => 'MIT',
    '__author' => [
        'name' => 'Iqbal Fauzi',
        'email' => 'iqbalfawz@gmail.com',
        'website' => 'http://iqbalfn.com/'
    ],
    '__files' => [
        'modules/lib-ftp-ssh' => ['install','update','remove']
    ],
    '__dependencies' => [
        'required' => [
            [
                'lib-ftp' => NULL
            ]
        ],
        'optional' => []
    ],
    'autoload' => [
        'classes' => [
            'LibFtpSsh\\Handler' => [
                'type' => 'file',
                'base' => 'modules/lib-ftp-ssh/handler'
            ],
            'LibFtpSsh\\Server' => [
                'type' => 'file',
                'base' => 'modules/lib-ftp-ssh/server'
            ]
        ],
        'files' => []
    ],
    'server' => [
        'lib-ftp-ssh' => [
            'SSH2' => 'LibFtpSsh\\Server\\PHP::ssh2'
        ]
    ],
    'libFtp' => [
        'handlers' => [
            'sftp' => 'LibFtpSsh\\Handler\\Sftp'
        ]
    ]
];
