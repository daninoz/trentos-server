<?php

$app->post('api/auth/login', 'AuthController@login');
$app->post('api/auth/facebook', 'AuthController@facebook');
$app->post('api/auth/register', 'AuthController@register');
$app->get('api/sports', 'SportController@index');

$app->get('api/events', 'EventController@index');
$app->get('api/events?{page}', 'EventController@index');

$app->group(['prefix' => 'api', 'middleware' => 'auth', 'namespace' => 'App\Http\Controllers'], function () use ($app) {
    $app->get('events/statistics', 'EventController@statistics');

    $app->get('feed', 'MeController@getFeed');

    $app->post('events', 'EventController@store');
    $app->get('events/today', 'EventController@getToday');
    $app->get('events/{id}', 'EventController@get');
    $app->put('events/{id}', 'EventController@update');
    $app->patch('events/{id}', 'EventController@highlight');
    $app->delete('events/{id}', 'EventController@destroy');
    $app->put('events/{id}/likes', 'EventController@like');

    $app->get('sports/{id}/events', 'SportController@events');
    $app->get('sports/{id}/events?{page}', 'SportController@events');

    $app->post('events/{id}/comments', 'CommentController@store');
    $app->put('events/comments/{id}', 'CommentController@update');

    $app->get('me', 'MeController@get');
    $app->put('me/sports', 'MeController@updateSubscriptions');
});