<?php

/**
 *  app/Console/Commands/ScanTranslations.php
 *
 * Date-Time: 07.06.21
 * Time: 13:02
 * @author Insite LLC <hello@insite.international>
 */

namespace App\Console\Commands;


use App\Models\Language;
use Illuminate\Console\Command;
use Spatie\TranslationLoader\LanguageLine;

/**
 * Class ScanTranslations
 * @package App\Console\Commands
 */
class ScanTranslationsJs extends Command
{
    /**
     *  The name and signature of the console command
     *
     * @var string
     */
    protected $signature = 'scan:translationsjs';

    /**
     * The console command description
     *
     * @var string
     */
    protected $description = 'Scan language translations.';

    /**
     *  functions names
     *
     * @var string[]
     */
    protected $functions = ['lang', 'trans', '__'];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $path = base_path() . '/resources/js';

        $this->info('Start read files recursive.');
        $files = iterator_to_array($this->filesIn($path));
        $this->info('End read files.');
        $results = ['single' => [], 'group' => []];

        $matchingPattern =
            '[^\w]' .
            '(?<!->)' .
            '(' . implode('|', $this->functions) . ')' .
            "\(" .
            "[\'\"]" .
            '(' .
            '.+' .
            ')' .
            "[\'\"]" .
            "[\),]";
        $this->info('Scan files start.');
        foreach ($files as $file) {
            $fileContents = file_get_contents($file);

            if (preg_match_all("/$matchingPattern/siU", $fileContents, $matches)) {
                $this->info($file->getFilename() . ' - File matched.');
                foreach ($matches[2] as $key) {
                    if (preg_match("/(^[a-zA-Z0-9:_-]+([.][^\1)\ ]+)+$)/siU", $key, $arrayMatches)) {
                        [$file, $k] = explode('.', $arrayMatches[0], 2);
                        $results['group'][$file][$k] = '';
                        continue;
                    } else {
                        $results['single']['single'][$key] = '';
                    }
                }
            }
        }
        $this->info('Scan files end.');

        $this->info('Import keys into LanguageLines.');
        foreach ($results['group'] as $group => $result) {
            $defaultLanguage = Language::where('default', true)->first();
            if (null === $defaultLanguage) {
                throw new \RuntimeException("Default language not exists.");
                break;
            }

            $keys = array_keys($result);
            foreach ($keys as $key) {
                $text = [];
                $languageLine = LanguageLine::where('group', $group)
                    ->where('key', $key)->first();
                if (null !== $languageLine) {
                    continue;
                }

                $text[$defaultLanguage->locale] = $key;
                $this->info('Insert into language lines -  ' . $key);
                LanguageLine::create([
                    'group' => $group,
                    'key' => $key,
                    'text' => $text
                ]);
            }
        }
        $this->info('LanguageLines updated..');
    }

    /**
     * @param string $path
     *
     * @return \Generator
     */
    protected function filesIn(string $path): \Generator
    {
        if (!is_dir($path)) {
            throw new \RuntimeException("{$path} is not a directory ");
        }

        $it = new \RecursiveDirectoryIterator($path);
        $it = new \RecursiveIteratorIterator($it);
        yield from new \RegexIterator($it, '/\.php$|.jsx$/', \RegexIterator::MATCH);
    }
}
