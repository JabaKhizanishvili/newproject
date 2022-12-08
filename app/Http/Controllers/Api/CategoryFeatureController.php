<?php
/**
 *  app/Http/Controllers/Api/CategoryFeatureController.php
 *
 * Date-Time: 11.06.21
 * Time: 10:38
 * @author Insite LLC <hello@insite.international>
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Repositories\CategoryRepositoryInterface;
use Illuminate\Http\Request;

/**
 * Class CategoryFeatureController
 * @package App\Http\Controllers\Api
 */
class CategoryFeatureController extends Controller
{

    /**
     * @var \App\Repositories\CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * CategoryFeatureController constructor.
     *
     * @param \App\Repositories\CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(CategoryRepositoryInterface $categoryRepository) {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param \App\Models\Category $category
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetch(Category $category) {
        $category = Category::where('id',$category->id)->with(['languages','features.languages','features.answers.languages'])->first();

        return response()->json([
            'category' => $category
        ]);
    }
}
