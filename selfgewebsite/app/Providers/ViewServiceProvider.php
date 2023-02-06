<?php
/**
 *  app/Providers/ViewServiceProvider.php
 *
 * Date-Time: 07.06.21
 * Time: 13:26
 * @author Insite LLC <hello@insite.international>
 */

namespace App\Providers;

use App\Http\View\Composers\LanguageComposer;
use App\Http\View\Composers\SettingComposer;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

/**
 * Class ViewServiceProvider
 * @package App\Providers
 */
class ViewServiceProvider extends ServiceProvider
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
        View::composer(['admin.panels.navbar'], LanguageComposer::class);
        View::composer(['client.layout.partial.footer', 'client.pages.contact.index'], SettingComposer::class);

    }
}
