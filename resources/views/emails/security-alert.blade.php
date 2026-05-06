<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fleetco Security Alert</title>
    <style>
        body {
            background-color: #020202;
            color: #ffffff;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #09090b;
            border: 1px solid #18181b;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);
        }
        .header {
            padding: 40px;
            text-align: center;
            background-color: #101014;
            border-bottom: 1px solid #18181b;
        }
        .logo {
            width: 48px;
            height: 48px;
            background-color: #ff8a00;
            border-radius: 12px;
            display: inline-block;
            margin-bottom: 20px;
        }
        .content {
            padding: 40px;
        }
        .alert-badge {
            display: inline-block;
            padding: 4px 12px;
            background-color: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 99px;
            color: #ef4444;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 16px;
        }
        h1 {
            font-size: 24px;
            font-weight: 800;
            margin: 0 0 16px 0;
            letter-spacing: -0.02em;
        }
        p {
            color: #a1a1aa;
            font-size: 14px;
            margin-bottom: 24px;
        }
        .info-grid {
            background-color: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 32px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 12px;
        }
        .info-row:last-child { margin-bottom: 0; }
        .label { color: #52525b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
        .value { color: #ffffff; font-weight: 600; }
        
        .cta {
            display: block;
            background-color: #ffffff;
            color: #000000;
            text-align: center;
            padding: 16px;
            border-radius: 99px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            transition: opacity 0.2s;
        }
        .footer {
            padding: 32px 40px;
            background-color: #050507;
            text-align: center;
            font-size: 11px;
            color: #3f3f46;
            border-top: 1px solid #18181b;
        }
        .footer a { color: #52525b; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-top: 12px;">
                    <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div style="font-size: 18px; font-weight: 800; color: #ffffff; letter-spacing: -0.05em;">FLEETCO</div>
        </div>
        
        <div class="content">
            <div class="alert-badge">Security Protocol: Breach</div>
            <h1>Abnormal Activity Detected</h1>
            <p>Our telematics heuristic engine has flagged a high-risk deviation from the expected vehicle route. Immediate review is advised.</p>
            
            <div class="info-grid">
                <div class="info-row">
                    <span class="label">Vehicle</span>
                    <span class="value">{{ $vehicleName }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Operator</span>
                    <span class="value">{{ $driverName }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Incident</span>
                    <span class="value">{{ $incidentType }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Location</span>
                    <span class="value">Centroid Deviation {{ $deviation }}km</span>
                </div>
            </div>
            
            <a href="{{ $dashboardUrl }}" class="cta">Open Operations Hub</a>
        </div>
        
        <div class="footer">
            &copy; {{ date('Y') }} Fleetco Professional. All rights reserved.<br>
            <span style="margin-top: 8px; display: block;">This is an automated security protocol. <a href="#">Unsubscribe</a></span>
        </div>
    </div>
</body>
</html>
