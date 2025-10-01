<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('Home', 'Home::index');

$routes->get('/login', 'Login::index');
$routes->post('/login/auth', 'Login::auth');
$routes->get('/logout', 'Login::logout');

$routes->get('/admin', 'Admin::index');
$routes->get('/student', 'Student::index');

// Student
$routes->get('/student', 'Student::index');
$routes->get('/student/enroll/(:num)', 'Student::enroll/$1');

// Admin
$routes->get('/admin', 'Admin::index');
$routes->post('/admin/addCourse', 'Admin::addCourse');





