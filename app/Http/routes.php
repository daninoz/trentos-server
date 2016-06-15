<?php

$app->post('auth/facebook', 'AuthController@facebook');

$app->group(['prefix' => 'api', 'middleware' => 'auth', 'namespace' => 'App\Http\Controllers'], function () use ($app) {
    $app->get('events', 'EventController@index');
    $app->post('events', 'EventController@store');
    $app->put('events/{id}', 'EventController@update');
    //$app->delete('events/{id}', 'EventController@destroy');
    $app->put('events/{id}/likes', 'EventController@like');

    $app->get('sports', 'SportController@index');
    $app->post('sports', 'SportController@store');
    $app->put('sports/{id}', 'SportController@update');
    //$app->delete('sports/{id}', 'SportController@destroy');
    $app->get('sports/{id}/events', 'SportController@events');

    $app->post('events/{id}/comments', 'CommentController@store');
    $app->put('events/comments/{id}', 'CommentController@update');
    //$app->delete('events/{id}/comments', 'CommentController@destroy');

    //$app->get('me/subscriptions/events', 'MeController@subscriptionsEvents');
    //$app->get('me', 'MeController@get');
});