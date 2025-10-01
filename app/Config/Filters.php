<?php

namespace Config;

use CodeIgniter\Config\Filters as BaseFilters;
use CodeIgniter\Filters\Cors;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\ForceHTTPS;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\PageCache;
use CodeIgniter\Filters\PerformanceMetrics;
use CodeIgniter\Filters\SecureHeaders;
use CodeIgniter\Config\BaseConfig;

class Filters extends BaseConfig
{
    // ... (property $aliases)
    public $aliases = [
        'csrf'     => CSRF::class,
        'toolbar'  => DebugToolbar::class,
        'honeypot' => Honeypot::class,
        
        // Pendaftaran Filter Baru
        'auth'     => \App\Filters\AuthFilter::class,
        'gudang'   => \App\Filters\GudangFilter::class,
        'dapur'    => \App\Filters\DapurFilter::class,
    ];
    // ...

    // Penerapan Filter Global dan Groups
    public $globals = [
        'before' => [
            // 'honeypot',
            // 'csrf',
        ],
        'after' => [
            'toolbar',
            // 'honeypot',
        ],
    ];

    public $methods = [];

    // PENERAPAN UTAMA DI SINI
    public $filters = [
        'gudang' => [
            'before' => [
                // Terapkan filter Gudang ke semua Controller Gudang
                'gudang/*',
            ]
        ],
        'dapur' => [
            'before' => [
                // Terapkan filter Dapur ke semua Controller Dapur
                'dapur/*',
            ]
        ],
        'auth' => [
            'before' => [
                // Terapkan filter Auth (wajib login) ke semua Controller yang terproteksi
                'gudang/*',
                'dapur/*',
            ]
        ],
    ];
}
