<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(
	[

		'middleware' => 'api',
		'namespace' => 'App\Http\Controllers',
		'prefix'=>'auth'
	], 
	function($router){
		Route::post('login', 	'AuthController@login');
		Route::post('register', 'AuthController@register');
		Route::get('profile', 	'AuthController@profile')->middleware('auth');
		Route::post('profile_image_update', 	'AuthController@profile_image_update')->middleware('auth');
		Route::post('update', 	'AuthController@update')->middleware('auth');
		Route::post('logout', 	'AuthController@logout');
		Route::post('refresh', 	'AuthController@refresh');
		Route::delete('delete/{id}', 	'AuthController@delete')->middleware('jwt.verify');

		Route::post('forgot-password', 'AuthController@forgot_password')->name('password.reset');
		Route::post('change-password', 'AuthController@change_password')->middleware('auth');

	}
);

Route::group(
	[

		'middleware' => 'api',
		'namespace' => 'App\Http\Controllers',
		'prefix'=>'admin'
	], 
	function($router){
		Route::post('login', 	'AdminAuthController@login');
		Route::post('register', 'AdminAuthController@register');
		Route::get('profile', 	'AdminAuthController@profile');
		Route::post('logout', 	'AdminAuthController@logout');
		Route::post('refresh', 		'AdminAuthController@refresh');
		Route::delete('delete/{id}', 'AdminAuthController@delete')->middleware('jwt.verify');
	}
);

//
Route::group(
	[
		'middleware' => 'api',
		'namespace' => 'App\Http\Controllers',
		'prefix' => 'courses'
	],function($router){

		Route::get('/', 	'CourseController@index')->middleware('auth');
		Route::get('view/{id}', 	'CourseController@view')->middleware('auth');
		Route::put('update/{id}', 	'CourseController@update')->middleware('jwt.verify');
		Route::post('add', 				'CourseController@add')->middleware('jwt.verify');
		Route::delete('delete/{id}', 	'CourseController@delete')->middleware('jwt.verify');
		
	}
);




Route::group(
	[
		'middleware' => 'api',
		'namespace' => 'App\Http\Controllers',
		'prefix' => 'test_scores'
	],function($router){
		Route::get('view_per_user', 'TestScoreController@view_per_user')->middleware('auth');
		Route::get('/', 	'TestScoreController@all_scores')->middleware('jwt.verify');
		Route::post('add_flash_card', 'TestScoreController@add_flash_card')->middleware('auth');
		Route::post('add_mcq', 'TestScoreController@add_mcq')->middleware('auth');
		Route::post('add_test_score', 'TestScoreController@add_test_score')->middleware('auth');
		Route::delete('delete/{id}', 	'TestScoreController@delete')->middleware('jwt.verify');
		
	}
);



Route::group(
	[
		'middleware' => 'api',
		'namespace' => 'App\Http\Controllers',
		'prefix' => 'essay_dumps'
	],function($router){

		Route::get('/', 	'Essay_dumpController@index')->middleware('jwt.verify');
		Route::get('view/{id}', 	'Essay_dumpController@view')->middleware('jwt.verify');
		Route::post('add', 				'Essay_dumpController@add')->middleware('auth');
		Route::delete('delete/{id}', 	'Essay_dumpController@delete')->middleware('jwt.verify');
		
	}
);


Route::group(
	[
		'middleware' => 'api',
		'namespace' => 'App\Http\Controllers',
		'prefix' => 'flash_cards'
	],function($router){

		Route::get('/', 	'Flash_cardController@index')->middleware('auth');
		Route::get('course/{course_id}/topic/{topic_id}', 'Flash_cardController@view_flashcards_by_topic')->middleware('auth');

		Route::get('view/{id}', 	'Flash_cardController@view')->middleware('auth');
		Route::post('add', 				'Flash_cardController@add')->middleware('jwt.verify');
		Route::put('update/{id}', 	'Flash_cardController@update')->middleware('jwt.verify');
		Route::delete('delete/{id}', 	'Flash_cardController@delete')->middleware('jwt.verify');
		
	}
);


Route::group(
	[
		'middleware' => 'api',
		'namespace' => 'App\Http\Controllers',
		'prefix' => 'mcq_per_topics'
	],function($router){

		Route::get('/', 	'McqPerTopicController@index')->middleware('jwt.verify');
		Route::get('course/{course_id}/topic/{topic_id}', 'McqPerTopicController@view_by_topic')->middleware('auth');

		Route::get('view/{id}', 	'McqPerTopicController@view')->middleware('auth');
		Route::post('add', 				'McqPerTopicController@add')->middleware('jwt.verify');
		Route::put('update/{id}', 	'McqPerTopicController@update')->middleware('jwt.verify');
		Route::delete('delete/{id}', 	'McqPerTopicController@delete')->middleware('jwt.verify');
		
	}
);


Route::group(
	[
		'middleware' => 'api',
		'namespace' => 'App\Http\Controllers',
		'prefix' => 'mcq_generals'
	],function($router){
		Route::get('/', 	'McqGeneralController@index')->middleware('auth');
		Route::get('view/{id}', 	'McqGeneralController@view')->middleware('auth');
		Route::post('add', 				'McqGeneralController@add')->middleware('jwt.verify');
		Route::put('update/{id}', 	'McqGeneralController@update')->middleware('jwt.verify');
		Route::delete('delete/{id}', 	'McqGeneralController@delete')->middleware('jwt.verify');
		
	}
);



Route::group(
	[
		'middleware' => 'api',
		'namespace' => 'App\Http\Controllers',
		'prefix' => 'lecture_notes'
	],function($router){

		Route::get('/', 	'Lecture_noteController@index')->middleware('auth');
		Route::get('view/{id}', 	'Lecture_noteController@view')->middleware('auth');
		Route::get('course/{course_id}/topic/{topic_id}', 	'Lecture_noteController@view_by_topic')->middleware('auth');
		Route::post('add', 				'Lecture_noteController@add')->middleware('jwt.verify');
		Route::put('update/{id}', 	'Lecture_noteController@update')->middleware('jwt.verify');
		Route::delete('delete/{id}', 	'Lecture_noteController@delete')->middleware('jwt.verify');
		
	}
);


Route::group(
	[
		'middleware' => 'api',
		'namespace' => 'App\Http\Controllers',
		'prefix' => 'multichoice_questions'
	],function($router){

		Route::get('/', 	'MultichoiceQuestionController@index')->middleware('auth');
		Route::get('view/{course_id}/{year_id}', 	'MultichoiceQuestionController@view')->middleware('auth');
		Route::post('add', 				'MultichoiceQuestionController@add')->middleware('jwt.verify');
		Route::put('update/{id}', 	'MultichoiceQuestionController@update')->middleware('jwt.verify');
		Route::delete('delete/{id}', 	'MultichoiceQuestionController@delete')->middleware('jwt.verify');
		
	}
);

Route::group(
	[
		'middleware' => 'api',
		'namespace' => 'App\Http\Controllers',
		'prefix' => 'essay_study_questions'
	],function($router){

		Route::get('/', 	'EssayStudyQuestionController@index')->middleware('auth');
		Route::get('view/{course_id}/{year_id}', 	'EssayStudyQuestionController@view')->middleware('auth');
		Route::post('add', 				'EssayStudyQuestionController@add')->middleware('jwt.verify');
		Route::put('update/{id}', 	'EssayStudyQuestionController@update')->middleware('jwt.verify');
		Route::delete('delete/{id}', 	'EssayStudyQuestionController@delete')->middleware('jwt.verify');
		
	}
);


Route::group(
	[
		'middleware' => 'api',
		'namespace' => 'App\Http\Controllers',
		'prefix' => 'mcq_exam_years'
	],function($router){
		Route::get('/', 	'Mcq_exam_yearController@index')->middleware('auth');
		Route::get('view/{id}', 	'Mcq_exam_yearController@view')->middleware('auth');
		
		Route::post('add', 			'Mcq_exam_yearController@add')->middleware('jwt.verify');
		Route::put('update/{id}', 	'Mcq_exam_yearController@update')->middleware('jwt.verify');
		Route::delete('delete/{id}', 'Mcq_exam_yearController@delete')->middleware('jwt.verify');
	}
);



Route::group(
	[
		'middleware' => 'api',
		'namespace' => 'App\Http\Controllers',
		'prefix' => 'notifications'
	],function($router){
		Route::get('/', 	'NotificationController@index')->middleware('auth');
		Route::put('read/{id}', 	'NotificationController@read')->middleware('auth');
		Route::get('view/{id}', 	'NotificationController@view')->middleware('auth');
		Route::post('update_status', 'NotificationController@update_notification_status')->middleware('auth');
		Route::post('add', 				'NotificationController@add')->middleware('jwt.verify');
	}
);


Route::group(
	[
		'middleware' => 'api',
		'namespace' => 'App\Http\Controllers',
		'prefix' => 'subscriptions'
	],function($router){
		Route::get('/', 	'SubscriptionController@index')->middleware('auth');
		Route::post('upgradePlan', 	'SubscriptionController@upgradePlan')->middleware('auth');
		Route::get('view/{id}', 	'SubscriptionController@view')->middleware('auth');
		Route::put('update/{id}', 	'SubscriptionController@update')->middleware('jwt.verify');
	}
);



Route::group(
	[
		'middleware' => 'api',
		'namespace' => 'App\Http\Controllers',
		'prefix' => 'topics'
	],function($router){
		Route::get('{topic_type}/course/{id}', 	'TopicController@topic_type')->middleware('auth');
		Route::get('paid/{topic_type}/course/{id}', 'TopicController@topic_paid_only')->middleware('auth');

		Route::post('add', 	'TopicController@add')->middleware('jwt.verify');
		Route::get('view/{id}', 	'TopicController@view')->middleware('auth');
		Route::get('/', 	'TopicController@index')->middleware('auth');
		Route::put('update/{id}', 	'TopicController@update')->middleware('jwt.verify');
		Route::delete('delete/{id}', 	'TopicController@delete')->middleware('jwt.verify');
	}
);

Route::group(
	[
		'middleware' => 'api',
		'namespace' => 'App\Http\Controllers',
		'prefix' => 'chat'
	],function($router){
		$router->post('/user/add', 'MessageBoardController@addUserChat')->middleware('auth');
		$router->post('/admin/add', 'MessageBoardController@addAdminChat')->middleware('jwt.verify');
		$router->get('user/all','MessageBoardController@userViewMessages')->middleware('auth');
		$router->get('admin/all/{id}','MessageBoardController@adminViewMessages')->middleware('jwt.verify');
		$router->get('admin/all','MessageBoardController@adminListMessages')->middleware('jwt.verify');
		// Route::put('update/{id}', 	'SubscriptionController@update')->middleware('jwt.verify');
	}
);



Route::group(
	[
		'middleware' => 'api',
		'namespace' => 'App\Http\Controllers',
		'prefix' => 'cac_forms'
	],function($router){

		Route::get('/', 	'CacFormController@index')->middleware('auth');
		Route::get('view/{id}', 	'CacFormController@view')->middleware('auth');
		Route::post('add', 				'CacFormController@add')->middleware('jwt.verify');
		Route::put('update/{id}', 	'CacFormController@update')->middleware('jwt.verify');
		Route::delete('delete/{id}', 	'CacFormController@delete')->middleware('jwt.verify');
		
	}
);


Route::group(
	[
		'middleware' => 'api',
		'namespace' => 'App\Http\Controllers',
		'prefix' => 'code_of_conducts'
	],function($router){

		Route::get('/', 	'CodeOfConductController@index')->middleware('auth');
		Route::get('view/{id}', 	'CodeOfConductController@view')->middleware('auth');
		Route::post('add', 				'CodeOfConductController@add')->middleware('jwt.verify');
		Route::put('update/{id}', 	'CodeOfConductController@update')->middleware('jwt.verify');
		Route::delete('delete/{id}', 	'CodeOfConductController@delete')->middleware('jwt.verify');
		
	}
);



Route::group(
	[
		'middleware' => 'api',
		'namespace' => 'App\Http\Controllers',
		'prefix' => 'disciplinary_actions'
	],function($router){

		Route::get('/', 	'DisciplinaryActionController@index')->middleware('auth');
		Route::get('view/{id}', 	'DisciplinaryActionController@view')->middleware('auth');
		Route::post('add', 				'DisciplinaryActionController@add')->middleware('jwt.verify');
		Route::put('update/{id}', 	'DisciplinaryActionController@update')->middleware('jwt.verify');
		Route::delete('delete/{id}', 	'DisciplinaryActionController@delete')->middleware('jwt.verify');
		
	}
);


Route::group(
	[
		'middleware' => 'api',
		'namespace' => 'App\Http\Controllers',
		'prefix' => 'dress_codes'
	],function($router){

		Route::get('/', 	'DressCodeController@index')->middleware('auth');
		Route::get('view/{id}', 	'DressCodeController@view')->middleware('auth');
		Route::post('add', 				'DressCodeController@add')->middleware('jwt.verify');
		Route::put('update/{id}', 	'DressCodeController@update')->middleware('jwt.verify');
		Route::delete('delete/{id}', 	'DressCodeController@delete')->middleware('jwt.verify');
		
	}
);


Route::group(
	[
		'middleware' => 'api',
		'namespace' => 'App\Http\Controllers',
		'prefix' => 'probates'
	],function($router){

		Route::get('/', 	'ProbateController@index')->middleware('auth');
		Route::get('view/{id}', 	'ProbateController@view')->middleware('auth');
		Route::post('add', 				'ProbateController@add')->middleware('jwt.verify');
		Route::put('update/{id}', 	'ProbateController@update')->middleware('jwt.verify');
		Route::delete('delete/{id}', 	'ProbateController@delete')->middleware('jwt.verify');
		
	}
);

Route::group(
	[
		'middleware' => 'api',
		'namespace' => 'App\Http\Controllers',
		'prefix' => 'statutes'
	],function($router){

		Route::get('/', 	'StatuteController@index')->middleware('auth');
		Route::get('view/{id}', 	'StatuteController@view')->middleware('auth');
		Route::post('add', 				'StatuteController@add')->middleware('jwt.verify');
		Route::put('update/{id}', 	'StatuteController@update')->middleware('jwt.verify');
		Route::delete('delete/{id}', 	'StatuteController@delete')->middleware('jwt.verify');
		
	}
);



Route::group(
	[
		'middleware' => 'api',
		'namespace' => 'App\Http\Controllers',
		'prefix' => 'case_notes'
	],function($router){

		Route::get('/', 	'CaseNoteController@index')->middleware('auth');
		Route::get('view/{id}', 	'CaseNoteController@view')->middleware('auth');
		Route::post('add', 				'CaseNoteController@add')->middleware('jwt.verify');
		Route::put('update/{id}', 	'CaseNoteController@update')->middleware('jwt.verify');
		Route::delete('delete/{id}', 	'CaseNoteController@delete')->middleware('jwt.verify');
		
	}
);


Route::group(
	[
		'middleware' => 'api',
		'namespace' => 'App\Http\Controllers',
		'prefix' => 'bar_compasses'
	],function($router){

		Route::get('/', 	'BarCompassController@index')->middleware('auth');
		Route::get('view/{id}', 	'BarCompassController@view')->middleware('auth');
		Route::post('add', 				'BarCompassController@add')->middleware('jwt.verify');
		Route::put('update/{id}', 	'BarCompassController@update')->middleware('jwt.verify');
		Route::delete('delete/{id}', 	'BarCompassController@delete')->middleware('jwt.verify');
		
	}
);



Route::group(
	[
		'middleware' => 'api',
		'namespace' => 'App\Http\Controllers',
		'prefix' => 'subscribers'
	],function($router){

		Route::post('add', 				'SubscriberController@add');
		
	}
);














