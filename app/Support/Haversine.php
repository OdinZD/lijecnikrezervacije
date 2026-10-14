<?php

namespace App\Support;

/**
 * Great-circle distance helpers for the radius search. We inline the origin
 * coordinates as numeric literals (they come from our own cities table, cast to
 * float) rather than bindings — this keeps the SQL readable and sidesteps the
 * fragile positional-binding order when the same expression appears in both
 * SELECT and WHERE. The latDelta/lngDelta give a cheap bounding box so the
 * B-tree (lat,lng) index prefilters before the exact acos() runs.
 */
class Haversine
{
    public const EARTH_KM = 6371.0;

    /**
     * SQL expression (Postgres) computing kilometres from the given origin to
     * the referenced lat/lng columns. No bindings — origin is inlined safely.
     */
    public static function expression(float $originLat, float $originLng, string $latCol, string $lngCol): string
    {
        $lat = sprintf('%.7f', $originLat);
        $lng = sprintf('%.7f', $originLng);
        $r = sprintf('%.1f', self::EARTH_KM);

        return "{$r} * acos(least(1, "
            ."cos(radians({$lat})) * cos(radians({$latCol})) * cos(radians({$lngCol}) - radians({$lng})) "
            ."+ sin(radians({$lat})) * sin(radians({$latCol}))))";
    }

    /** Degrees of latitude spanning the given km (constant ~111 km per degree). */
    public static function latDelta(float $km): float
    {
        return $km / 111.0;
    }

    /** Degrees of longitude spanning the given km at the given latitude. */
    public static function lngDelta(float $km, float $atLat): float
    {
        return $km / (111.0 * max(0.01, cos(deg2rad($atLat))));
    }
}
