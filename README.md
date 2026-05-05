# 🛰️ Fleetco: Predictive Mobility Intelligence

Fleetco is an enterprise-grade fleet management platform that transforms raw GPS telemetry into behavioral intelligence. Using a "Zero-Hardware" approach, it treats smartphones as advanced telematics sensors.

## 🚀 Deployment Quickstart (Fly.io)

1. **Install CLI**: `iwr https://fly.io/install.ps1 -useb | iex`
2. **Init App**: `fly launch` (Enable Postgres + Redis)
3. **Migrate**: `fly ssh console -C "php artisan migrate --force"`
4. **Test**: Open your URL, go to `/track-me`, and use token `FLT-FADD69D3`.

## 🧠 Core Features
- **Heuristic Risk Engine**: Real-time driver scoring based on behavioral patterns.
- **Spatial Intelligence**: PostGIS-powered geofencing and proximity detection.
- **Offline Resiliency**: PWA support with IndexedDB queuing and Background Sync.
- **Bento Dashboard**: High-density operational overview (Laravel 11 + Blade).

## 🛠️ Tech Stack
- **Backend**: Laravel 11 (Monolith)
- **Database**: PostgreSQL + PostGIS
- **Frontend**: Blade + Tailwind CSS + Leaflet.js
- **Real-Time**: Laravel Reverb

---
*Built for high-performance fleet operations.*
