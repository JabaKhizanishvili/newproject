<?php
/**
 *  app/Providers/MenuServiceProvider.php
 *
 * Date-Time: 03.06.21
 * Time: 16:41
 * @author Insite LLC <hello@insite.international>
 */
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class MenuServiceProvider
 * @package App\Providers
 */
class MenuServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // get all data from menu.json file
        $verticalMenuJson = file_get_contents(base_path('resources/json/verticalMenu.json'));
        $verticalMenuData = json_decode($verticalMenuJson);

        $horizontalMenuJson = file_get_contents(base_path('resources/json/horizontalMenu.json'));
        $horizontalMenuData = json_decode($horizontalMenuJson);

        // share all menuData to all the views
        \View::share('menuData', [$verticalMenuData, $horizontalMenuData]);


        require_once(app()->basePath().'/app/Helpers/locale_route.php');
        require_once(app()->basePath().'/app/Helpers/get_url.php');
    }
}
