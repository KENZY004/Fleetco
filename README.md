# 🚐 Fleetco Telematics Dashboard

**Fleetco** is a professional, high-fidelity telematics and fleet management SaaS platform. Designed for real-time operational command, it provides deep visibility into vehicle tracking, driver safety, and fleet maintenance.

![Dashboard Preview](https://via.placeholder.com/800x400?text=Fleetco+Operations+Command+Hub)

## 🚀 Core Features

*   **Real-Time Tracking**: High-performance Leaflet-based map with smooth marker interpolation and live breadcrumb pathing.
*   **Driver Scorecards**: Comprehensive safety analytics, including risk-event tracking, safety score trends, and mission history.
*   **Trip Replay Engine**: Advanced historical playback with scrub controls, incident jump-navigation, and speed visualization.
*   **Smart Alerts**: Real-time detection of speeding, geofence breaches, and unauthorized movements.
*   **Maintenance Hub**: Automatic odometer tracking and service-due prediction based on live trip data.
*   **Command Center**: Global platform configuration for safety thresholds and company branding.

## 🛠️ Technical Stack

*   **Backend**: Laravel 11 (PHP 8.2+)
*   **Database**: PostgreSQL with **PostGIS** extension for spatial intelligence.
*   **Frontend**: Alpine.js, Tailwind CSS (Zinc-950 Obsidian Theme).
*   **Mapping**: Leaflet.js with custom smooth-sliding interpolation.
*   **Spatial Logic**: Magellan (PostGIS for Laravel).

## 📥 Installation

1.  **Clone the repository**:
    ```bash
    git clone https://github.com/KENZY004/Fleetco.git
    cd fleetco
    ```

2.  **Install dependencies**:
    ```bash
    composer install
    npm install
    ```

3.  **Environment Setup**:
    *   Copy `.env.example` to `.env`.
    *   Configure your PostgreSQL credentials.
    *   Ensure the **PostGIS** extension is enabled in your database.

4.  **Database Migration**:
    ```bash
    php artisan migrate --seed
    ```

5.  **Start Development Server**:
    ```bash
    php artisan serve
    npm run dev
    ```

## 🔐 Security & Access
The platform features Role-Based Access Control (RBAC):
*   **Admin**: Full access to fleet operations, settings, and maintenance.
*   **Driver**: Access to the "Mobile Link" Co-Pilot dashboard for shift tracking and self-monitoring.

---
Built with ❤️ for professional fleet operators by Team MinVa.
