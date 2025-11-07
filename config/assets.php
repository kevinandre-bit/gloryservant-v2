<?php

return [
    // Toggle to use CDN vs local self-hosted vendor assets
    'use_cdn' => env('ASSET_USE_CDN', true),

    // Versions (for documentation/reference only)
    'versions' => [
        'flatpickr'   => '4.x',
        'html5_qrcode'=> '2.3.7',
        'cropperjs'   => '1.6.2',
    ],

    // CDN URLs
    'cdn' => [
        'flatpickr_js'   => 'https://cdn.jsdelivr.net/npm/flatpickr',
        'flatpickr_css'  => 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css',
        'html5_qrcode'   => 'https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js',
        'cropper_js'     => 'https://unpkg.com/cropperjs@1.6.2/dist/cropper.min.js',
        'cropper_css'    => 'https://unpkg.com/cropperjs@1.6.2/dist/cropper.min.css',
    ],

    // Local paths (place vendor files here to self-host)
    'local' => [
        // Use existing plugins directory for self-hosted vendor assets
        'flatpickr_js'   => 'assets3/plugins/flatpickr.min.js',
        'flatpickr_css'  => 'assets3/plugins/flatpickr.min.css',
        'html5_qrcode'   => 'assets3/plugins/html5-qrcode.min.js',
        'cropper_js'     => 'assets3/plugins/cropper.min.js',
        'cropper_css'    => 'assets3/plugins/cropper.min.css',
    ],
];
