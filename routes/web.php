<?php

use Illuminate\Support\Facades\Route;
//use Mailgun\Mailgun;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/actualizar-proveedores','ProvidersController@actualizar');
Route::get('/actualizar-ventas','SalesController@actualizar');
Route::get('/kanulki','SalesController@kanulki');


Route::get('/api/provider', 'ProvidersController@index');

Route::get('send_test_email', function(){
	//require 'vendor/autoload.php';
	# Instantiate the client.
	$mgClient = new Mailgun('58ec539b3c5dfb426742c23f48df0b4f-a65173b1-a19b17c6');
	$domain = "sandboxc609b5d0e8b14f009edfddcbc6b79c0e.mailgun.org";
	# Make the call to the client.
	$result = $mgClient->sendMessage($domain, array(
		'from'	=> 'Excited User <mailgun@sandboxc609b5d0e8b14f009edfddcbc6b79c0e.mailgun.org>',
		'to'	=> 'Baz <diegoc3327.db@gmail.com>',
		'subject' => 'Hello',
		'text'	=> 'Testing some Mailgun awesomness!'
	));
});