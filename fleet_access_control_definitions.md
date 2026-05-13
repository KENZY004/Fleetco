# Fleetco Access Control & Feature Definitions

This document outlines the feature set for user roles in the Fleetco Predictive Mobility Intelligence platform. It defines what each user can access and the critical restrictions required to maintain data integrity and security.

## 1. User Roles Overview

| Role | Primary Interface | Scope |
| :--- | :--- | :--- |
| **Driver** | Mobile "Co-Pilot" Dashboard | Personal performance, real-time safety, and duty tracking. |
| **Admin** | Operations Hub (Desktop) | Fleet oversight, risk management, vehicle assignments, and system settings. |

---

## 2. Feature Access Matrix

### A. Driver (Operational Level)
Drivers focus on the road and self-regulation. Their access is limited to their current active session and historical personal data.

| Feature | Access Level | Description |
| :--- | :--- | :--- |
| **Duty Toggle** | Full | Start/Stop tracking (ON DUTY / OFF DUTY). |
| **Safety HUD** | View | Real-time radial gauge showing current risk score. |
| **Incident Feed** | View | Self-monitoring feed explaining score drops (e.g., speeding). |
| **Speedometer** | Real-time | Live GPS-synced speed with overspeed alerts. |
| **Maintenance** | Report | Submit mechanical issues directly from the vehicle card. |
| **Profile** | Edit | Manage personal info and notification preferences. |

### B. Admin (Tactical & Strategic Level)
Admins manage both the fleet operations and the platform's global logic.

| Feature | Access Level | Description |
| :--- | :--- | :--- |
| **Operations Map** | View/Manage | Real-time location of all vehicles in the fleet. |
| **Driver Management** | Full | Onboard drivers, view leaderboards, and reset scores if justified. |
| **Vehicle Fleet** | Manage | Edit vehicle specs, assign drivers, and monitor status. |
| **Geofence Engine** | Create/Edit | Define virtual boundaries and manage proximity alerts. |
| **Alert Resolution** | Manage | Acknowledge and resolve security or safety anomalies. |
| **Trip Analytics** | View | Detailed playback and telemetry history of fleet trips. |
| **Global Settings** | Manage | Set global speed limits, scoring weights, and system logic. |

---

## 3. Restricted Features (Security & Integrity)

To prevent "State Shortcutting" and "Identity Spoofing," the following restrictions are enforced:

### 🚫 Restricted for ALL Users (System Level)
- **Manual Anomaly Creation**: Anomalies must be system-generated via the Heuristic Risk Engine. Users cannot "inject" alerts manually.
- **Timestamp Manipulation**: Telemetry data with future timestamps or unrealistic historical backdating is rejected at the API layer.
- **PII Blanket Reads**: No user can perform bulk exports of private data without specific system-level audit logging.

### 🚫 Restricted for Drivers
- **Cross-Vehicle Tracking**: Drivers cannot see the location of other vehicles or drivers.
- **Alert Deletion**: Drivers can view their incidents but cannot delete or resolve them (only Admins can acknowledge).
- **Role Escalation**: Drivers cannot change their role to 'Admin' via profile updates.
- **Vehicle Token Access**: Drivers use their own session; they do not have access to the raw Vehicle Auth Tokens used for hardware integration.

---

## 4. Rationale for Restrictions

1.  **Data Invariants**: Ensuring that a vehicle's state transition (e.g., from `Warning` to `Active`) is governed by the system prevents drivers from "hiding" unsafe behavior.
2.  **Accountability**: By restricting alert resolution to Admins, the platform maintains a perfect audit trail for insurance and compliance.
3.  **Security**: Strict role-based access control prevents lateral movement and ensures data privacy.
