<?php

$app->post('api/auth/login', 'AuthController@login');
$app->post('api/auth/register', 'AuthController@register');
$app->get('api/sports', 'SportController@index');

$app->group(['prefix' => 'api', 'middleware' => 'auth', 'namespace' => 'App\Http\Controllers'], function () use ($app) {
    $app->get('events/statistics', 'EventController@statistics');

    $app->get('events', 'EventController@index');
    $app->post('events', 'EventController@store');
    $app->get('events/{id}', 'EventController@get');
    $app->put('events/{id}', 'EventController@update');
    $app->patch('events/{id}', 'EventController@highlight');
    $app->delete('events/{id}', 'EventController@destroy');
    $app->put('events/{id}/likes', 'EventController@like');

    $app->post('sports', 'SportController@store');
    $app->put('sports/{id}', 'SportController@update');
    //$app->delete('sports/{id}', 'SportController@destroy');
    $app->get('sports/{id}/events', 'SportController@events');

    $app->post('events/{id}/comments', 'CommentController@store');
    $app->put('events/comments/{id}', 'CommentController@update');
    //$app->delete('events/{id}/comments', 'CommentController@destroy');

    //$app->get('me/subscriptions/events', 'MeController@subscriptionsEvents');
    $app->get('me', 'MeController@get');
});