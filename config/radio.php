<?php
return [
    'content_root'    => env('RADIO_CONTENT_ROOT', 'D:\\Radio\\Content'), // Windows example
    'export_delimiter'=> env('RADIO_EXPORT_DELIMITER', ','),
    'export_dir'      => env('RADIO_EXPORT_DIR', 'playlist_exports'),
    'xplayout_auto_path' => env('XPLAYOUT_AUTO_PATH', 'I:\Playlist'),

    // OPTIONAL: SFTP push to playout inbox
    'sftp' => [
        'enabled'   => env('RADIO_SFTP_ENABLED', false),
        'host'      => env('RADIO_SFTP_HOST'),
        'port'      => env('RADIO_SFTP_PORT', 22),
        'username'  => env('RADIO_SFTP_USERNAME'),
        'password'  => env('RADIO_SFTP_PASSWORD'),
        'remote_dir'=> env('RADIO_SFTP_REMOTE_DIR', '/inbox'),
    ],
];
