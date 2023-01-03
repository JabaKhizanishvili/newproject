<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\SliderController1;
use App\Http\Controllers\Admin\PartnersController;
use App\Http\Controllers\Admin\UpcomingEventsController;
use App\Http\Controllers\Admin\TranslationController;
use App\Http\Controllers\CKEditorController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Client\AboutUsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\EvaluationController;
use App\Http\Controllers\Client\ServiceController;
use App\Http\Controllers\Client\PortfolioController;
use App\Http\Controllers\Client\DocumentationController;
use App\Http\Controllers\IconController;
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

                //portfolio

                Route::resource('portfolio', \App\Http\Controllers\Admin\PortfolioController::class);
                Route::get('portfolio/{portfolio}/destroy', [\App\Http\Controllers\Admin\PortfolioController::class, 'destroy'])->name('portfolio.destroy');

                //news
                Route::resource('news', NewsController::class);
                Route::get('news/{news}/destroy', [NewsController::class, 'destroy'])->name('news.destroy');

                //blog
                Route::resource('blog', BlogController::class);
                Route::get("/blog/{blog?}/destroy", [BlogController::class, 'destroy'])->name('blog.destroy');

                //partners
                Route::resource('partner', PartnersController::class);
                Route::get("/partner/{partner?}/destroy", [PartnersController::class, 'destroy'])->name('partner.destroy');

                //menu
                Route::resource('menu', MenuController::class);
                Route::get("/menu/{menu?}/destroy", [MenuController::class, 'destroy'])->name('menu.destroy');

                // Language
                Route::resource('language', LanguageController::class);
                Route::get('language/{language}/destroy', [LanguageController::class, 'destroy'])->name('language.destroy');

                // Translation
                Route::resource('translation', TranslationController::class);


                // Slider
                Route::resource('slider', SliderController::class);
                Route::get('slider/{slider}/destroy', [SliderController::class, 'destroy'])->name('slider.destroy');
                //slider1
                Route::resource('slider1', SliderController1::class);
                Route::get('slider1/{slider1}/destroy', [SliderController1::class, 'destroy'])->name('slider1.destroy');


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

                // bookvisit
                Route::resource('bookvisit', \App\Http\Controllers\Admin\BookvisitController::class);
                Route::get('bookvisit/{id}', [BookvisitController::class, 'details'])->name('bookvisit.details');

                //Ray Content

                Route::resource('raycontent', \App\Http\Controllers\Admin\ContentController::class);
                // Route::get('raycontent/{id}', [ContentController::class, 'details'])->name('eval.details');
                Route::get('raycontent/{raycontent}/destroy', [ContentController::class, 'destroy'])->name('raycontent.destroy');

                // Password
                Route::get('password', [\App\Http\Controllers\Admin\PasswordController::class, 'index'])->name('password.index');
                Route::post('password', [\App\Http\Controllers\Admin\PasswordController::class, 'update'])->name('password.update');
            });
        });
        Route::middleware(['active'])->group(function () {
            // Home Page
            Route::get('', [HomeController::class, 'index'])->name('client.home.index')->withoutMiddleware('active');
            // Route::get('menu/{menu?}', [HomeController::class, 'menu'])->name('client.home.menu')->withoutMiddleware('active');
            Route::get('{menu?}', [HomeController::class, 'menu'])->name('client.home.menu')->withoutMiddleware('active');
            //blog
            Route::get("blog/{id?}", [HomeController::class, 'singleblog'])->name('client.singleblog')->withoutMiddleware('active');
            Route::get('vaccancy/{SingleVaccancy?}', [HomeController::class, 'SingleVaccancy'])->name('client.SingleVaccancy')->withoutMiddleware('active');

            Route::get('/bookvisit', [HomeController::class, 'bookvisit'])->name('client.bookvisit')->withoutMiddleware('active');
            Route::get('Vaccancy', [HomeController::class, 'vaccancy'])->name('client.vaccancy')->withoutMiddleware('active');
            Route::post('/bookvisitform', [HomeController::class, 'bookvisitform'])->name('client.bookvisitform')->withoutMiddleware('active');

            //blog
            Route::get("blog", [HomeController::class, 'blog'])->name('client.blog')->withoutMiddleware('active');
            // Route::get("singleblog", [HomeController::class, 'singleblog'])->name('client.singleblog')->withoutMiddleware('active');
            Route::post("blogicons", [IconController::class, 'addImage'])->name('uploadicon');
            //search

            // Route::get('search', [TilesController::class, 'search'])->name('client.search.index')->withoutMiddleware('active');
        });
    });
