<?php
namespace Utopigs\Pigsync;

use Event;
use System\Classes\PluginBase;
use Utopigs\Pigsync\Models\Settings;
use Utopigs\Pigsync\Classes\PageUpdate;
use Utopigs\Pigsync\Classes\MenuUpdate;
use Utopigs\Pigsync\Classes\SourceControl;

class Plugin extends PluginBase
{
    /** @var array Plugin dependencies */
    public $require = ['Rainlab.Pages'];

    public function boot()
    {
        Event::listen('pages.object.save', function($controller, $object, $type) {
            if (!Settings::get('sync_enabled')) {
                return;
            }

            $source_control = new SourceControl(
                Settings::get('team'),
                Settings::get('repository'),
                Settings::get('branch'),
                Settings::get('token'),
            );

            $operation = null;

            switch ($type) {
                case 'page':
                    $operation = new PageUpdate($object);
                    break;
                case 'menu':
                    $operation = new MenuUpdate($object);
                    break;
            }

            if ($operation === null) {
                return;
            }

            $operation->processChanges($source_control);

            $message = strlen(Settings::get('message')) > 0 ? Settings::get('message') : 'Changed %f by %u';
            $message = str_replace('%u', $source_control->author(SourceControl::AUTHOR_EMAIL), $message);
            $message = str_replace('%f', $type, $message);

            $source_control->commit($message);
            
            $source_control->push();
        });
    }

    public function registerComponents()
    {
        //
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions() {
        return [
            'utopigs.pigsync.settings' => [ 'tab' => 'utopigs.pigsync::lang.plugin.name', 'label' => 'utopigs.pigsync::lang.permissions.settings' ],
        ];
    }

    public function registerSettings()
    {
        return [
            'config' => [
                'label' => 'utopigs.pigsync::lang.plugin.name',
                'category' => 'system::lang.system.categories.cms',
                'icon' => 'oc-icon-refresh',
                'description' => 'utopigs.pigsync::lang.plugin.description',
                'class' => 'Utopigs\Pigsync\Models\Settings',
                'order' => 800,
                'permissions' => ['utopigs.pigsync.settings']
            ]
        ];
    }
}
