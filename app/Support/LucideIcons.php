<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class LucideIcons
{
    public static function all(): array
    {
        // Cache the icons for performance so we don't scan the disk on every request
        return Cache::rememberForever('lucide_icons_list', function () {
            $dir = base_path('vendor/mallardduck/blade-lucide-icons/resources/svg');

            if (!is_dir($dir)) {
                return [];
            }

            return collect(scandir($dir))
                ->filter(fn ($file) => str_ends_with($file, '.svg'))
                ->map(function ($file) {
                    $name = str_replace('.svg', '', $file);
                    $kebabName = 'lucide-' . $name;

                    // We try to render the icon to get the SVG HTML string
                    try {
                        $svg = svg($kebabName)->toHtml();
                    } catch (\Exception $e) {
                        return null;
                    }

                    return [
                        'label' => Str::title(str_replace('-', ' ', $name)),
                        'value' => $kebabName, // Store the full component name
                        'svg'   => $svg, // The raw SVG HTML
                        'keywords' => $name, // For searching
                    ];
                })
                ->filter() // Remove nulls
                ->values()
                ->toArray();
        });
    }
}
