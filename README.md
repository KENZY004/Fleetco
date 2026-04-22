# Fleetco | Operations Command Hub

![FleetCo Banner](/fleet_hub.png)

## 🌌 Overview
Fleetco is an enterprise-grade **Predictive Mobility Intelligence** platform. It transforms raw telematics into a high-fidelity "Operations Command Hub," providing real-time oversight for complex vehicle fleets.

## 🛠️ Tech Stack
- **Engine:** Laravel 11 (Monolith)
- **Intelligence:** PostGIS + Heuristic Risk Engine
- **Interface:** Blade Components + Tailwind CSS + GSAP
- **Mapping:** Leaflet.js (Dark Mode)

## 📜 Master Directive
This project is governed by a strict architectural directive focused on high-velocity delivery, behavioral analytics, and "Zero-Hardware" PWA capabilities.

**[Read the Full Master Directive](MASTER_DIRECTIVE.md)**

## 🚀 Key Features
- **Neural Spatial Matrix:** Real-time PostGIS-backed tracking.
- **Heuristic Risk Scoring:** AI-driven driver behavior analysis.
- **Mission Visualization:** Historical path playback and spatial analysis.
- **Command Hub UI:** Cinematic, map-centric "Bento" dashboard.

---

## 💻 Local Development

**Prerequisites:** PHP 8.2+, Composer, PostgreSQL + PostGIS.

1. **Clone & Install:**
   ```bash
   composer install
   npm install
   ```

2. **Environment Setup:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database Migration:**
   ```bash
   php artisan migrate --seed
   ```

4. **Launch:**
   ```bash
   php artisan serve
   npm run dev
   ```

---

*Built with precision for Fleet Managers and System Architects.*
