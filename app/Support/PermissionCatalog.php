<?php

namespace App\Support;

use App\Classes\permission as PermissionMap;

class PermissionCatalog
{
    /**
     * Return all permissions as a flat array with id/key/labels.
     */
    public static function all(): array
    {
        $items = [];

        foreach (PermissionMap::$perms as $id => $key) {
            $items[] = [
                'id'        => (int) $id,
                'key'       => $key,
                'label'     => self::label($key),
                'group'     => self::group($id, $key),
                'is_parent' => self::isParent($id),
            ];
        }

        return $items;
    }

    /**
     * Group permissions by their logical category.
     */
    public static function grouped(): array
    {
        $groups = [];

        foreach (self::all() as $item) {
            $groups[$item['group']][] = $item;
        }

        ksort($groups, SORT_NATURAL);

        return $groups;
    }

    /**
     * Return the list of valid permission IDs.
     */
    public static function ids(): array
    {
        return array_map(static fn ($entry) => $entry['id'], self::all());
    }

    private static function label(string $key): string
    {
        return ucwords(str_replace(['-', '_'], ' ', $key));
    }

    private static function group(int $id, string $key): string
    {
        if (self::isParent($id)) {
            return self::label($key);
        }

        $root = strtok($key, '-');

        return $root ? self::label($root) : self::label($key);
    }

    private static function isParent(int $id): bool
    {
        if ($id < 100) {
            return true;
        }

        // treat 4-digit block starters (xx00) and special codes as parents
        if ($id % 100 === 0) {
            return true;
        }

        return in_array($id, [7000, 7010], true);
    }
}
