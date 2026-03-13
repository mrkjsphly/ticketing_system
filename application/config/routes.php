<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'auth/login';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
$route['admin/user/check-username'] = 'admin/user/check_username';
$route['csr/dashboard'] = 'csr/dashboard';
$route['tickets/endorse'] = 'tickets/endorse';
$route['tech/dashboard'] = 'tech/dashboard';
$route['tech/tickets']   = 'tech/tickets';
$route['tech/get_ticket/(:num)']    = 'tech/get_ticket/$1';
$route['tech/update_status/(:num)'] = 'tech/update_status/$1';
$route['admin/teams']                = 'admin/teams/index';
$route['admin/teams/store']          = 'admin/teams/store';
$route['admin/teams/update/(:num)']  = 'admin/teams/update/$1';
$route['admin/teams/delete/(:num)']  = 'admin/teams/delete/$1';
$route['admin/teams/members/(:num)'] = 'admin/teams/members/$1';
$route['tickets/confirm_closure/(:num)'] = 'tickets/confirm_closure/$1';
$route['accounting/dashboard']              = 'accounting/dashboard';
$route['accounting/tickets']                = 'accounting/tickets';
$route['accounting/get_ticket/(:num)']      = 'accounting/get_ticket/$1';
$route['accounting/update_status/(:num)']   = 'accounting/update_status/$1';
$route['tl/dashboard']             = 'tl/dashboard';
$route['tl/tickets']               = 'tl/tickets';
$route['tl/get_ticket/(:num)']     = 'tl/get_ticket/$1';
$route['tl/close_ticket/(:num)']   = 'tl/close_ticket/$1';
$route['tl/reassign/(:num)']       = 'tl/reassign/$1';