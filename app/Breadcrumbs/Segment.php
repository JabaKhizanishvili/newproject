<?php
/**
 *  app/Breadcrumbs/Segment.php
 *
 * Date-Time: 07.06.21
 * Time: 15:23
 * @author Insite LLC <hello@insite.international>
 */

namespace App\Breadcrumbs;

use Illuminate\Http\Request;

/**
 * Class Segment
 * @package App\Breadcrumbs
 */
class Segment
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;
    /**
     * @var string $segment
     */
    protected $segment;

    /**
     * Segment constructor.
     *
     * @param \Illuminate\Http\Request $request
     * @param $segment
     */
    public function __construct(Request $request, $segment)
    {
        $this->request = $request;
        $this->segment = $segment;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->segment;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function model(): \Illuminate\Support\Collection
    {
        // Todo get route parameter model
        return collect($this->request->route()->parameters())->where('id', $this->segment)->first();
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function url()
    {
        return url(implode('/', array_slice($this->request->segments(), 0, $this->position() + 1)));
    }

    /**
     * @return \Illuminate\Routing\Route|object|string|null
     */
    public function method()
    {
        return $this->request->route();
    }

    /**
     * @return false|int|string
     */
    public function position()
    {
        return array_search($this->segment, $this->request->segments(), true);
    }

}