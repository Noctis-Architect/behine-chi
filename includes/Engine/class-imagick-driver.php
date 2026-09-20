<?php
/**
 * Imagick Image Processing Driver
 *
 * @package WSO\Engine
 */

namespace WSO\Engine;

if (!defined('ABSPATH')) {
    exit;
}

class Imagick_Driver implements Image_Driver {

    /**
     * Checks if Imagick extension is loaded.
     *
     * @return bool
     */
    public function is_available(): bool {
        return extension_loaded('imagick') && class_exists('\Imagick');
    }

    /**
     * Checks if Imagick supports WebP.
     *
     * @return bool
     */
    public function supports_webp(): bool {
        if (!$this->is_available()) {
            return false;
        }
        $formats = \Imagick::queryFormats('WEBP');
        return !empty($formats);
    }

    /**
     * Checks if Imagick supports AVIF.
     *
     * @return bool
     */
    public function supports_avif(): bool {
        if (!$this->is_available()) {
            return false;
        }
        $formats = \Imagick::queryFormats('AVIF');
        return !empty($formats);
    }

    /**
     * Converts image using Imagick.
     *
     * @param string $source_file
     * @param string $target_file
     * @param string $mime_type
     * @param int $quality
     * @return bool
     */
    public function convert(string $source_file, string $target_file, string $mime_type, int $quality = 82): bool {
        if (!file_exists($source_file) || !$this->is_available()) {
            return false;
        }

        try {
            $imagick = new \Imagick($source_file);

            $format = match ($mime_type) {
                'image/webp' => 'WEBP',
                'image/avif' => 'AVIF',
                default      => null,
            };

            if (!$format) {
                return false;
            }

            $imagick->setImageFormat($format);
            $imagick->setImageCompressionQuality($quality);

            if ($format === 'WEBP') {
                $imagick->setOption('webp:method', '6');
                $imagick->setOption('webp:lossless', 'false');
            }

            $written = $imagick->writeImage($target_file);
            $imagick->clear();
            $imagick->destroy();

            return $written && file_exists($target_file) && filesize($target_file) > 0;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Resizes image down using Imagick.
     *
     * @param string $file
     * @param int $max_width
     * @param int $max_height
     * @param int $quality
     * @return bool
     */
    public function resize(string $file, int $max_width, int $max_height, int $quality = 82): bool {
        if (!file_exists($file) || !$this->is_available()) {
            return false;
        }

        try {
            $imagick = new \Imagick($file);
            $geo = $imagick->getImageGeometry();
            $orig_w = $geo['width'] ?? 0;
            $orig_h = $geo['height'] ?? 0;

            if ($orig_w <= $max_width && $orig_h <= $max_height) {
                return false;
            }

            $imagick->resizeImage($max_width, $max_height, \Imagick::FILTER_LANCZOS, 1, true);
            $imagick->setImageCompressionQuality($quality);
            $res = $imagick->writeImage($file);

            $imagick->clear();
            $imagick->destroy();

            return (bool) $res;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Strips EXIF metadata using Imagick.
     *
     * @param string $file
     * @return bool
     */
    public function strip_exif(string $file): bool {
        if (!file_exists($file) || !$this->is_available()) {
            return false;
        }

        try {
            $imagick = new \Imagick($file);
            $imagick->stripImage();
            $res = $imagick->writeImage($file);

            $imagick->clear();
            $imagick->destroy();

            return (bool) $res;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
