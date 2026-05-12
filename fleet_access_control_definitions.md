# Fleetco Access Control & Feature Definitions

This document outlines the proposed feature set for different user roles in the Fleetco Predictive Mobility Intelligence platform. It defines what each user can access and the critical restrictions required to maintain data integrity and security.

## 1. User Roles Overview

| Role | Primary Interface | Scope |
| :--- | :--- | :--- |
| **Driver** | Mobile "Co-Pilot" Dashboard | Personal performance, real-time safety, and duty tracking. |
| **Fleet Manager** | Operations Hub (Desktop) | Fleet oversight, risk management, and vehicle assignments. |
| **Super Admin** | System Admin Panel | Global settings, security policies, and cross-fleet orchestration. |

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

### B. Fleet Manager (Tactical Level)
Fleet Managers need a "birds-eye view" of operations to ensure efficiency and safety compliance.

| Feature | Access Level | Description |
| :--- | :--- | :--- |
| **Operations Map** | View/Manage | Real-time location of all vehicles in the assigned fleet. |
| **Driver Management** | Full | Onboard drivers, view leaderboards, and reset scores if justified. |
| **Vehicle Fleet** | Manage | Edit vehicle specs, assign drivers, and monitor status. |
| **Geofence Engine** | Create/Edit | Define virtual boundaries and manage proximity alerts. |
| **Alert Resolution** | Manage | Acknowledge and resolve security or safety anomalies. |
| **Trip Analytics** | View | Detailed playback and telemetry history of fleet trips. |

### C. Super Admin (Strategic Level)
Super Admins manage the platform's infrastructure and global logic.

| Feature | Access Level | Description |
| :--- | :--- | :--- |
| **Global Settings** | Manage | Set global speed limits, scoring weights, and API rates. |
| **RBAC Controls** | Full | Define roles, permissions, and cross-fleet access rules. |
| **System Audits** | View | Access logs, system performance metrics, and audit trails. |
| **Data Purge** | Restricted | Ability to clear historical alerts or archived data globally. |

---

## 3. Restricted Features (Security & Integrity)

To prevent "State Shortcutting" and "Identity Spoofing," the following restrictions are enforced:

### 🚫 Restricted for ALL Users (System Level)
- **Manual Anomaly Creation**: Anomalies must be system-generated via the Heuristic Risk Engine. Users cannot "inject" alerts manually.
- **Timestamp Manipulation**: Telemetry data with future timestamps or unrealistic historical backdating is rejected at the API layer.
- **PII Blanket Reads**: No user (including Managers) can perform bulk exports of private data without specific system-level audit logging.

### 🚫 Restricted for Drivers
- **Cross-Vehicle Tracking**: Drivers cannot see the location of other vehicles or drivers.
- **Alert Deletion**: Drivers can view their incidents but cannot delete or resolve them (only Managers/Admins can acknowledge).
- **Role Escalation**: Drivers cannot change their role to 'Admin' or 'Manager' via profile updates.
- **Vehicle Token Access**: Drivers use their own session; they do not have access to the raw Vehicle Auth Tokens used for hardware integration.

### 🚫 Restricted for Fleet Managers
- **Scoring Algorithm Modification**: Managers can view scores but cannot change the underlying math (e.g., how much weight "idling" has on the total score).
- **Global Config**: Managers are restricted to their assigned fleet's context and cannot change system-wide environment variables.
- **Database Deletions**: Managers can "resolve" alerts (marking them as handled) but cannot permanently delete the record from the database.

---

## 4. Rationale for Restrictions

1.  **Data Invariants**: Ensuring that a vehicle's state transition (e.g., from `Warning` to `Active`) is governed by the system prevents drivers from "hiding" unsafe behavior.
2.  **Accountability**: By restricting alert deletion to Admins, the platform maintains a perfect audit trail for insurance and compliance.
3.  **Security**: Strict RBAC prevents lateral movement between fleets, ensuring data privacy in a multi-tenant environment.
