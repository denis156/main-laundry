<?php

declare(strict_types=1);

namespace App\Helper\Database;

use App\Models\CourierCarSchedule;

/**
 * CourierCarSchedule Helper
 *
 * Helper untuk menangani data JSONB di tabel courier_cars_schedules.
 * 
 * JSONB Structure:
 * - departure_time: time
 * - location_ids: [int] (array ID locations yang dikunjungi)
 * - route: [{location_id, arrival_time, departure_time, status}]
 * - driver_info: {name, phone, vehicle_number}
 * - notes: string
 */
class CourierCarScheduleHelper
{
    public static function getDepartureTime(CourierCarSchedule $schedule): ?string
    {
        return $schedule->data['departure_time'] ?? null;
    }

    public static function getLocationIds(CourierCarSchedule $schedule): array
    {
        return $schedule->data['location_ids'] ?? [];
    }

    public static function getRoute(CourierCarSchedule $schedule): array
    {
        return $schedule->data['route'] ?? [];
    }

    public static function getDriverInfo(CourierCarSchedule $schedule): array
    {
        return $schedule->data['driver_info'] ?? [];
    }

    public static function getNotes(CourierCarSchedule $schedule): ?string
    {
        return $schedule->data['notes'] ?? null;
    }

    public static function setLocationIds(CourierCarSchedule $schedule, array $locationIds): void
    {
        $data = $schedule->data ?? [];
        $data['location_ids'] = $locationIds;
        $schedule->data = $data;
    }

    public static function isPickup(CourierCarSchedule $schedule): bool
    {
        return $schedule->trip_type === 'pickup';
    }

    public static function isDelivery(CourierCarSchedule $schedule): bool
    {
        return $schedule->trip_type === 'delivery';
    }
}
