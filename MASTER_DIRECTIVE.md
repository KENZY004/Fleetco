# 🛰️ Fleetco: Predictive Mobility Intelligence

## The Master Directive

**Role:** Lead System Architect & Senior Full-Stack Developer  
**Objective:** Architect a "Predictive Mobility Intelligence" platform. Deliver an enterprise-grade MVP within a high-pressure deadline using a high-velocity technical stack.

---

### 1. Executive Vision
We are not just "tracking vehicles." We are building a **Behavioral Analytics Engine**. The goal is to transform raw coordinates into safety and efficiency insights. We use a **Monolithic Architecture** to eliminate API overhead and maximize delivery speed without sacrificing technical depth.

### 2. High-Level Technical Architecture (The "Speed & Power" Stack)
- **Backend & Frontend:** Laravel 11 (Monolith) + Blade + Tailwind CSS. (Priority: Zero-latency development and seamless state management).
- **Data Layer:** PostgreSQL + PostGIS. Use native spatial indexing to handle geofencing and proximity logic.
- **Auth & Security:** Laravel Sanctum with Middleware-level RBAC. Security is baked into the kernel.
- **Real-Time Strategy:** High-frequency polling (Phase 1) with an architectural bridge for Laravel Reverb (WebSockets).

### 3. The "Intelligence" Edge
- **Heuristic Risk Engine:** Backend service calculating a **Driver Risk Score** based on speed delta, idle duration, and geofence deviations.
- **Zero-Hardware PWA:** Browser-based background sync treating the device as a telematics sensor.
- **Spatial Intelligence:** PostGIS `ST_DWithin` for ultra-fast "Vehicle vs. Landmark" detection.

### 4. UI/UX Philosophy
- **The "Bento" Dashboard:** Componentized Blade UI with high information density.
- **Map-Centric Design:** Leaflet.js "Hero" map with floating, modular data overlays.
- **Aesthetic:** High-contrast, cinematic dark mode (Obsidian/Emerald/Orange).

### 5. Developer Operational Directive
- **Shippable over Complex:** Rapid delivery in Blade with SPA-like feel.
- **Professional Documentation:** Code comments explaining DSA choices (e.g., Haversine, Spatial Indexing).
- **Strict Security:** CSRF, validation, and RBAC in every snippet.
- **Scalable Foundations:** Built for future React/API migration ("IntelFleet-ready").
