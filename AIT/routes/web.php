<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::get('/about','HomePages\AboutController@index')->name('home.about');
Route::get('/contact','HomePages\ContactController@index')->name('home.contact');
Route::get('/', 'HomePages\HomeController@index')->name('home.home');
Route::post('/send-email','HomePages\ContactController@sendEmail' )->name('contact-mail');
Route::get('/allowPermisionSubject/{sid}/{tid}','HomePages\HomeController@allowPermisionSubject');

Auth::routes();

// Route::get('/home', 'HomeController@index')->name('home');
Route::resource('admin', 'Admin\AdminController');
// Route::post('/adminProfileUpload','Admin\AdminController@profileUpload')->name('adminProfileUpload');



Route::resource('teacher', 'Teacher\TeacherController');
// Route::post('/teacherProfileUpload','Teacher\TeacherController@profileUpload')->name('teacherProfileUpload');
Route::get('/allTeacherDetails','Teacher\TeacherController@getAllDetails')->name('teacherGetAllDetails');
Route::get('/classes','Teacher\TeacherController@showClasses')->name('showClasses');
Route::get('/attendance','Teacher\TeacherController@attendanceShowClasses')->name('attendanceShowClasses');
Route::get('/resources','Teacher\TeacherController@resourcesShowClasses')->name('resourcesShowClasses');
Route::get('/resourcesUploadForm/{grade}','Teacher\TeacherController@resourcesUploadForm')->name('resourcesUploadForm');
Route::post('/resourcesUpload/{grade}','Teacher\TeacherController@resourcesUpload')->name('resourcesUpload');
Route::post('/addClass','Teacher\TeacherController@addClasses')->name('addClasses');
Route::get('/viewResources/{grade}/{teacher}','Teacher\TeacherController@viewResources')->name('viewResources');


Route::resource('student', 'Student\StudentController');
// Route::post('/studentProfileUpload','Student\StudentController@profileUpload')->name('studentProfileUpload');
Route::get('/allStudentDetails','Student\StudentController@getAllDetails')->name('studentGetAllDetails');
Route::get('/subjects','Student\StudentController@showSubjects')->name('showSubjects');
Route::post('/getPermisionSubject','Student\StudentController@getPermisionSubject')->name('getPermisionSubject');



Route::resource('parent', 'Parent\ParentController');
// Route::post('/parentProfileUpload','Parent\ParentController@profileUpload')->name('parentProfileUpload');
Route::get('/allParentDetails','Parent\ParentController@getAllDetails')->name('parentGetAllDetails');
Route::get('/linkStudentToParent/{id}','Parent\ParentController@linkStudent')->name('linkStudent');
Route::get('/linkedStudentList/{id}','Parent\ParentController@getLinkedStudent')->name('getLinkedStudent');

Route::get('/comments','ChatSystem\CommentController@index')->name('comments');

