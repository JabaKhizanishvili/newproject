<?php
/**
 *  app/Http/View/Composers/LanguageComposer.php
 *
 * Date-Time: 07.06.21
 * Time: 13:22
 * @author Insite LLC <hello@insite.international>
 */

namespace App\Http\View\Composers;


use App\Models\Language;
use App\Models\Setting;
use Illuminate\View\View;

/**
 * Class LanguageComposer
 * @package App\Http\View\Composers
 */
class SettingComposer
{
    /**
     * Bind data to the view.
     *
     * @param View $view
     *
     * @return void
     */
    public function compose(View $view)
    {
        $gphone = "";
        $gemail = "";
        $gaddress = "";
        $gworkingHours = "";
        $ginstagram = "";
        $gfacebook = "";
        $gyoutube = "";

        $settings = Setting::query()->with(['translations']);
        $settings = $settings->get();
        foreach ($settings as $setting){
            switch ($setting->key){
                case "phone":
                    $gphone = $setting;
                    break;
                case "email":
                    $gemail = $setting;
                    break;
                case "address":
                    $gaddress = $setting;
                    break;
                case "working_hours":
                    $gworkingHours = $setting;
                    break;
                case "instagram":
                    $ginstagram = $setting;
                    break;
                case "facebook":
                    $gfacebook = $setting;
                    break;
                case "youtube":
                    $gyoutube = $setting;
                    break;
            }
        }
//        dd($gemail->value);

        $view
            ->with('gaddress',$gaddress->value)
            ->with('ginstagram',$ginstagram->value)
            ->with('gfacebook',$gfacebook->value)
            ->with('gyoutube',$gyoutube->value)
            ->with('gemail',$gemail->value)
            ->with('gphone',$gphone->value)
            ->with("gworkingHours",$gworkingHours->value);
    }


}
