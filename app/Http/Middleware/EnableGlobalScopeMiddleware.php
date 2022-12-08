<?php
/**
 *  app/Http/Middleware/EnableGlobalScopeMiddleware.php
 *
 * Date-Time: 11.08.21
 * Time: 11:03
 * @author Insite LLC <hello@insite.international>
 */
namespace App\Http\Middleware;

use App\Models\Product;
use App\Models\Slider;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class EnableGlobalScopeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        Product::addGlobalScope('active', function (Builder $builder) {
            $builder->where('status', true);
        });


        Slider::addGlobalScope('active', function (Builder $builder) {
            $builder->where('status', true);
        });

        return $next($request);
    }
}
