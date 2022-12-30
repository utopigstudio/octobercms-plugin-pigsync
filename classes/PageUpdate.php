<?php
namespace Utopigs\Pigsync\Classes;

use RainLab\Pages\Classes\Page;

class PageUpdate implements ChangeOperation
{
    private $page;
    private $translator;

    public function __construct($page)
    {
        $this->page = $page;

        if (class_exists('\RainLab\Translate\Classes\Translator', true)) {
            $this->translator = \RainLab\Translate\Classes\Translator::instance();
        }
    }

    private function getFileContents($filepath)
    {
        if (!file_exists($filepath)) {
            throw new \Exception('Unable to process file change, please contact your developer (file does not exists: ' . $filepath . ')');
        } else if (($contents = file_get_contents($filepath)) === false) {
            throw new \Exception('Unable to process file change, please contact your developer (cannot get file contents: ' . $filepath . ')');
        }

        return $contents;
    }

    public function processChanges(SourceControl $source_control)
    {
        $page_path = $this->page->getFilePath();
        $source_control->add($page_path, $this->getFileContents($page_path));

        if ($this->translator !== null) {
            $default_locale = $this->translator->getDefaultLocale();
            $locales = array_keys(\RainLab\Translate\Models\Locale::listEnabled());

            foreach ($locales as $locale) {
                if ($locale === $default_locale) {
                    continue;
                }

                $translated_page = \RainLab\Translate\Classes\MLStaticPage::findLocale($locale, $this->page);

                // Not all translations exist, skip them if they don't
                if (!$translated_page || !file_exists(($translated_page_path = $translated_page->getFilePath()))) {
                    continue;
                }

                $source_control->add($translated_page_path, $this->getFileContents($translated_page_path));
            }
        }

        // This is safe to use, (@see: RainLab\Pages\Classes\PageList)
        $meta_path = $this->page->getThemeAttribute()->getPath() . '/meta/static-pages.yaml';

        $source_control->add($meta_path, $this->getFileContents($meta_path));
    }
}
