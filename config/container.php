<?php

use App\Database\Transaction;
use App\Database\TransactionInterface;
use App\Exception\DefaultErrorHandler;
use Cake\Database\Connection;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Interfaces\RouteParserInterface;
use Slim\Middleware\ErrorMiddleware;
use Slim\Views\PhpRenderer;
use Selective\BasePath\BasePathMiddleware;
use Nyholm\Psr7\Factory\Psr17Factory;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use App\Routing\JwtAuth;
use Slim\Views\Twig;
use Symfony\Component\Console\Application;
use Odan\Session\PhpSession;
use Odan\Session\SessionInterface;
use Odan\Session\Middleware\SessionMiddleware;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;

return [
    'settings' => function () {
        return require __DIR__ . '/settings.php';
    },

    SessionInterface::class => function (ContainerInterface $container) {
        $settings = $container->get('settings');
        $session = new PhpSession();
        $session->setOptions((array)$settings['session']);
        return $session;
    },
    SessionMiddleware::class => function (ContainerInterface $container) {
        return new SessionMiddleware($container->get(SessionInterface::class));
    },

    App::class => function (ContainerInterface $container) {
        AppFactory::setContainer($container);

        return AppFactory::create();
    },
    // The Twig template engine
    Twig::class => function (ContainerInterface $container) {
        $settings = (array)$container->get('settings');
        $twigSettings = $settings['twig'];

        $twig = Twig::create($twigSettings['paths'], $twigSettings['options']);

        // The path must be absolute.
        // e.g. /var/www/example.com/public
        $publicPath = (string)$settings['public'];

        return $twig;
    },

    BasePathMiddleware::class => function (ContainerInterface $container) {
        return new BasePathMiddleware($container->get(App::class));
    },

    ErrorMiddleware::class => function (ContainerInterface $container) {
        $app = $container->get(App::class);
        $settings = $container->get('settings')['error'];

        $errorMiddleware = new ErrorMiddleware(
            $app->getCallableResolver(),
            $app->getResponseFactory(),
            (bool)$settings['display_error_details'],
            (bool)$settings['log_errors'],
            (bool)$settings['log_error_details']
        );

        $errorMiddleware->setDefaultErrorHandler($container->get(DefaultErrorHandler::class));

        return $errorMiddleware;
    },

    Connection::class => function (ContainerInterface $container) {
        return new Connection($container->get('settings')['db']);
    },

    PDO::class => function (ContainerInterface $container) {
        $db = $container->get(Connection::class);
        $driver = $db->getDriver();
        $driver->connect();

        return $driver->getConnection();
    },
    /*
    PDO::class => function (ContainerInterface $container) {
        $settings = $container->get('settings')['db'];

        $host = $settings['host'];
        $dbname = $settings['database'];
        $username = $settings['username'];
        $password = $settings['password'];
        $charset = $settings['charset'];
        $flags = $settings['flags'];
        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

        return new PDO($dsn, $username, $password, $flags);
    },*/

    ServerRequestFactoryInterface::class => function () {
        return new Psr17Factory();
    },
    PhpRenderer::class => function (ContainerInterface $container) {
        return new PhpRenderer($container->get('settings')['template']);
    },
    // The Slim RouterParser
    RouteParserInterface::class => function (ContainerInterface $container) {
        return $container->get(App::class)->getRouteCollector()->getRouteParser();
    },
    // HTTP factories
    ResponseFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(App::class)->getResponseFactory();
    },
    JwtAuth::class => function (ContainerInterface $container) {
        $configuration = $container->get(Configuration::class);
        $jwtSettings = $container->get('settings')['jwt'];
        $issuer = (string)$jwtSettings['issuer'];
        $lifetime = (int)$jwtSettings['lifetime'];
        return new JwtAuth($configuration, $issuer, $lifetime);
    },
    // Add this entry
    Configuration::class => function (ContainerInterface $container) {
        $jwtSettings = $container->get('settings')['jwt'];
        $privateKey = (string)$jwtSettings['private_key'];
        $publicKey = (string)$jwtSettings['public_key'];
// Asymmetric algorithms use a private key for signature creation
// and a public key for verification
        return Configuration::forAsymmetricSigner(
            new Sha256(),
            InMemory::plainText($privateKey),
            InMemory::plainText($publicKey)
        );
    },
    Application::class => function (ContainerInterface $container) {
        $application = new Application();

        foreach ($container->get('settings')['commands'] as $class) {
            $application->add($container->get($class));
        }

        return $application;
    },

    TransactionInterface::class => function(ContainerInterface $container){
        return new Transaction($container->get(Connection::class));
    },

    // SMTP transport
    MailerInterface::class => function (ContainerInterface $container) {
        $settings = $container->get('settings')['smtp'];
        // or
        // $settings = $container->get('settings')['smtp'];

        // smtp://user:pass@smtp.example.com:25
        $dsn = sprintf(
            '%s://%s:%s@%s:%s',
            $settings['type'],
            $settings['username'],
            $settings['password'],
            $settings['host'],
            $settings['port']
        );

        return new Mailer(Transport::fromDsn($dsn));
    },

];
