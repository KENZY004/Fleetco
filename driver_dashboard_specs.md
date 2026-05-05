# 📱 Project Brief: Fleetco Driver Co-Pilot (Mobile Dashboard)

## 🎯 Objective
Create a high-fidelity, mobile-first "Co-Pilot" dashboard for drivers. This page is the primary interface for operators on the road, focusing on self-regulation, shift tracking, and safety gamification.

## 🎨 UI/UX Design System (Obsidian)
*   **Theme**: Strict Dark Mode.
*   **Background**: `#020202` (Zinc-950).
*   **Accent Color**: `#ff8a00` (Orange-500) for primary actions and safety alerts.
*   **Glassmorphism**: Use `bg-white/5` with `backdrop-blur-md` for cards.
*   **Typography**: Plus Jakarta Sans (Heading), Inter (Body).

---

## 🛠️ Key Components to Build

### 1. Safety Score HUD (The "Heart")
*   **Visual**: A large, centered radial gauge.
*   **Logic**: Pull the `risk_score` from the Driver model. 
*   **Feedback**: 
    *   `80-100`: Emerald/Green (Optimal)
    *   `60-79`: Orange (Caution)
    *   `<60`: Red (At Risk)

### 2. Duty Status Matrix (Logbook)
*   **Controls**: A sliding toggle or large buttons for:
    *   **ON DUTY**: Starts the GPS tracking engine.
    *   **BREAK**: Pauses tracking but stays logged in.
    *   **OFF DUTY**: Ends shift and uploads final trip stats.
*   **Shift Timer**: A digital clock showing `HH:MM:SS` elapsed since the last "ON DUTY" toggle.

### 3. Active Vehicle Card
*   **Info**: Display License Plate, Vehicle Name, and "Connectivity Status" (Green pulse if pings are succeeding).
*   **Feature**: A button to "Report Mechanical Issue" (pops up a small form for maintenance).

### 4. Self-Monitoring Incident Feed
*   **Content**: A scrolling list of the last 5 alerts triggered by this driver.
*   **Purpose**: Allow the driver to see *why* their score dropped (e.g., "Speeding at 14:05 on Main St") so they can adjust their behavior immediately.

### 5. Live Speed Indicator
*   **Visual**: Digital speedometer synced with the phone's GPS.
*   **Logic**: If current speed > `Global Speed Limit` (from settings), the speedometer should turn red and the phone should vibrate.

---

## 📡 API Integration Notes

*   **GET `/api/driver/status`**: Fetch current vehicle assignment and safety score.
*   **POST `/api/telemetry`**: Send `[lat, lng, speed, heading, captured_at]` every 3-5 seconds when "ON DUTY".
*   **POST `/api/maintenance/report`**: Submit a quick mechanical alert to the Maintenance Hub.

## 💡 Pro-Tip for Implementation
Ensure the "Start Tracking" button requests **High Accuracy GPS** and handles **Wake Lock** (prevents the phone screen from turning off while driving).
