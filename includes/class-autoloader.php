<?php
/**
 * PSR-4 Autoloader for WebP Smart Optimizer
 *
 * @package WSO
 */

namespace WSO;

if (!defined('ABSPATH')) {
    exit;
}

class Autoloader {

    /**
     * Cache for resolved class paths.
     *
     * @var array<string, string|bool>
     */
    private static array $class_map = [];

    /**
     * Registers the autoloader.
     *
     * @return void
     */
    public static function register(): void {
        spl_autoload_register([__CLASS__, 'autoload']);
    }

    /**
     * Autoloads WSO classes, interfaces, and traits.
     *
     * @param string $class Fully qualified class name.
     * @return void
     */
    public static function autoload(string $class): void {
        $prefix = 'WSO\\';
        
        if (isset(self::$class_map[$class])) {
            if (is_string(self::$class_map[$class])) {
                require_once self::$class_map[$class];
            }
            return;
        }

        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }

        $base_dir = defined('WSO_PATH') ? WSO_PATH . 'includes/' : plugin_dir_path(__FILE__);
        $relative_class = substr($class, $len);
        $parts = explode('\\', $relative_class);
        
        $class_name = array_pop($parts);
        $sub_path = !empty($parts) ? implode('/', $parts) . '/' : '';

        $slug_name = strtolower(str_replace('_', '-', $class_name));

        // Possible file name conventions
        $candidates = [
            $base_dir . $sub_path . 'interface-' . $slug_name . '.php',
            $base_dir . $sub_path . 'class-' . $slug_name . '.php',
            $base_dir . $sub_path . $class_name . '.php',
        ];

        foreach ($candidates as $file) {
            if (file_exists($file)) {
                self::$class_map[$class] = $file;
                require_once $file;
                return;
            }
        }

        self::$class_map[$class] = false;
    }
}
