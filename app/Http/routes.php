<?php
Route::get('/form', ['as' => 'diff.form', 'uses' => 'DiffController@form']);
Route::get('/show', ['as' => 'diff.show', 'uses' => 'DiffController@show']);
Route::post('/execute', ['as' => 'diff.execute', 'uses' => 'DiffController@execute']);
