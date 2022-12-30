<?php
namespace Utopigs\Pigsync\Models;

use Model;

/**
 * Model
 */
class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    // A unique code
    public $settingsCode = 'utopigs_pigsync_settings';

    // Reference to field configuration
    public $settingsFields = 'fields.yaml';
}
