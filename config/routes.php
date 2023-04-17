<?php

use App\Middleware\UserAuthMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {
    // Redirect to Swagger documentation
    $app->get('/', \App\Action\Home\HomeAction::class)->setName('home');

    $app->get('/docs/v1', \App\Action\OpenApi\Version1DocAction::class)->setName('docs');

    $app->post('/payment-sheet', \App\Action\Stripe\PaymentSheet::class);

    $app->post('/login', \App\Action\Auth\UserLoginSubmitAction::class)->setName('user-login');

    $app->post('/admin_login', \App\Action\Auth\AdminLoginSubmitAction::class)->setName('admin-login');


    $app->get('/logout', \App\Action\Auth\LogoutAction::class);


    $app->group('/public', function (RouteCollectorProxy $group) {
        $group->get("/login", \App\Action\Pages\AdminLoginPageAction::class)->setName('public-login');
    });

    $app->group('/pages', function (RouteCollectorProxy $group) {
        $group->get("/home", \App\Action\Pages\HomeAdminPageAction::class)->setName('pages-home');
        $group->get("/products", \App\Action\Pages\ProductPageAction::class)->setName('pages-product');
        $group->get("/categories", \App\Action\Pages\CategoriesPageAction::class)->setName('pages-categories');
        $group->get("/claims", \App\Action\Pages\ClaimsPageAction::class)->setName('pages-claims');
        $group->get("/users", \App\Action\Pages\UsersPageAction::class)->setName('users-page');
    })->add(UserAuthMiddleware::class);

    // API endpoints. This group is protected with JWT.
    $app->group('/api', function (RouteCollectorProxy $group) {
        /*** DASHBOARD STAT ***/
        $group->get('/stat/today/money', \App\Action\Statistics\TodayMoneyAction::class);
        $group->get('/stat/today/users', \App\Action\Statistics\TodayUsersAction::class);
        $group->get('/stat/total-users', \App\Action\Statistics\TotalUsersAction::class);
        $group->get('/stat/total-sales', \App\Action\Statistics\TotalSalesAction::class);
        $group->get('/stat/product/latest', \App\Action\Statistics\LatestProductsAction::class);
        $group->get('/stat/claims/latest', \App\Action\Statistics\LatestClaimsAction::class);

        /*** PRODUCT PAGE ***/
        $group->post('/product/update', \App\Action\Products\WebProductUpdateAction::class);
        $group->post('/product/delete', \App\Action\Products\WebProductDeleteAction::class);
        $group->get('/products/list', \App\Action\Products\WebProductListAction::class);
        $group->post('/products/add', \App\Action\Products\WebProductAddAction::class);

        /*** CATEGORIES ***/
        $group->get('/categories/list', \App\Action\Categories\WebCategoriesListAction::class);
        $group->post('/categories/update', \App\Action\Categories\WebCategoriesUpdateAction::class);
        $group->post('/categories/delete', \App\Action\Categories\WebCategoriesDeleteAction::class);
        $group->post('/categories/add', \App\Action\Categories\WebCategoriesAddAction::class);

        /*** CLAIMS ***/
        $group->get('/claims/list', \App\Action\Prizes\WebClaimListAction::class);
        $group->post('/claims/update', \App\Action\Categories\WebCategoriesUpdateAction::class);

        /*** USERS ***/
        $group->get('/users/list', \App\Action\Users\WebUsersListAction::class);
        $group->post('/users/update', \App\Action\Users\WebUsersUpdateAction::class);
        $group->post('/users/add', \App\Action\Users\WebUsersAddAction::class);
        $group->post('/users/delete', \App\Action\Users\WebUsersDeleteAction::class);

    })->add(UserAuthMiddleware::class);

    $app->group('/mobile/api', function (RouteCollectorProxy $group) {
        $group->get('/points/total', \App\Action\Points\TotalPointsAction::class);
        $group->get('/points/list', \App\Action\Points\ListPointsAction::class);
        $group->get('/transactions/list/{limit}', \App\Action\Transactions\TransactionListAction::class);
        $group->get('/transactions/detail/{trx_id}', \App\Action\Transactions\DetailTransactionAction::class);
        $group->get('/product/list/{category_id}', \App\Action\Products\ProductsListCategoryAction::class);
        $group->get('/product/latest', \App\Action\Products\ProductLatestAction::class);
        $group->get('/product/detail/{prod_id}', \App\Action\Products\ProductDetailAction::class);
        $group->get('/categories/list', \App\Action\Categories\CategoriesListAction::class);
        $group->post('/register/transaction', \App\Action\Transactions\AddTransactionAction::class);
        $group->get('/prize/latest', \App\Action\Prizes\LatestPrizesAction::class);
        $group->get('/prize/categories/list', \App\Action\Prizes\CategoriesListAction::class);
        $group->get('/prize/detail/{id}', \App\Action\Prizes\PrizeDetailAction::class);
        $group->get('/prize/claim/{prize_id}', \App\Action\Prizes\ClaimPrizeAction::class);
        $group->get('/claims/list', \App\Action\Prizes\ClaimHistoryAction::class);
        $group->get('/claim/detail/{claim_id}', \App\Action\Prizes\ClaimDetailAction::class);
        $group->get('/claim/qr_code', \App\Action\Points\QRCodeAction::class);
        $group->post('/profile/update', \App\Action\Users\MobileProfileUpdateAction::class);
    })->add(\App\Middleware\JwtAuthMiddleware::class);


    /** DO NOT DELETE, RESOLVE PREFLIGHT REQUEST PROBLEM... I know i should modify the CORS class, but im a lazy ass */
    $app->options('/payment-sheet', \App\CORS\CORSAction::class);
    $app->options('/mobile/api/points/total', \App\CORS\CORSAction::class);
    $app->options('/mobile/api/points/list', \App\CORS\CORSAction::class);
    $app->options('/mobile/api/transactions/list/{limit}', \App\CORS\CORSAction::class);
    $app->options('/mobile/api/product/list/{category_id}', \App\CORS\CORSAction::class);
    $app->options('/mobile/api/product/latest', \App\CORS\CORSAction::class);
    $app->options('/mobile/api/product/detail/{prod_id}', \App\CORS\CORSAction::class);
    $app->options('/mobile/api/categories/list', \App\CORS\CORSAction::class);
    $app->options('/mobile/api/register/transaction', \App\CORS\CORSAction::class);
    $app->options('/mobile/api/transactions/detail/{trx_id}', \App\CORS\CORSAction::class);
    $app->options('/mobile/api/prize/latest', \App\CORS\CORSAction::class);
    $app->options('/mobile/api/prize/categories/list', \App\CORS\CORSAction::class);
    $app->options('/mobile/api/prize/detail/{id}', \App\CORS\CORSAction::class);
    $app->options('/mobile/api/prize/claim/{prize_id}', \App\CORS\CORSAction::class);
    $app->options('/mobile/api/claims/list', \App\CORS\CORSAction::class);
    $app->options('/mobile/api/claim/detail/{claim_id}', \App\CORS\CORSAction::class);
    $app->options('/mobile/api/profile/update', \App\CORS\CORSAction::class);
};
