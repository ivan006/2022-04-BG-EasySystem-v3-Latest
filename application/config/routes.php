<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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
|	https://codeigniter.com/user_guide/general/routing.html
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
| Examples:	my-controller/index	-> CI_Controller/index
|		my-controller/my-method	-> CI_Controller/my_method
*/
$route['default_controller'] = 'database_c/databases';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;




// /auth/index()
// /auth/login()
// /auth/logout()
// /auth/change_password()
// /auth/forgot_password()
// /auth/reset_password($code = NULL)
// /auth/activate($id, $code = FALSE)
// /auth/deactivate($id = NULL)
// /auth/create_user()
// /auth/redirectUser()
// /auth/edit_user($id)
// /auth/create_group()
// /auth/edit_group($id)
// /auth/_get_csrf_nonce()
// /auth/_valid_csrf_nonce()
// /auth/_render_page($view, $data = NULL, $returnhtml = FALSE)//I think this makes more sense



$route['erd/d/(:any)'] = "erd_c/index/$1";


$route['databases'] = 'database_c/databases';
$route['databases_api'] = 'database_c/databases_api';

$route['database/d/(:any)'] = 'database_c/database/$1';
$route['database_api/d/(:any)'] = 'database_c/database_api/$1';

$route['table/d/(:any)/t/(:any)'] = 'table_c/index/$1/$2';

$route['api/table/d/(:any)/t/(:any)/insert'] = 'table_c/insert/$1/$2';
$route['api/table/d/(:any)/t/(:any)/fetch'] = 'table_c/fetch/$1/$2';
$route['api/table_ssp/d/(:any)/t/(:any)/fetch'] = 'table_c/fetch_ssp/$1/$2';
// $route['api/table/t/(:any)/fetch_without_inheritance'] = 'table_c/fetch_without_inheritance/$1';
$route['api/table/d/(:any)/t/(:any)/delete'] = 'table_c/delete/$1/$2';
$route['api/table/d/(:any)/t/(:any)/edit'] = 'table_c/edit/$1/$2';
$route['api/table/d/(:any)/t/(:any)/update'] = 'table_c/update/$1/$2';
$route['api/table/d/(:any)/t/(:any)/fetch_for_record/h_type/(:any)/h/(:any)/n/(:any)'] = 'table_c/fetch_for_record/$1/$2/$3/$4/$5';
$route['api/table_ssp/d/(:any)/t/(:any)/fetch_for_record/h_type/(:any)/h/(:any)/n/(:any)'] = 'table_c/fetch_for_record_ssp/$1/$2/$3/$4/$5';
// $route['api/table/t/(:any)/fetch_join_where/t/(:any)/h/(:any)/n/(:any)'] = 'table_c/fetch_join_where/$1/$2/$3/$4';
// $route['api/table/t/(:any)/fetch_join_where/t/(:any)/h/(:any)/n/(:any)'] = 'table_c/fetch_join_where/$1/$2/$3/$4';

$route['record/d/(:any)/t/(:any)/r/(:num)'] = 'record_c/index/$1/$2/$3';
