<?php
/**
 *  app/Http/Controllers/Controller.php
 *
 * Date-Time: 07.06.21
 * Time: 17:10
 * @author Insite LLC <hello@insite.international>
 */

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * Class Controller
 * @package App\Http\Controllers
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function activeLanguages()
    {
        return Language::where('status', true)->orderBy('default', 'DESC')->get()->keyBy('locale');
    }
}
