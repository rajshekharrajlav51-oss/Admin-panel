<?php return array (
  'broadcasting' => 
  array (
    'default' => 'sync',
    'connections' => 
    array (
      'reverb' => 
      array (
        'driver' => 'reverb',
        'key' => NULL,
        'secret' => NULL,
        'app_id' => NULL,
        'options' => 
        array (
          'host' => NULL,
          'port' => 443,
          'scheme' => 'https',
          'useTLS' => true,
        ),
        'client_options' => 
        array (
        ),
      ),
      'pusher' => 
      array (
        'driver' => 'pusher',
        'key' => NULL,
        'secret' => NULL,
        'app_id' => NULL,
        'options' => 
        array (
          'cluster' => NULL,
          'host' => 'api-mt1.pusher.com',
          'port' => 443,
          'scheme' => 'https',
          'encrypted' => true,
          'useTLS' => true,
        ),
        'client_options' => 
        array (
        ),
      ),
      'ably' => 
      array (
        'driver' => 'ably',
        'key' => NULL,
      ),
      'log' => 
      array (
        'driver' => 'log',
      ),
      'null' => 
      array (
        'driver' => 'null',
      ),
    ),
  ),
  'concurrency' => 
  array (
    'default' => 'process',
  ),
  'cors' => 
  array (
    'paths' => 
    array (
      0 => 'api/*',
      1 => 'sanctum/csrf-cookie',
    ),
    'allowed_methods' => 
    array (
      0 => '*',
    ),
    'allowed_origins' => 
    array (
      0 => '*',
    ),
    'allowed_origins_patterns' => 
    array (
    ),
    'allowed_headers' => 
    array (
      0 => '*',
    ),
    'exposed_headers' => 
    array (
    ),
    'max_age' => 0,
    'supports_credentials' => false,
  ),
  'hashing' => 
  array (
    'driver' => 'bcrypt',
    'bcrypt' => 
    array (
      'rounds' => '12',
      'verify' => true,
      'limit' => NULL,
    ),
    'argon' => 
    array (
      'memory' => 65536,
      'threads' => 1,
      'time' => 4,
      'verify' => true,
    ),
    'rehash_on_login' => true,
  ),
  'view' => 
  array (
    'paths' => 
    array (
      0 => 'D:\\Render\\Admin panel\\resources\\views',
    ),
    'compiled' => 'D:\\Render\\Admin panel\\storage\\framework\\views',
  ),
  'app' => 
  array (
    'name' => 'PTR NOW',
    'env' => 'local',
    'debug' => false,
    'url' => 'http://127.0.0.1:8000',
    'frontend_url' => 'http://localhost:3000',
    'asset_url' => NULL,
    'timezone' => 'Asia/Kolkata',
    'locale' => 'en',
    'fallback_locale' => 'en',
    'faker_locale' => 'en_US',
    'cipher' => 'AES-256-CBC',
    'key' => 'base64:aShngZiwKzo3RNDE/+j/xFnpnjybDBu3gaEo/rqYdCA=',
    'previous_keys' => 
    array (
    ),
    'maintenance' => 
    array (
      'driver' => 'file',
      'store' => 'database',
    ),
    'providers' => 
    array (
      0 => 'Illuminate\\Auth\\AuthServiceProvider',
      1 => 'Illuminate\\Broadcasting\\BroadcastServiceProvider',
      2 => 'Illuminate\\Bus\\BusServiceProvider',
      3 => 'Illuminate\\Cache\\CacheServiceProvider',
      4 => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
      5 => 'Illuminate\\Concurrency\\ConcurrencyServiceProvider',
      6 => 'Illuminate\\Cookie\\CookieServiceProvider',
      7 => 'Illuminate\\Database\\DatabaseServiceProvider',
      8 => 'Illuminate\\Encryption\\EncryptionServiceProvider',
      9 => 'Illuminate\\Filesystem\\FilesystemServiceProvider',
      10 => 'Illuminate\\Foundation\\Providers\\FoundationServiceProvider',
      11 => 'Illuminate\\Hashing\\HashServiceProvider',
      12 => 'Illuminate\\Mail\\MailServiceProvider',
      13 => 'Illuminate\\Notifications\\NotificationServiceProvider',
      14 => 'Illuminate\\Pagination\\PaginationServiceProvider',
      15 => 'Illuminate\\Auth\\Passwords\\PasswordResetServiceProvider',
      16 => 'Illuminate\\Pipeline\\PipelineServiceProvider',
      17 => 'Illuminate\\Queue\\QueueServiceProvider',
      18 => 'Illuminate\\Redis\\RedisServiceProvider',
      19 => 'Illuminate\\Session\\SessionServiceProvider',
      20 => 'Illuminate\\Translation\\TranslationServiceProvider',
      21 => 'Illuminate\\Validation\\ValidationServiceProvider',
      22 => 'Illuminate\\View\\ViewServiceProvider',
      23 => 'App\\Providers\\AppServiceProvider',
      24 => 'App\\Providers\\AuthServiceProvider',
      25 => 'App\\Providers\\EventServiceProvider',
      26 => 'App\\Providers\\MailConfigServiceProvider',
      27 => 'Froiden\\LaravelInstaller\\Providers\\LaravelInstallerServiceProvider',
      28 => 'App\\Providers\\AppServiceProvider',
      29 => 'App\\Providers\\AuthServiceProvider',
      30 => 'App\\Providers\\EventServiceProvider',
      31 => 'App\\Providers\\MailConfigServiceProvider',
      32 => 'Froiden\\LaravelInstaller\\Providers\\LaravelInstallerServiceProvider',
    ),
    'aliases' => 
    array (
      'App' => 'Illuminate\\Support\\Facades\\App',
      'Arr' => 'Illuminate\\Support\\Arr',
      'Artisan' => 'Illuminate\\Support\\Facades\\Artisan',
      'Auth' => 'Illuminate\\Support\\Facades\\Auth',
      'Benchmark' => 'Illuminate\\Support\\Benchmark',
      'Blade' => 'Illuminate\\Support\\Facades\\Blade',
      'Broadcast' => 'Illuminate\\Support\\Facades\\Broadcast',
      'Bus' => 'Illuminate\\Support\\Facades\\Bus',
      'Cache' => 'Illuminate\\Support\\Facades\\Cache',
      'Concurrency' => 'Illuminate\\Support\\Facades\\Concurrency',
      'Config' => 'Illuminate\\Support\\Facades\\Config',
      'Context' => 'Illuminate\\Support\\Facades\\Context',
      'Cookie' => 'Illuminate\\Support\\Facades\\Cookie',
      'Crypt' => 'Illuminate\\Support\\Facades\\Crypt',
      'Date' => 'Illuminate\\Support\\Facades\\Date',
      'DB' => 'Illuminate\\Support\\Facades\\DB',
      'Eloquent' => 'Illuminate\\Database\\Eloquent\\Model',
      'Event' => 'Illuminate\\Support\\Facades\\Event',
      'File' => 'Illuminate\\Support\\Facades\\File',
      'Gate' => 'Illuminate\\Support\\Facades\\Gate',
      'Hash' => 'Illuminate\\Support\\Facades\\Hash',
      'Http' => 'Illuminate\\Support\\Facades\\Http',
      'Js' => 'Illuminate\\Support\\Js',
      'Lang' => 'Illuminate\\Support\\Facades\\Lang',
      'Log' => 'Illuminate\\Support\\Facades\\Log',
      'Mail' => 'Illuminate\\Support\\Facades\\Mail',
      'Notification' => 'Illuminate\\Support\\Facades\\Notification',
      'Number' => 'Illuminate\\Support\\Number',
      'Password' => 'Illuminate\\Support\\Facades\\Password',
      'Process' => 'Illuminate\\Support\\Facades\\Process',
      'Queue' => 'Illuminate\\Support\\Facades\\Queue',
      'RateLimiter' => 'Illuminate\\Support\\Facades\\RateLimiter',
      'Redirect' => 'Illuminate\\Support\\Facades\\Redirect',
      'Request' => 'Illuminate\\Support\\Facades\\Request',
      'Response' => 'Illuminate\\Support\\Facades\\Response',
      'Route' => 'Illuminate\\Support\\Facades\\Route',
      'Schedule' => 'Illuminate\\Support\\Facades\\Schedule',
      'Schema' => 'Illuminate\\Support\\Facades\\Schema',
      'Session' => 'Illuminate\\Support\\Facades\\Session',
      'Storage' => 'Illuminate\\Support\\Facades\\Storage',
      'Str' => 'Illuminate\\Support\\Str',
      'Uri' => 'Illuminate\\Support\\Uri',
      'URL' => 'Illuminate\\Support\\Facades\\URL',
      'Validator' => 'Illuminate\\Support\\Facades\\Validator',
      'View' => 'Illuminate\\Support\\Facades\\View',
      'Vite' => 'Illuminate\\Support\\Facades\\Vite',
    ),
    'version' => '1.2.0',
    'frontendUrl' => 'https://hyper-local-22.vercel.app',
    'license_purchase' => '123456',
    'license_token' => '8c5d1dcb-e903-4e56-be28-7dff3d53bde7',
    'license_signature' => 'c1a92f5ad48fbb1eca37e888036c2abffb50ac7b643170484d1da6c1e1709ec0',
  ),
  'auth' => 
  array (
    'defaults' => 
    array (
      'guard' => 'web',
      'passwords' => 'users',
    ),
    'guards' => 
    array (
      'web' => 
      array (
        'driver' => 'session',
        'provider' => 'users',
      ),
      'admin' => 
      array (
        'driver' => 'session',
        'provider' => 'users',
      ),
      'seller' => 
      array (
        'driver' => 'session',
        'provider' => 'users',
      ),
      'sanctum' => 
      array (
        'driver' => 'sanctum',
        'provider' => NULL,
      ),
    ),
    'providers' => 
    array (
      'users' => 
      array (
        'driver' => 'eloquent',
        'model' => 'App\\Models\\User',
      ),
      'admins' => 
      array (
        'driver' => 'eloquent',
        'model' => 'App\\Models\\User',
      ),
      'sellers' => 
      array (
        'driver' => 'eloquent',
        'model' => 'App\\Models\\User',
      ),
      0 => 'Spatie\\Permission\\PermissionServiceProvider',
    ),
    'passwords' => 
    array (
      'users' => 
      array (
        'provider' => 'users',
        'table' => 'password_reset_tokens',
        'expire' => 60,
        'throttle' => 60,
      ),
    ),
    'password_timeout' => 10800,
  ),
  'cache' => 
  array (
    'default' => 'database',
    'stores' => 
    array (
      'array' => 
      array (
        'driver' => 'array',
        'serialize' => false,
      ),
      'session' => 
      array (
        'driver' => 'session',
        'key' => '_cache',
      ),
      'database' => 
      array (
        'driver' => 'database',
        'connection' => NULL,
        'table' => 'cache',
        'lock_connection' => NULL,
        'lock_table' => NULL,
      ),
      'file' => 
      array (
        'driver' => 'file',
        'path' => 'D:\\Render\\Admin panel\\storage\\framework/cache/data',
        'lock_path' => 'D:\\Render\\Admin panel\\storage\\framework/cache/data',
      ),
      'memcached' => 
      array (
        'driver' => 'memcached',
        'persistent_id' => NULL,
        'sasl' => 
        array (
          0 => NULL,
          1 => NULL,
        ),
        'options' => 
        array (
        ),
        'servers' => 
        array (
          0 => 
          array (
            'host' => '127.0.0.1',
            'port' => 11211,
            'weight' => 100,
          ),
        ),
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'cache',
        'lock_connection' => 'default',
      ),
      'dynamodb' => 
      array (
        'driver' => 'dynamodb',
        'key' => 'MAIL_PORT',
        'secret' => '',
        'region' => 'us-east-1',
        'table' => 'cache',
        'endpoint' => NULL,
      ),
      'octane' => 
      array (
        'driver' => 'octane',
      ),
      'failover' => 
      array (
        'driver' => 'failover',
        'stores' => 
        array (
          0 => 'database',
          1 => 'array',
        ),
      ),
    ),
    'prefix' => 'ptr_now_cache_',
  ),
  'database' => 
  array (
    'default' => 'mysql',
    'connections' => 
    array (
      'sqlite' => 
      array (
        'driver' => 'sqlite',
        'url' => NULL,
        'database' => 'ptrnow',
        'prefix' => '',
        'foreign_key_constraints' => true,
        'busy_timeout' => NULL,
        'journal_mode' => NULL,
        'synchronous' => NULL,
      ),
      'mysql' => 
      array (
        'driver' => 'mysql',
        'url' => NULL,
        'host' => 'localhost',
        'port' => '3306',
        'database' => 'ptrnow',
        'username' => 'root',
        'password' => '',
        'unix_socket' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => false,
        'engine' => NULL,
        'options' => 
        array (
        ),
      ),
      'mariadb' => 
      array (
        'driver' => 'mariadb',
        'url' => NULL,
        'host' => 'localhost',
        'port' => '3306',
        'database' => 'ptrnow',
        'username' => 'root',
        'password' => '',
        'unix_socket' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => true,
        'engine' => NULL,
        'options' => 
        array (
        ),
      ),
      'pgsql' => 
      array (
        'driver' => 'pgsql',
        'url' => NULL,
        'host' => 'localhost',
        'port' => '3306',
        'database' => 'ptrnow',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
        'search_path' => 'public',
        'sslmode' => 'prefer',
      ),
      'sqlsrv' => 
      array (
        'driver' => 'sqlsrv',
        'url' => NULL,
        'host' => 'localhost',
        'port' => '3306',
        'database' => 'ptrnow',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
      ),
    ),
    'migrations' => 
    array (
      'table' => 'migrations',
      'update_date_on_publish' => true,
    ),
    'redis' => 
    array (
      'client' => 'phpredis',
      'options' => 
      array (
        'cluster' => 'redis',
        'prefix' => 'ptr_now_database_',
        'persistent' => false,
      ),
      'default' => 
      array (
        'url' => NULL,
        'host' => '127.0.0.1',
        'username' => NULL,
        'password' => NULL,
        'port' => '6379',
        'database' => '0',
      ),
      'cache' => 
      array (
        'url' => NULL,
        'host' => '127.0.0.1',
        'username' => NULL,
        'password' => NULL,
        'port' => '6379',
        'database' => '1',
      ),
    ),
  ),
  'filepond' => 
  array (
    'disk' => 'public',
    'temp_disk' => 'local',
    'temp_folder' => 'filepond/temp',
    'middleware' => 
    array (
      0 => 'web',
      1 => 'auth',
    ),
    'soft_delete' => true,
    'expiration' => 30,
    'controller' => 'RahulHaque\\Filepond\\Http\\Controllers\\FilepondController',
    'model' => 'RahulHaque\\Filepond\\Models\\Filepond',
    'validation_rules' => 
    array (
      0 => 'required',
      1 => 'file',
      2 => 'max:5000',
    ),
    'server' => 
    array (
      'url' => '/filepond',
    ),
  ),
  'filesystems' => 
  array (
    'default' => 'public',
    'disks' => 
    array (
      'local' => 
      array (
        'driver' => 'local',
        'root' => 'D:\\Render\\Admin panel\\storage\\app/private',
        'serve' => true,
        'throw' => false,
        'report' => false,
      ),
      'public' => 
      array (
        'driver' => 'local',
        'root' => 'D:\\Render\\Admin panel\\storage\\app/public',
        'url' => 'http://127.0.0.1:8000/storage',
        'visibility' => 'public',
        'throw' => false,
        'report' => false,
      ),
      's3' => 
      array (
        'driver' => 's3',
        'key' => 'MAIL_PORT',
        'secret' => '',
        'region' => 'us-east-1',
        'bucket' => '',
        'url' => NULL,
        'endpoint' => NULL,
        'use_path_style_endpoint' => false,
        'throw' => false,
        'report' => false,
      ),
    ),
    'links' => 
    array (
      'D:\\Render\\Admin panel\\public\\storage' => 'D:\\Render\\Admin panel\\storage\\app/public',
    ),
  ),
  'firebase' => 
  array (
    'default' => 'app',
    'projects' => 
    array (
      'app' => 
      array (
        'credentials' => NULL,
        'auth' => 
        array (
          'tenant_id' => NULL,
        ),
        'firestore' => 
        array (
        ),
        'database' => 
        array (
          'url' => NULL,
        ),
        'dynamic_links' => 
        array (
          'default_domain' => NULL,
        ),
        'storage' => 
        array (
          'default_bucket' => NULL,
        ),
        'cache_store' => 'file',
        'logging' => 
        array (
          'http_log_channel' => NULL,
          'http_debug_log_channel' => NULL,
        ),
        'http_client_options' => 
        array (
          'proxy' => NULL,
          'timeout' => NULL,
          'guzzle_middlewares' => 
          array (
          ),
        ),
      ),
    ),
  ),
  'installer' => 
  array (
    'core' => 
    array (
      'minPhpVersion' => '8.2',
    ),
    'requirements' => 
    array (
      0 => 'openssl',
      1 => 'pdo',
      2 => 'mbstring',
      3 => 'tokenizer',
      4 => 'fileinfo',
      5 => 'curl',
    ),
    'permissions' => 
    array (
      'storage/app/' => '775',
      'storage/framework/' => '775',
      'storage/logs/' => '775',
      'bootstrap/cache/' => '775',
    ),
  ),
  'logging' => 
  array (
    'default' => 'stack',
    'deprecations' => 
    array (
      'channel' => NULL,
      'trace' => false,
    ),
    'channels' => 
    array (
      'stack' => 
      array (
        'driver' => 'stack',
        'channels' => 
        array (
          0 => 'single',
        ),
        'ignore_exceptions' => false,
      ),
      'single' => 
      array (
        'driver' => 'single',
        'path' => 'D:\\Render\\Admin panel\\storage\\logs/laravel.log',
        'level' => 'debug',
        'replace_placeholders' => true,
      ),
      'daily' => 
      array (
        'driver' => 'daily',
        'path' => 'D:\\Render\\Admin panel\\storage\\logs/laravel.log',
        'level' => 'debug',
        'days' => 14,
        'replace_placeholders' => true,
      ),
      'slack' => 
      array (
        'driver' => 'slack',
        'url' => NULL,
        'username' => 'Laravel Log',
        'emoji' => ':boom:',
        'level' => 'debug',
        'replace_placeholders' => true,
      ),
      'papertrail' => 
      array (
        'driver' => 'monolog',
        'level' => 'debug',
        'handler' => 'Monolog\\Handler\\SyslogUdpHandler',
        'handler_with' => 
        array (
          'host' => NULL,
          'port' => NULL,
          'connectionString' => 'tls://:',
        ),
        'processors' => 
        array (
          0 => 'Monolog\\Processor\\PsrLogMessageProcessor',
        ),
      ),
      'stderr' => 
      array (
        'driver' => 'monolog',
        'level' => 'debug',
        'handler' => 'Monolog\\Handler\\StreamHandler',
        'handler_with' => 
        array (
          'stream' => 'php://stderr',
        ),
        'formatter' => NULL,
        'processors' => 
        array (
          0 => 'Monolog\\Processor\\PsrLogMessageProcessor',
        ),
      ),
      'syslog' => 
      array (
        'driver' => 'syslog',
        'level' => 'debug',
        'facility' => 8,
        'replace_placeholders' => true,
      ),
      'errorlog' => 
      array (
        'driver' => 'errorlog',
        'level' => 'debug',
        'replace_placeholders' => true,
      ),
      'null' => 
      array (
        'driver' => 'monolog',
        'handler' => 'Monolog\\Handler\\NullHandler',
      ),
      'emergency' => 
      array (
        'path' => 'D:\\Render\\Admin panel\\storage\\logs/laravel.log',
      ),
    ),
  ),
  'mail' => 
  array (
    'default' => 'smtp',
    'mailers' => 
    array (
      'smtp' => 
      array (
        'transport' => 'smtp',
        'scheme' => 'smtp',
        'url' => NULL,
        'host' => 'smtp.gmail.com',
        'port' => '587',
        'username' => 'info@anitech.rajlav.com',
        'password' => 'd',
        'timeout' => NULL,
        'local_domain' => '127.0.0.1',
      ),
      'ses' => 
      array (
        'transport' => 'ses',
      ),
      'postmark' => 
      array (
        'transport' => 'postmark',
      ),
      'resend' => 
      array (
        'transport' => 'resend',
      ),
      'sendmail' => 
      array (
        'transport' => 'sendmail',
        'path' => '/usr/sbin/sendmail -bs -i',
      ),
      'log' => 
      array (
        'transport' => 'log',
        'channel' => NULL,
      ),
      'array' => 
      array (
        'transport' => 'array',
      ),
      'failover' => 
      array (
        'transport' => 'failover',
        'mailers' => 
        array (
          0 => 'smtp',
          1 => 'log',
        ),
        'retry_after' => 60,
      ),
      'roundrobin' => 
      array (
        'transport' => 'roundrobin',
        'mailers' => 
        array (
          0 => 'ses',
          1 => 'postmark',
        ),
        'retry_after' => 60,
      ),
    ),
    'from' => 
    array (
      'address' => 'info@anitech.rajlav.com',
      'name' => 'Rajlav Store',
    ),
    'markdown' => 
    array (
      'theme' => 'default',
      'paths' => 
      array (
        0 => 'D:\\Render\\Admin panel\\resources\\views/vendor/mail',
      ),
    ),
  ),
  'media-library' => 
  array (
    'disk_name' => 'public',
    'max_file_size' => 10485760,
    'queue_connection_name' => 'database',
    'queue_name' => '',
    'queue_conversions_by_default' => true,
    'queue_conversions_after_database_commit' => true,
    'media_model' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
    'media_observer' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Observers\\MediaObserver',
    'use_default_collection_serialization' => false,
    'temporary_upload_model' => 'Spatie\\MediaLibraryPro\\Models\\TemporaryUpload',
    'enable_temporary_uploads_session_affinity' => true,
    'generate_thumbnails_for_temporary_uploads' => true,
    'file_namer' => 'Spatie\\MediaLibrary\\Support\\FileNamer\\DefaultFileNamer',
    'path_generator' => 'Spatie\\MediaLibrary\\Support\\PathGenerator\\DefaultPathGenerator',
    'file_remover_class' => 'Spatie\\MediaLibrary\\Support\\FileRemover\\DefaultFileRemover',
    'custom_path_generators' => 
    array (
    ),
    'url_generator' => 'Spatie\\MediaLibrary\\Support\\UrlGenerator\\DefaultUrlGenerator',
    'moves_media_on_update' => false,
    'version_urls' => false,
    'image_optimizers' => 
    array (
      'Spatie\\ImageOptimizer\\Optimizers\\Jpegoptim' => 
      array (
        0 => '-m85',
        1 => '--force',
        2 => '--strip-all',
        3 => '--all-progressive',
      ),
      'Spatie\\ImageOptimizer\\Optimizers\\Pngquant' => 
      array (
        0 => '--force',
      ),
      'Spatie\\ImageOptimizer\\Optimizers\\Optipng' => 
      array (
        0 => '-i0',
        1 => '-o2',
        2 => '-quiet',
      ),
      'Spatie\\ImageOptimizer\\Optimizers\\Svgo' => 
      array (
        0 => '--disable=cleanupIDs',
      ),
      'Spatie\\ImageOptimizer\\Optimizers\\Gifsicle' => 
      array (
        0 => '-b',
        1 => '-O3',
      ),
      'Spatie\\ImageOptimizer\\Optimizers\\Cwebp' => 
      array (
        0 => '-m 6',
        1 => '-pass 10',
        2 => '-mt',
        3 => '-q 90',
      ),
      'Spatie\\ImageOptimizer\\Optimizers\\Avifenc' => 
      array (
        0 => '-a cq-level=23',
        1 => '-j all',
        2 => '--min 0',
        3 => '--max 63',
        4 => '--minalpha 0',
        5 => '--maxalpha 63',
        6 => '-a end-usage=q',
        7 => '-a tune=ssim',
      ),
    ),
    'image_generators' => 
    array (
      0 => 'Spatie\\MediaLibrary\\Conversions\\ImageGenerators\\Image',
      1 => 'Spatie\\MediaLibrary\\Conversions\\ImageGenerators\\Webp',
      2 => 'Spatie\\MediaLibrary\\Conversions\\ImageGenerators\\Avif',
      3 => 'Spatie\\MediaLibrary\\Conversions\\ImageGenerators\\Pdf',
      4 => 'Spatie\\MediaLibrary\\Conversions\\ImageGenerators\\Svg',
      5 => 'Spatie\\MediaLibrary\\Conversions\\ImageGenerators\\Video',
    ),
    'temporary_directory_path' => NULL,
    'image_driver' => 'gd',
    'ffmpeg_path' => '/usr/bin/ffmpeg',
    'ffprobe_path' => '/usr/bin/ffprobe',
    'ffmpeg_timeout' => 900,
    'ffmpeg_threads' => 0,
    'jobs' => 
    array (
      'perform_conversions' => 'Spatie\\MediaLibrary\\Conversions\\Jobs\\PerformConversionsJob',
      'generate_responsive_images' => 'Spatie\\MediaLibrary\\ResponsiveImages\\Jobs\\GenerateResponsiveImagesJob',
    ),
    'media_downloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
    'media_downloader_ssl' => true,
    'temporary_url_default_lifetime' => 5,
    'remote' => 
    array (
      'extra_headers' => 
      array (
        'CacheControl' => 'max-age=604800',
      ),
    ),
    'responsive_images' => 
    array (
      'width_calculator' => 'Spatie\\MediaLibrary\\ResponsiveImages\\WidthCalculator\\FileSizeOptimizedWidthCalculator',
      'use_tiny_placeholders' => true,
      'tiny_placeholder_generator' => 'Spatie\\MediaLibrary\\ResponsiveImages\\TinyPlaceholderGenerator\\Blurred',
    ),
    'enable_vapor_uploads' => false,
    'default_loading_attribute_value' => NULL,
    'prefix' => '',
    'force_lazy_loading' => true,
  ),
  'menu' => 
  array (
    'admin' => 
    array (
      'dashboard' => 
      array (
        'icon' => 'ti-home',
        'route' => 'admin.dashboard',
        'title' => 'labels.dashboard',
        'active' => 'dashboard',
      ),
      'orders' => 
      array (
        'icon' => 'ti-package',
        'route' => 'admin.orders.index',
        'title' => 'labels.orders',
        'active' => 'orders',
        'permission' => 'orders.view',
      ),
      'categories' => 
      array (
        'icon' => 'ti-category-2',
        'title' => 'labels.categories',
        'active' => 'categories',
        'route' => 
        array (
          'categories' => 
          array (
            'sub_active' => 'categories',
            'sub_route' => 'admin.categories.index',
            'sub_title' => 'labels.categories',
            'permission' => 'category.view',
          ),
          'sort' => 
          array (
            'sub_active' => 'sort',
            'sub_route' => 'admin.categories.sort',
            'sub_title' => 'labels.sort',
            'permission' => 'category.create',
          ),
          'bulk_upload' => 
          array (
            'sub_active' => 'bulk_upload',
            'sub_route' => 'admin.categories.bulk-upload',
            'sub_title' => 'labels.bulk_upload',
            'permission' => 'category.create',
          ),
        ),
      ),
      'brands' => 
      array (
        'icon' => 'ti-sparkles',
        'route' => 'admin.brands.index',
        'title' => 'labels.brands',
        'active' => 'brands',
        'permission' => 'brand.view',
      ),
      'customers' => 
      array (
        'icon' => 'ti-users',
        'title' => 'labels.customers',
        'active' => 'customers',
        'route' => 
        array (
          'customers' => 
          array (
            'sub_active' => 'customers',
            'sub_route' => 'admin.customers.index',
            'sub_title' => 'labels.customers',
            'permission' => 'customer.view',
          ),
          'transactions' => 
          array (
            'sub_active' => 'transactions',
            'sub_route' => 'admin.wallet.transactions',
            'sub_title' => 'labels.wallet_transactions',
            'permission' => 'orders.view',
          ),
          'deposits' => 
          array (
            'sub_active' => 'deposits',
            'sub_route' => 'admin.wallet.deposits',
            'sub_title' => 'labels.pending_wallet_deposits',
            'permission' => 'orders.view',
          ),
        ),
      ),
      'subscriptions' => 
      array (
        'icon' => 'ti-user-dollar',
        'title' => 'labels.subscriptions',
        'active' => 'subscriptions',
        'route' => 
        array (
          'plans' => 
          array (
            'sub_active' => 'plans',
            'sub_route' => 'admin.subscription-plans.index',
            'sub_title' => 'labels.plans',
            'permission' => 'subscription_plan.view',
          ),
          'subscribers' => 
          array (
            'sub_active' => 'subscribers',
            'sub_route' => 'admin.subscription-plans.subscribers',
            'sub_title' => 'labels.subscribers',
            'permission' => 'subscription_subscriber.view',
          ),
        ),
      ),
      'seller_management' => 
      array (
        'icon' => 'ti-users-group',
        'title' => 'labels.seller_management',
        'active' => 'sellers',
        'route' => 
        array (
          'sellers' => 
          array (
            'sub_active' => 'sellers',
            'sub_route' => 'admin.sellers.index',
            'sub_title' => 'labels.sellers',
            'permission' => 'seller.view',
          ),
          'add_sellers' => 
          array (
            'sub_active' => 'add_sellers',
            'sub_route' => 'admin.sellers.create',
            'sub_title' => 'labels.add_sellers',
            'permission' => 'seller.create',
          ),
          'earning_settlement' => 
          array (
            'sub_active' => 'seller_earning_settlement',
            'sub_route' => 'admin.commissions.index',
            'sub_title' => 'labels.earning_settlement',
            'permission' => 'commission.view',
          ),
          'seller_withdrawals' => 
          array (
            'sub_active' => 'seller_withdrawals',
            'sub_route' => 'admin.seller-withdrawals.index',
            'sub_title' => 'labels.seller_withdrawals',
            'permission' => 'seller_withdrawal.view',
          ),
          'seller_withdrawal_history' => 
          array (
            'sub_active' => 'seller_withdrawal_history',
            'sub_route' => 'admin.seller-withdrawals.history',
            'sub_title' => 'labels.seller_withdrawal_history',
            'permission' => 'seller_withdrawal.view',
          ),
        ),
      ),
      'stores' => 
      array (
        'icon' => 'ti-building-warehouse',
        'route' => 'admin.sellers.store.index',
        'title' => 'labels.stores',
        'active' => 'stores',
        'permission' => 'store.view',
      ),
      'products' => 
      array (
        'icon' => 'ti-cube-spark',
        'title' => 'labels.products',
        'active' => 'products',
        'route' => 
        array (
          'products' => 
          array (
            'sub_active' => 'products',
            'sub_route' => 'admin.products.index',
            'sub_title' => 'labels.products',
            'permission' => 'product.view',
          ),
          'pending_approval_products' => 
          array (
            'sub_active' => 'pending_approval_products',
            'sub_route' => 'admin.products.index',
            'route_param' => 
            array (
              'verification_status' => 'pending_verification',
            ),
            'sub_title' => 'labels.pending_approval_products',
            'permission' => 'product.view',
          ),
          'product_faqs' => 
          array (
            'sub_active' => 'product_faqs',
            'sub_route' => 'admin.product_faqs.index',
            'sub_title' => 'labels.product_faqs',
            'permission' => 'product_faqs.view',
          ),
        ),
      ),
      'tax_rates' => 
      array (
        'icon' => 'ti-square-rounded-percentage',
        'route' => 'admin.tax-rates.index',
        'title' => 'labels.tax_rates',
        'active' => 'tax_rates',
        'permission' => 'tax_class.view',
      ),
      'delivery_boy_management' => 
      array (
        'icon' => 'ti-truck-delivery',
        'title' => 'labels.delivery_boy_management',
        'active' => 'delivery_boy_management',
        'route' => 
        array (
          'delivery_boys' => 
          array (
            'sub_active' => 'delivery_boys',
            'sub_route' => 'admin.delivery-boys.index',
            'sub_title' => 'labels.delivery_boys',
            'permission' => 'delivery_boy.view',
          ),
          'delivery_boy_earnings' => 
          array (
            'sub_active' => 'delivery_boy_earnings',
            'sub_route' => 'admin.delivery-boy-earnings.index',
            'sub_title' => 'labels.delivery_boy_earnings',
            'permission' => 'delivery_boy_earning.view',
          ),
          'earning_history' => 
          array (
            'sub_active' => 'earning_history',
            'sub_route' => 'admin.delivery-boy-earnings.history',
            'sub_title' => 'labels.earning_history',
            'permission' => 'delivery_boy_earning.view',
          ),
          'delivery_boy_cash_collections' => 
          array (
            'sub_active' => 'delivery_boy_cash_collections',
            'sub_route' => 'admin.delivery-boy-cash-collections.index',
            'sub_title' => 'labels.delivery_boy_cash_collections',
            'permission' => 'delivery_boy_cash_collection.view',
          ),
          'cash_collection_history' => 
          array (
            'sub_active' => 'cash_collection_history',
            'sub_route' => 'admin.delivery-boy-cash-collections.history',
            'sub_title' => 'labels.cash_collection_history',
            'permission' => 'delivery_boy_cash_collection.view',
          ),
          'delivery_boy_withdrawals' => 
          array (
            'sub_active' => 'delivery_boy_withdrawals',
            'sub_route' => 'admin.delivery-boy-withdrawals.index',
            'sub_title' => 'labels.delivery_boy_withdrawals',
            'permission' => 'delivery_boy_withdrawal.view',
          ),
          'withdrawal_history' => 
          array (
            'sub_active' => 'withdrawal_history',
            'sub_route' => 'admin.delivery-boy-withdrawals.history',
            'sub_title' => 'labels.withdrawal_history',
            'permission' => 'delivery_boy_withdrawal.view',
          ),
        ),
      ),
      'banners' => 
      array (
        'icon' => 'ti-photo',
        'route' => 'admin.banners.index',
        'title' => 'labels.banners',
        'active' => 'banners',
        'permission' => 'banner.view',
      ),
      'featured_section' => 
      array (
        'icon' => 'ti-star',
        'title' => 'labels.featured_section',
        'active' => 'featured_section',
        'route' => 
        array (
          'featured_section' => 
          array (
            'sub_active' => 'featured_section',
            'sub_route' => 'admin.featured-sections.index',
            'sub_title' => 'labels.featured_section',
            'permission' => 'featured_section.view',
          ),
          'sort_featured_section' => 
          array (
            'sub_active' => 'sort_featured_section',
            'sub_route' => 'admin.featured-sections.sort',
            'sub_title' => 'labels.sort_featured_section',
            'permission' => 'featured_section.sorting_view',
          ),
        ),
      ),
      'promos' => 
      array (
        'icon' => 'ti-ticket',
        'route' => 'admin.promos.index',
        'title' => 'labels.promos',
        'active' => 'promos',
        'permission' => 'promo.view',
      ),
      'faqs' => 
      array (
        'icon' => 'ti-help-circle',
        'route' => 'admin.faqs.index',
        'title' => 'labels.faqs',
        'active' => 'faqs',
        'permission' => 'faq.view',
      ),
      'delivery_zones' => 
      array (
        'icon' => 'ti-map-pin',
        'route' => 'admin.delivery-zones.index',
        'title' => 'labels.delivery_zones',
        'active' => 'delivery_zones',
        'permission' => 'delivery_zone.view',
      ),
      'notifications' => 
      array (
        'icon' => 'ti-bell-ringing',
        'route' => 'admin.notifications.index',
        'title' => 'labels.notifications',
        'active' => 'notifications',
        'permission' => 'notification.view',
      ),
      'roles_permissions' => 
      array (
        'icon' => 'ti-users-group',
        'title' => 'labels.roles_permissions',
        'active' => 'roles_permissions',
        'route' => 
        array (
          'roles' => 
          array (
            'sub_active' => 'roles',
            'sub_route' => 'admin.roles.index',
            'sub_title' => 'labels.roles',
            'permission' => 'role.view',
          ),
          'system_users' => 
          array (
            'sub_active' => 'system_users',
            'sub_route' => 'admin.system-users.index',
            'sub_title' => 'labels.system_users',
            'permission' => 'system_user.view',
          ),
        ),
      ),
      'settings' => 
      array (
        'icon' => 'ti-settings',
        'title' => 'labels.settings',
        'active' => 'settings',
        'route' => 
        array (
          'system' => 
          array (
            'sub_active' => 'system',
            'sub_route' => 'admin.settings.show',
            'route_param' => 
            array (
              'setting' => 'system',
            ),
            'sub_title' => 'labels.menu_system',
            'permission' => 'setting.system.view',
          ),
          'web' => 
          array (
            'sub_active' => 'web',
            'sub_route' => 'admin.settings.show',
            'route_param' => 
            array (
              'setting' => 'web',
            ),
            'sub_title' => 'labels.menu_web',
            'permission' => 'setting.web.view',
          ),
          'app' => 
          array (
            'sub_active' => 'app',
            'sub_route' => 'admin.settings.show',
            'route_param' => 
            array (
              'setting' => 'app',
            ),
            'sub_title' => 'labels.menu_app',
            'permission' => 'setting.app.view',
          ),
          'home_general_settings' => 
          array (
            'sub_active' => 'home_general_settings',
            'sub_route' => 'admin.settings.show',
            'route_param' => 
            array (
              'setting' => 'home_general_settings',
            ),
            'sub_title' => 'labels.home_general_settings',
            'permission' => 'setting.home_general_settings.view',
          ),
          'storage' => 
          array (
            'sub_active' => 'storage',
            'sub_route' => 'admin.settings.show',
            'route_param' => 
            array (
              'setting' => 'storage',
            ),
            'sub_title' => 'labels.menu_storage',
            'permission' => 'setting.storage.view',
          ),
          'authentication' => 
          array (
            'sub_active' => 'authentication',
            'sub_route' => 'admin.settings.show',
            'route_param' => 
            array (
              'setting' => 'authentication',
            ),
            'sub_title' => 'labels.menu_authentication',
            'permission' => 'setting.authentication.view',
          ),
          'email' => 
          array (
            'sub_active' => 'email',
            'sub_route' => 'admin.settings.show',
            'route_param' => 
            array (
              'setting' => 'email',
            ),
            'sub_title' => 'labels.email',
            'permission' => 'setting.email.view',
          ),
          'payment' => 
          array (
            'sub_active' => 'payment',
            'sub_route' => 'admin.settings.show',
            'route_param' => 
            array (
              'setting' => 'payment',
            ),
            'sub_title' => 'labels.menu_payment',
            'permission' => 'setting.payment.view',
          ),
          'notification' => 
          array (
            'sub_active' => 'notification',
            'sub_route' => 'admin.settings.show',
            'route_param' => 
            array (
              'setting' => 'notification',
            ),
            'sub_title' => 'labels.menu_notification',
            'permission' => 'setting.notification.view',
          ),
          'delivery_boy' => 
          array (
            'sub_active' => 'delivery_boy',
            'sub_route' => 'admin.settings.show',
            'route_param' => 
            array (
              'setting' => 'delivery_boy',
            ),
            'sub_title' => 'labels.delivery_boy',
            'permission' => 'setting.delivery_boy.view',
          ),
          'seller' => 
          array (
            'sub_active' => 'seller',
            'sub_route' => 'admin.settings.show',
            'route_param' => 
            array (
              'setting' => 'seller',
            ),
            'sub_title' => 'labels.seller',
            'permission' => 'setting.seller.view',
          ),
        ),
      ),
      'system_updates' => 
      array (
        'icon' => 'ti-package',
        'route' => 'admin.system-updates.index',
        'title' => 'labels.system_updates',
        'active' => 'system_updates',
        'permission' => 'setting.system.view',
      ),
      'logout' => 
      array (
        'icon' => 'ti-logout-2',
        'route' => 'admin.logout',
        'title' => 'labels.logout',
      ),
    ),
    'delivery-partner' => 
    array (
      'dashboard' => 
      array (
        'icon' => 'ti-home',
        'route' => 'delivery-partner.dashboard',
        'title' => 'labels.delivery_partner_dashboard',
      ),
    ),
    'seller' => 
    array (
      'dashboard' => 
      array (
        'icon' => 'ti-home',
        'route' => 'seller.dashboard',
        'title' => 'labels.seller_dashboard',
        'active' => 'dashboard',
      ),
      'wallet' => 
      array (
        'icon' => 'ti-wallet',
        'title' => 'labels.wallet',
        'active' => 'wallet',
        'route' => 
        array (
          'balance' => 
          array (
            'sub_active' => 'wallet_balance',
            'sub_route' => 'seller.wallet.index',
            'sub_title' => 'labels.wallet_balance',
            'permission' => 'wallet.view',
          ),
          'withdrawals' => 
          array (
            'sub_active' => 'withdrawals',
            'sub_route' => 'seller.withdrawals.index',
            'sub_title' => 'labels.withdrawals',
            'permission' => 'withdrawal.view',
          ),
          'withdrawal_history' => 
          array (
            'sub_active' => 'withdrawal_history',
            'sub_route' => 'seller.withdrawals.history',
            'sub_title' => 'labels.withdrawal_history',
            'permission' => 'withdrawal.view',
          ),
        ),
      ),
      'earnings' => 
      array (
        'icon' => 'ti-currency-dollar',
        'route' => 'seller.commissions.index',
        'title' => 'labels.earnings',
        'active' => 'earnings',
        'permission' => 'earning.view',
      ),
      'orders' => 
      array (
        'icon' => 'ti-package',
        'route' => 'seller.orders.index',
        'title' => 'labels.seller_orders',
        'active' => 'orders',
        'permission' => 'order.view',
      ),
      'return_orders' => 
      array (
        'icon' => 'ti-truck-return',
        'title' => 'labels.return_orders',
        'active' => 'return_orders',
        'route' => 
        array (
          'return_requests' => 
          array (
            'sub_active' => 'return_requests',
            'sub_route' => 'seller.returns.index',
            'sub_title' => 'labels.return_requests',
            'permission' => 'return.view',
          ),
        ),
      ),
      'categories' => 
      array (
        'icon' => 'ti-category-2',
        'route' => 'seller.categories.index',
        'title' => 'labels.seller_categories',
        'active' => 'categories',
        'permission' => 'category.view',
      ),
      'brands' => 
      array (
        'icon' => 'ti-sparkles',
        'route' => 'seller.brands.index',
        'title' => 'labels.seller_brands',
        'active' => 'brands',
        'permission' => 'brand.view',
      ),
      'attributes' => 
      array (
        'icon' => 'ti-tag-starred',
        'route' => 'seller.attributes.index',
        'title' => 'labels.attributes',
        'active' => 'attributes',
        'permission' => 'attribute.view',
      ),
      'products' => 
      array (
        'icon' => 'ti-cube-spark',
        'title' => 'labels.manage_products',
        'active' => 'products',
        'route' => 
        array (
          'products' => 
          array (
            'sub_active' => 'products',
            'sub_route' => 'seller.products.index',
            'sub_title' => 'labels.seller_products',
            'permission' => 'product.view',
          ),
          'add_products' => 
          array (
            'sub_active' => 'add_products',
            'sub_route' => 'seller.products.create',
            'sub_title' => 'labels.add_products',
            'permission' => 'product.create',
          ),
          'bulk_upload' => 
          array (
            'sub_active' => 'bulk_upload',
            'sub_route' => 'seller.products.bulk-upload',
            'sub_title' => 'labels.bulk_upload',
            'permission' => 'product.create',
          ),
          'product_faqs' => 
          array (
            'sub_active' => 'product_faqs',
            'sub_route' => 'seller.product_faqs.index',
            'sub_title' => 'labels.seller_product_faqs',
            'permission' => 'product_faq.view',
          ),
        ),
      ),
      'subscriptions' => 
      array (
        'icon' => 'ti-user-dollar',
        'title' => 'labels.subscriptions',
        'active' => 'subscriptions',
        'route' => 
        array (
          'plans' => 
          array (
            'sub_active' => 'plans',
            'sub_route' => 'seller.subscription-plans.index',
            'sub_title' => 'labels.plans',
            'permission' => 'subscription.view',
          ),
          'current' => 
          array (
            'sub_active' => 'current',
            'sub_route' => 'seller.subscription-plans.current',
            'sub_title' => 'labels.current_subscription',
            'permission' => 'subscription.view',
          ),
          'history' => 
          array (
            'sub_active' => 'history',
            'sub_route' => 'seller.subscription-plans.history',
            'sub_title' => 'labels.subscription_history',
            'permission' => 'subscription.view',
          ),
        ),
      ),
      'tax_rates' => 
      array (
        'icon' => 'ti-square-rounded-percentage',
        'route' => 'seller.tax-rates.index',
        'title' => 'labels.seller_tax_rates',
        'active' => 'tax_rates',
        'permission' => 'tax_rate.view',
      ),
      'stores' => 
      array (
        'icon' => 'ti-building-warehouse',
        'title' => 'labels.seller_stores',
        'active' => 'stores',
        'route' => 'seller.stores.index',
        'permission' => 'store.view',
      ),
      'notifications' => 
      array (
        'icon' => 'ti-bell-ringing',
        'route' => 'seller.notifications.index',
        'title' => 'labels.seller_notifications',
        'active' => 'notifications',
        'permission' => 'notification.view',
      ),
      'roles_permissions' => 
      array (
        'icon' => 'ti-users-group',
        'title' => 'labels.seller_roles_permissions',
        'active' => 'roles_permissions',
        'route' => 
        array (
          'roles' => 
          array (
            'sub_active' => 'roles',
            'sub_route' => 'seller.roles.index',
            'sub_title' => 'labels.seller_roles',
            'permission' => 'role.view',
          ),
          'system_users' => 
          array (
            'sub_active' => 'system_users',
            'sub_route' => 'seller.system-users.index',
            'sub_title' => 'labels.seller_system_users',
            'permission' => 'system_user.view',
          ),
        ),
      ),
      'logout' => 
      array (
        'icon' => 'ti-logout-2',
        'route' => 'seller.logout',
        'title' => 'labels.seller_logout',
      ),
    ),
  ),
  'permission' => 
  array (
    'models' => 
    array (
      'permission' => 'Spatie\\Permission\\Models\\Permission',
      'role' => 'Spatie\\Permission\\Models\\Role',
    ),
    'table_names' => 
    array (
      'roles' => 'roles',
      'permissions' => 'permissions',
      'model_has_permissions' => 'model_has_permissions',
      'model_has_roles' => 'model_has_roles',
      'role_has_permissions' => 'role_has_permissions',
    ),
    'column_names' => 
    array (
      'role_pivot_key' => NULL,
      'permission_pivot_key' => NULL,
      'model_morph_key' => 'model_id',
      'team_foreign_key' => 'seller_id',
    ),
    'register_permission_check_method' => true,
    'register_octane_reset_listener' => false,
    'events_enabled' => false,
    'teams' => false,
    'team_resolver' => 'Spatie\\Permission\\DefaultTeamResolver',
    'use_passport_client_credentials' => false,
    'display_permission_in_exception' => false,
    'display_role_in_exception' => false,
    'enable_wildcard_permission' => false,
    'cache' => 
    array (
      'expiration_time' => 
      \DateInterval::__set_state(array(
         'from_string' => true,
         'date_string' => '24 hours',
      )),
      'key' => 'spatie.permission.cache',
      'store' => 'default',
    ),
  ),
  'queue' => 
  array (
    'default' => 'database',
    'connections' => 
    array (
      'sync' => 
      array (
        'driver' => 'sync',
      ),
      'database' => 
      array (
        'driver' => 'database',
        'connection' => NULL,
        'table' => 'jobs',
        'queue' => 'default',
        'retry_after' => 90,
        'after_commit' => false,
      ),
      'beanstalkd' => 
      array (
        'driver' => 'beanstalkd',
        'host' => 'localhost',
        'queue' => 'default',
        'retry_after' => 90,
        'block_for' => 0,
        'after_commit' => false,
      ),
      'sqs' => 
      array (
        'driver' => 'sqs',
        'key' => 'MAIL_PORT',
        'secret' => '',
        'prefix' => 'https://sqs.us-east-1.amazonaws.com/your-account-id',
        'queue' => 'default',
        'suffix' => NULL,
        'region' => 'us-east-1',
        'after_commit' => false,
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => 'default',
        'retry_after' => 90,
        'block_for' => NULL,
        'after_commit' => false,
      ),
      'deferred' => 
      array (
        'driver' => 'deferred',
      ),
      'failover' => 
      array (
        'driver' => 'failover',
        'connections' => 
        array (
          0 => 'database',
          1 => 'deferred',
        ),
      ),
    ),
    'batching' => 
    array (
      'database' => 'mysql',
      'table' => 'job_batches',
    ),
    'failed' => 
    array (
      'driver' => 'database-uuids',
      'database' => 'mysql',
      'table' => 'failed_jobs',
    ),
  ),
  'sanctum' => 
  array (
    'stateful' => 
    array (
      0 => 'localhost',
      1 => 'localhost:3000',
      2 => '127.0.0.1',
      3 => '127.0.0.1:8000',
      4 => '::1',
      5 => '127.0.0.1:8000',
    ),
    'guard' => 
    array (
      0 => 'web',
    ),
    'expiration' => NULL,
    'token_prefix' => '',
    'middleware' => 
    array (
      'authenticate_session' => 'Laravel\\Sanctum\\Http\\Middleware\\AuthenticateSession',
      'encrypt_cookies' => 'Illuminate\\Cookie\\Middleware\\EncryptCookies',
      'validate_csrf_token' => 'Illuminate\\Foundation\\Http\\Middleware\\ValidateCsrfToken',
    ),
  ),
  'scramble' => 
  array (
    'api_path' => 'api',
    'api_domain' => NULL,
    'export_path' => 'api.json',
    'info' => 
    array (
      'version' => '1.1.1',
      'description' => '',
    ),
    'ui' => 
    array (
      'title' => NULL,
      'theme' => 'dark',
      'hide_try_it' => false,
      'hide_schemas' => false,
      'logo' => '',
      'try_it_credentials_policy' => 'include',
      'layout' => 'responsive',
    ),
    'servers' => NULL,
    'enum_cases_description_strategy' => 'description',
    'enum_cases_names_strategy' => false,
    'flatten_deep_query_parameters' => true,
    'middleware' => 
    array (
      0 => 'web',
      1 => 'Dedoc\\Scramble\\Http\\Middleware\\RestrictedDocsAccess',
    ),
    'extensions' => 
    array (
    ),
  ),
  'services' => 
  array (
    'postmark' => 
    array (
      'token' => NULL,
    ),
    'resend' => 
    array (
      'key' => NULL,
    ),
    'ses' => 
    array (
      'key' => 'MAIL_PORT',
      'secret' => '',
      'region' => 'us-east-1',
    ),
    'slack' => 
    array (
      'notifications' => 
      array (
        'bot_user_oauth_token' => NULL,
        'channel' => NULL,
      ),
    ),
    'firebase' => 
    array (
      'credentials' => 
      array (
        'file' => 'D:\\Render\\Admin panel\\storage\\app/private/settings/service-account-file.json',
      ),
    ),
  ),
  'session' => 
  array (
    'driver' => 'database',
    'lifetime' => 1440,
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => 'D:\\Render\\Admin panel\\storage\\framework/sessions',
    'connection' => NULL,
    'table' => 'sessions',
    'store' => NULL,
    'lottery' => 
    array (
      0 => 2,
      1 => 100,
    ),
    'cookie' => 'ptr_now_session',
    'path' => '/',
    'domain' => NULL,
    'secure' => NULL,
    'http_only' => true,
    'same_site' => 'lax',
    'partitioned' => false,
  ),
  'debugbar' => 
  array (
    'enabled' => NULL,
    'hide_empty_tabs' => true,
    'except' => 
    array (
      0 => 'telescope*',
      1 => 'horizon*',
      2 => '_boost/browser-logs',
    ),
    'storage' => 
    array (
      'enabled' => true,
      'open' => NULL,
      'driver' => 'file',
      'path' => 'D:\\Render\\Admin panel\\storage\\debugbar',
      'connection' => NULL,
      'provider' => '',
      'hostname' => '127.0.0.1',
      'port' => 2304,
    ),
    'editor' => 'phpstorm',
    'remote_sites_path' => NULL,
    'local_sites_path' => NULL,
    'include_vendors' => true,
    'capture_ajax' => true,
    'add_ajax_timing' => false,
    'ajax_handler_auto_show' => true,
    'ajax_handler_enable_tab' => true,
    'defer_datasets' => false,
    'error_handler' => false,
    'error_level' => 32767,
    'clockwork' => false,
    'collectors' => 
    array (
      'phpinfo' => false,
      'messages' => true,
      'time' => true,
      'memory' => true,
      'exceptions' => true,
      'log' => true,
      'db' => true,
      'views' => true,
      'route' => false,
      'auth' => false,
      'gate' => true,
      'session' => false,
      'symfony_request' => true,
      'mail' => true,
      'laravel' => true,
      'events' => false,
      'default_request' => false,
      'logs' => false,
      'files' => false,
      'config' => false,
      'cache' => false,
      'models' => true,
      'livewire' => true,
      'jobs' => false,
      'pennant' => false,
    ),
    'options' => 
    array (
      'time' => 
      array (
        'memory_usage' => false,
      ),
      'messages' => 
      array (
        'trace' => true,
        'capture_dumps' => false,
      ),
      'memory' => 
      array (
        'reset_peak' => false,
        'with_baseline' => false,
        'precision' => 0,
      ),
      'auth' => 
      array (
        'show_name' => true,
        'show_guards' => true,
      ),
      'gate' => 
      array (
        'trace' => false,
      ),
      'db' => 
      array (
        'with_params' => true,
        'exclude_paths' => 
        array (
        ),
        'backtrace' => true,
        'backtrace_exclude_paths' => 
        array (
        ),
        'timeline' => false,
        'duration_background' => true,
        'explain' => 
        array (
          'enabled' => false,
        ),
        'hints' => false,
        'show_copy' => true,
        'only_slow_queries' => true,
        'slow_threshold' => false,
        'memory_usage' => false,
        'soft_limit' => 100,
        'hard_limit' => 500,
      ),
      'mail' => 
      array (
        'timeline' => true,
        'show_body' => true,
      ),
      'views' => 
      array (
        'timeline' => true,
        'data' => false,
        'group' => 50,
        'inertia_pages' => 'js/Pages',
        'exclude_paths' => 
        array (
          0 => 'vendor/filament',
        ),
      ),
      'route' => 
      array (
        'label' => true,
      ),
      'session' => 
      array (
        'hiddens' => 
        array (
        ),
      ),
      'symfony_request' => 
      array (
        'label' => true,
        'hiddens' => 
        array (
        ),
      ),
      'events' => 
      array (
        'data' => false,
        'excluded' => 
        array (
        ),
      ),
      'logs' => 
      array (
        'file' => NULL,
      ),
      'cache' => 
      array (
        'values' => true,
      ),
    ),
    'inject' => true,
    'route_prefix' => '_debugbar',
    'route_middleware' => 
    array (
    ),
    'route_domain' => NULL,
    'theme' => 'auto',
    'debug_backtrace_limit' => 50,
  ),
  'blueprint' => 
  array (
    'namespace' => 'App',
    'models_namespace' => 'Models',
    'controllers_namespace' => 'Http\\Controllers',
    'components_namespace' => 'Livewire',
    'policy_namespace' => 'Policies',
    'app_path' => 'app',
    'generate_phpdocs' => false,
    'use_constraints' => false,
    'on_delete' => 'cascade',
    'on_update' => 'cascade',
    'fake_nullables' => true,
    'use_guarded' => false,
    'types' => 
    array (
      'primary' => 'id',
      'timestamps' => 'timestamp',
    ),
    'singular_routes' => false,
    'property_promotion' => false,
    'generate_resource_collection_classes' => true,
    'generators' => 
    array (
      'controller' => 'Blueprint\\Generators\\ControllerGenerator',
      'factory' => 'Blueprint\\Generators\\FactoryGenerator',
      'migration' => 'Blueprint\\Generators\\MigrationGenerator',
      'model' => 'Blueprint\\Generators\\ModelGenerator',
      'route' => 'Blueprint\\Generators\\RouteGenerator',
      'seeder' => 'Blueprint\\Generators\\SeederGenerator',
      'test' => 'Blueprint\\Generators\\PhpUnitTestGenerator',
      'event' => 'Blueprint\\Generators\\Statements\\EventGenerator',
      'form_request' => 'Blueprint\\Generators\\Statements\\FormRequestGenerator',
      'job' => 'Blueprint\\Generators\\Statements\\JobGenerator',
      'mail' => 'Blueprint\\Generators\\Statements\\MailGenerator',
      'notification' => 'Blueprint\\Generators\\Statements\\NotificationGenerator',
      'resource' => 'Blueprint\\Generators\\Statements\\ResourceGenerator',
      'view' => 'Blueprint\\Generators\\Statements\\ViewGenerator',
      'inertia_page' => 'Blueprint\\Generators\\Statements\\InertiaPageGenerator',
      'policy' => 'Blueprint\\Generators\\PolicyGenerator',
    ),
  ),
  'tinker' => 
  array (
    'commands' => 
    array (
    ),
    'alias' => 
    array (
    ),
    'dont_alias' => 
    array (
      0 => 'App\\Nova',
    ),
  ),
);
