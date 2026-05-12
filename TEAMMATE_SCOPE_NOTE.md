# Teammate Scope Note — Fleet Multi-Tenancy

> **Generated automatically as part of the multi-tenant migration.**  
> Last updated: 2026-05-10

## Overview

The following controllers contain queries that **require manual `fleet_id` scoping** to maintain tenant isolation. All queries in the fleet manager namespace must include `.where('fleet_id', auth()->user()->fleet_id)`.

---

## ✅ Already Scoped (via Repository layer)

| Controller / Repository | Scope Applied |
|---|---|
| `DriverRepository::all()` | ✅ `->when($fleetId, fn($q) => $q->where('fleet_id', $fleetId))` |
| `DriverRepository::getUnlinkedUsers()` | ✅ Fleet-scoped |
| `DriverRepository::create()` | ✅ Stamps `fleet_id` on create |
| `VehicleRepository::getAllWithStatus()` | ✅ `->when($fleetId, ...)` |
| `VehicleRepository::create()` | ✅ Stamps `fleet_id` on create |
| `Driver` model `fillable` | ✅ `fleet_id` added |
| `Vehicle` model `fillable` | ✅ `fleet_id` added |

---

## ⚠️ Requires Manual Scoping — Admin Controllers

These controllers query data globally and will need fleet scoping if/when the "fleet manager" role gets their own isolated dashboard views.

| File | Method | Scope Needed |
|---|---|---|
| `app/Http/Controllers/DashboardController.php` | `index()` — queries all vehicles/alerts | `->where('fleet_id', auth()->user()->fleet_id)` |
| `app/Http/Controllers/AlertHistoryController.php` | `index()`, `resolve()`, `destroy()` | Scope by vehicle's fleet |
| `app/Http/Controllers/TripController.php` | `index()`, `show()` | `->where('fleet_id', ...)` via vehicle relation |
| `app/Http/Controllers/GeofenceController.php` | `index()`, `store()`, `destroy()` | Geofences not yet fleet-scoped |
| `app/Http/Controllers/SettingsController.php` | `index()`, `update()` | Global settings are intentionally shared for now |
| `app/Http/Controllers/VehicleMaintenanceController.php` | `index()`, `store()` | Scope by vehicle's `fleet_id` |

---

## Notes

- The `SettingsController` reads from a shared global settings table — this is intentional. Speed limits etc. are platform-wide.
- The `DashboardController` serves the live map view — once fleet managers have their own dashboard, scope all vehicle/alert queries.
- Driver-namespace controllers (`Driver\*`) are scoped by `driver_id` (the auth user's own data only) — no fleet scoping needed there.
