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
$route['login'] = 'auth/login';
$route['lupa-password'] = 'auth/lupa_password';
$route['logout'] = 'auth/logout';
$route['setup'] = 'setup/index';
$route['dashboard'] = 'dashboard/index';
$route['admin/warga'] = 'admin/warga';
$route['admin/warga/status/(:num)'] = 'admin/status_warga/$1';
$route['admin/warga/hapus/(:num)'] = 'admin/hapus_warga/$1';
$route['admin/pembayaran'] = 'admin/pembayaran';
$route['admin/pembayaran/bukti/(:num)'] = 'admin/bukti/$1';
$route['admin/iuran'] = 'admin/iuran';
$route['admin/buku-kas'] = 'admin/buku_kas';
$route['admin/buku-kas/cetak'] = 'admin/cetak_kas';
$route['admin/users'] = 'admin/users';
$route['admin/setting'] = 'admin/setting';
$route['warga/history'] = 'warga/history';
$route['warga/pembayaran'] = 'warga/pembayaran';
$route['warga/buku-kas'] = 'warga/buku_kas';
$route['warga/bukti/(:num)'] = 'warga/bukti/$1';
$route['warga/notifikasi/(:num)'] = 'warga/baca_notifikasi/$1';
$route['warga/setting'] = 'warga/setting';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
