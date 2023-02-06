<?php
/**
 *  app/Observers/LanguageObserver.php
 *
 * Date-Time: 29.07.21
 * Time: 16:57
 * @author Insite LLC <hello@insite.international>
 */
namespace App\Observers;

use App\Models\Language;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;


class LanguageObserver
{
    /**
     * Handle the Language "created" event.
     *
     * @param  \App\Models\Language  $language
     * @return void
     */
    public function created(Language $language): void
    {
        $this->translatableConfigUpdate();
    }

    /**
     * Handle the Language "updated" event.
     *
     * @param  \App\Models\Language  $language
     * @return void
     */
    public function updated(Language $language): void
    {
        $this->translatableConfigUpdate();
    }

    /**
     * Handle the Language "deleted" event.
     *
     * @param  \App\Models\Language  $language
     * @return void
     */
    public function deleted(Language $language): void
    {
        $this->translatableConfigUpdate();
    }

    /**
     * Handle the Language "restored" event.
     *
     * @param  \App\Models\Language  $language
     * @return void
     */
    public function restored(Language $language): void
    {
        $this->translatableConfigUpdate();
    }

    /**
     * Handle the Language "force deleted" event.
     *
     * @param  \App\Models\Language  $language
     * @return void
     */
    public function forceDeleted(Language $language): void
    {
        $this->translatableConfigUpdate();
    }


    /**
     *  Update translatable config...
     */
    private function translatableConfigUpdate(): void
    {
        $languages = Language::where('status' ,true)->pluck('locale', 'title')->toArray();

        $array = Config::get('translatable');
        $array['locales'] = $languages;
        $data = var_export($array, 1);
        try {
            File::put(base_path() . '/config/translatable.php', "<?php\n return $data ;");
        } catch (Exception $exception) {
            dd($exception);
        }

        Artisan::call('config:clear');
    }
}
