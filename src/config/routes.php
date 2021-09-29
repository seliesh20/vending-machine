<?php
$routes->get('/', function(){
    echo "Vendor Machine";
});

// USER API
$routes->post('/user/create', 'UserController@create')->requires('APIKEY');
$routes->get('/user/view/$user_id', 'UserController@view')->requires('APIKEY');
$routes->post('/user/update/$user_id', 'UserController@update');
$routes->delete('/user/$user_id', 'UserController@delete')->requires('APIKEY');

//Authentication 
$routes->post('/login', 'AuthController@login');
$routes->post('/logout', 'AuthController@logout');

// PRODUCT API
$routes->post('/product/create', 'ProductController@create')->requires('APIKEY');
$routes->get('/product/view/$product_id', 'ProductController@view');
$routes->post('/product/update/$product_id', 'ProductController@update')->requires('APIKEY');
$routes->delete('/product/delete/$product_id', 'ProductController@delete')->requires('APIKEY');

// TRANSACTION API
$routes->post('/deposit', 'TransactionController@deposit')->requires('APIKEY');
$routes->post('/buy', 'TransactionController@buy')->requires('APIKEY');
$routes->post('/reset', 'TransactionController@reset')->requires('APIKEY');
