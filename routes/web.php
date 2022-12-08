<?php

/**
 *  routes/web.php
 *
 * Date-Time: 03.06.21
 * Time: 15:41
 * @author Insite LLC <hello@insite.international>
 */

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\SliderController1;
use App\Http\Controllers\Admin\UpcomingEventsController;
use App\Http\Controllers\Admin\TranslationController;
use App\Http\Controllers\CKEditorController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\RayController;
use App\Http\Controllers\Client\ContactController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Client\AboutUsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\EvaluationController;
use App\Http\Controllers\Client\ServiceController;
use App\Http\Controllers\Client\PortfolioController;
use App\Http\Controllers\Client\DocumentationController;
use App\Http\Controllers\Client\OurTeamController;
use App\Http\Controllers\Client\LoginPageController;




Route::post('ckeditor/image_upload', [CKEditorController::class, 'upload'])->name('upload');

Route::redirect('', config('translatable.fallback_locale'));
Route::prefix('{locale?}')
    ->middleware(['setlocale'])
    ->group(function () {
        Route::prefix('adminpanel')->group(function () {
            Route::get('login', [LoginController::class, 'loginView'])->name('loginView');
            Route::post('login', [LoginController::class, 'login'])->name('login');


            Route::middleware('auth')->group(function () {
                Route::get('logout', [LoginController::class, 'logout'])->name('logout');

                Route::redirect('', 'adminpanel/page');

                //news
                Route::resource('news', NewsController::class);
                Route::get('news/{news}/destroy', [NewsController::class, 'destroy'])->name('news.destroy');

                // Language
                Route::resource('language', LanguageController::class);
                Route::get('language/{language}/destroy', [LanguageController::class, 'destroy'])->name('language.destroy');

                // Translation
                Route::resource('translation', TranslationController::class);

                // Category
                Route::resource('category', \App\Http\Controllers\Admin\CategoryController::class);
                Route::get('category/{category}/destroy', [\App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('category.destroy');

                //RayAcademy
                // Route::resource('rayacademy', \App\Http\Controllers\Admin\CategoryController::class);
                // Route::get('rayacademy/{rayacademy}/destroy', [\App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('rayacademy.destroy');


                Route::resource('staff', \App\Http\Controllers\Admin\StaffController::class);
                Route::get('staff/{staff}/destroy', [\App\Http\Controllers\Admin\StaffController::class, 'destroy'])->name('staff.destroy');

                //portfolio

                Route::resource('portfolio', \App\Http\Controllers\Admin\PortfolioController::class);
                Route::get('portfolio/{portfolio}/destroy', [\App\Http\Controllers\Admin\PortfolioController::class, 'destroy'])->name('portfolio.destroy');

                //team
                Route::resource('team', \App\Http\Controllers\Admin\TeamController::class);
                Route::get('team/{team}/destroy', [\App\Http\Controllers\Admin\TeamController::class, 'destroy'])->name('team.destroy');



                // Product
                Route::resource('product', \App\Http\Controllers\Admin\ProductController::class);
                Route::get('product/{product}/destroy', [\App\Http\Controllers\Admin\ProductController::class, 'destroy'])->name('product.destroy');

                // Slider
                Route::resource('slider', SliderController::class);
                Route::get('slider/{slider}/destroy', [SliderController::class, 'destroy'])->name('slider.destroy');
                //slider1
                Route::resource('slider1', SliderController1::class);
                Route::get('slider1/{slider1}/destroy', [SliderController1::class, 'destroy'])->name('slider.destroy');


                // upcoming events
                Route::resource('upcomingevents', UpcomingEventsController::class);
                Route::get('upcomingevents/{upcomingevents}/destroy', [UpcomingEventsController::class, 'destroy'])->name('upcomingevents.destroy');


                // Page
                Route::resource('page', PageController::class);
                Route::get('page/{page}/destroy', [PageController::class, 'destroy'])->name('page.destroy');
                Route::get('page/doc/{doc}/destroy', [PageController::class, 'docDelete'])->name('page.delete-doc');


                Route::get('setting/active', [SettingController::class, 'setActive'])->name('setting.active');
                // Setting
                Route::resource('setting', SettingController::class);
                Route::get('setting/{setting}/destroy', [SettingController::class, 'destroy'])->name('setting.destroy');

                Route::resource('customer', \App\Http\Controllers\Admin\CustomerController::class);
                Route::get('customer/{customer}/destroy', [\App\Http\Controllers\Admin\CustomerController::class, 'destroy'])->name('customer.destroy');
                Route::get('customer/doc/{doc}/destroy', [\App\Http\Controllers\Admin\CustomerController::class, 'docDelete'])->name('customer.delete-doc');
                Route::get('customer/subclass/{customer}/create', [\App\Http\Controllers\Admin\CustomerController::class, 'createSubClass'])->name('subclass.create');
                Route::post('customer/subclass/{customer}/store', [\App\Http\Controllers\Admin\CustomerController::class, 'storeSubClass'])->name('subclass.store');
                Route::get('customer/subclass/{customer}/{subclass}/edit', [\App\Http\Controllers\Admin\CustomerController::class, 'editSubClass'])->name('subclass.edit');
                Route::put('customer/subclass/{customer}/{subclass}/edit', [\App\Http\Controllers\Admin\CustomerController::class, 'updateSubClass'])->name('subclass.update');
                Route::get('customer/subclass/{customer}/{subclass}/destroy', [\App\Http\Controllers\Admin\CustomerController::class, 'destroySubClass'])->name('subclass.destroy');
                Route::get('customer/subclass/{customer}/{subclass}/{doc}/destroy', [\App\Http\Controllers\Admin\CustomerController::class, 'subclassDocDelete'])->name('subclass.delete-doc');

                //evaluation
                Route::resource('courseorder', \App\Http\Controllers\Admin\EvaluationController::class);
                Route::get('courseorder/{id}', [EvaluationController::class, 'details'])->name('eval.details');

                //Ray Content

                Route::resource('raycontent', \App\Http\Controllers\Admin\ContentController::class);
                // Route::get('raycontent/{id}', [ContentController::class, 'details'])->name('eval.details');
                Route::get('raycontent/{raycontent}/destroy', [ContentController::class, 'destroy'])->name('raycontent.destroy');

                // Password
                Route::get('password', [\App\Http\Controllers\Admin\PasswordController::class, 'index'])->name('password.index');
                Route::post('password', [\App\Http\Controllers\Admin\PasswordController::class, 'update'])->name('password.update');

                Route::resource('classification', \App\Http\Controllers\Admin\ClassificationController::class);
                Route::get('classification/{classification}/destroy', [\App\Http\Controllers\Admin\ClassificationController::class, 'destroy'])->name('classification.destroy');
            });
        });
        Route::middleware(['active'])->group(function () {

            // Home Page
            Route::get('', [HomeController::class, 'index'])->name('client.home.index')->withoutMiddleware('active');


            Route::post('rateservices', [DocumentationController::class, 'add_rateservices'])->name('client.documentations.rateservices');
            // Contact Page
            Route::get('/contact', [ContactController::class, 'index'])->name('client.contact.index');
            // Route::post('/contact-us', [ContactController::class, 'mail'])->name('client.contact.mail');
            // contact us from navbar
            Route::post('/contactus', [ContactController::class, 'ContactMail'])->name('client.contact.contactmail');

            // About Page
            Route::get('aboutus', [LoginPageController::class, 'aboutus'])->name('client.aboutus');
            // partners page
            Route::get('partners', [LoginPageController::class, 'partners'])->name('client.partners.index');


            // Ray Compaies
            Route::get('rayacademy', [RayController::class, 'index'])->name('client.rayacademy');
            Route::get('rayacademy/{course?}', [RayController::class, 'show'])->name('client.rayacademy.show');
            Route::get('rayproductrion', [RayController::class, 'rayproductrion'])->name('client.rayproductrion');
            Route::get('rayanimation', [RayController::class, 'rayanimation'])->name('client.rayanimation');
            Route::get('raycrypto', [RayController::class, 'raycrypto'])->name('client.raycrypto');


            //news
            Route::get('/news', [\App\Http\Controllers\Client\NewsController::class, 'index'])->name('client.news.index');
            Route::get('/news/{news}', [\App\Http\Controllers\Client\NewsController::class, 'show'])->name('client.news.show');

            //rental routes
            Route::get("/rental", [\App\Http\Controllers\Client\ProductController::class, 'index'])->name('client.rental.index');
            Route::get("/rental/{rental}", [\App\Http\Controllers\Client\ProductController::class, 'show'])->name('client.rental.show');

            //portfolio
            Route::get('/projects', [\App\Http\Controllers\PortfolioController::class, 'index'])->name('client.project.index');
            Route::get('/projectssearch{search}/{item}', [\App\Http\Controllers\PortfolioController::class, 'search'])->name('client.project.show');
            Route::get('/projects/ssearch/{search}/{item?}', [\App\Http\Controllers\PortfolioController::class, 'searchProject'])->name('client.projects.show');
            Route::get('project/projectsdetails/{projectsdetails?}', [\App\Http\Controllers\PortfolioController::class, 'singleproject'])->name('client.showsingleproject.show');


            //search

            // Route::get('search', [TilesController::class, 'search'])->name('client.search.index')->withoutMiddleware('active');
        });
    });
