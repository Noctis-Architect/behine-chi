<?php
/**
 * Font Manager Utility
 *
 * @package WSO\Tools
 */

namespace WSO\Tools;

if (!defined('ABSPATH')) {
    exit;
}

class Font_Manager {

    /**
     * Singleton instance.
     *
     * @var Font_Manager|null
     */
    private static ?Font_Manager $instance = null;

    /**
     * Returns the singleton instance.
     *
     * @return Font_Manager
     */
    public static function instance(): Font_Manager {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Private constructor.
     */
    private function __construct() {}

    /**
     * Scans assets/fonts directory and parses font structures.
     *
     * @return array Array of fonts grouped by family.
     */
    public function get_available_fonts(): array {
        $fonts_url = WSO_URL . 'assets/fonts/';
        return [
            'Vazir' => [
                [
                    'url'    => $fonts_url . 'Vazir-Regular.ttf',
                    'weight' => 400,
                    'style'  => 'normal',
                    'format' => 'truetype',
                ]
            ],
            'Iran Sans' => [
                [
                    'url'    => $fonts_url . 'IRANSansX-Regular.otf',
                    'weight' => 400,
                    'style'  => 'normal',
                    'format' => 'opentype',
                ],
                [
                    'url'    => $fonts_url . 'IRANSansX-Bold.otf',
                    'weight' => 700,
                    'style'  => 'normal',
                    'format' => 'opentype',
                ]
            ],
            'Iran Yekan' => [
                [
                    'url'    => $fonts_url . 'IRANYekanX-Regular.ttf',
                    'weight' => 400,
                    'style'  => 'normal',
                    'format' => 'truetype',
                ],
                [
                    'url'    => $fonts_url . 'IRANYekanX-Bold.ttf',
                    'weight' => 700,
                    'style'  => 'normal',
                    'format' => 'truetype',
                ]
            ]
        ];
    }

    /**
     * Injects CSS @font-face rules into admin header (Bypassed since declared in admin.css).
     *
     * @return void
     */
    public function generate_font_face_css(): void {
        // Declared via relative URLs in assets/css/admin.css for performance and commercial standards
    }
}
