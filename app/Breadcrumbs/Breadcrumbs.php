<?php
/**
 *  app/Breadcrumbs/Breadcrumbs.php
 *
 * Date-Time: 07.06.21
 * Time: 15:23
 * @author Insite LLC <hello@insite.international>
 */
namespace App\Breadcrumbs;


use Illuminate\Http\Request;

/**
 * Class Breadcrumbs
 * @package App\Breadcrumbs
 */
class Breadcrumbs
{

    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Breadcrumbs constructor.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return array
     */
    public function segments(): array
    {
        return collect($this->request->segments())->map(function ($segment) {
            return new Segment($this->request, $segment);
        })->toArray();
    }
}
