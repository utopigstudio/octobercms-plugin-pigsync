<?php
namespace Utopigs\Pigsync\Classes;

use RainLab\Pages\Classes\Menu;

class MenuUpdate implements ChangeOperation
{
    private $menu;

    public function __construct(Menu $menu)
    {
        $this->menu = $menu;
    }

    private function getFileContents($filepath)
    {
        if (!file_exists($filepath)) {
            throw new \Exception('Unable to process menu change, please contact your developer (file does not exists: ' . $filepath . ')');
        } else if (($contents = file_get_contents($filepath)) === false) {
            throw new \Exception('Unable to process menu change, please contact your developer (cannot get file contents: ' . $filepath . ')');
        }

        return $contents;
    }

    public function processChanges(SourceControl $source_control)
    {
        $menu_path = $this->menu->getFilePath();
        $source_control->add($menu_path, $this->getFileContents($menu_path));
    }
}
