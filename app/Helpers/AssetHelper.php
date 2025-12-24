<?php

namespace App\Helpers;

class AssetHelper
{
    private static $manifest = null;

    public static function getManifest()
    {
        if (self::$manifest === null) {
            $manifestPath = public_path('build/manifest.json');
            if (file_exists($manifestPath)) {
                self::$manifest = json_decode(file_get_contents($manifestPath), true);
            } else {
                self::$manifest = [];
            }
        }
        return self::$manifest;
    }

    public static function asset($path)
    {
        $manifest = self::getManifest();

        if (isset($manifest[$path])) {
            return url('build/' . $manifest[$path]['file']);
        }

        return url($path);
    }
}
