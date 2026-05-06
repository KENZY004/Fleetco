<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Fleetco</title>
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
        .welcome-badge {
            display: inline-block;
            padding: 4px 12px;
            background-color: rgba(255, 138, 0, 0.1);
            border: 1px solid rgba(255, 138, 0, 0.2);
            border-radius: 99px;
            color: #ff8a00;
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
        .setup-card {
            background-color: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 32px;
        }
        .step {
            display: flex;
            gap: 16px;
            margin-bottom: 20px;
        }
        .step-num {
            width: 24px;
            height: 24px;
            background-color: #18181b;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 800;
            color: #ff8a00;
            flex-shrink: 0;
        }
        .step-content {
            font-size: 13px;
            color: #ffffff;
            font-weight: 600;
        }
        .step-desc {
            font-size: 11px;
            color: #52525b;
            margin-top: 4px;
            font-weight: 400;
        }
        
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
            <div class="welcome-badge">Operator Activation</div>
            <h1>Welcome to the Fleet, {{ $driverName }}</h1>
            <p>Your account has been officially activated in the Fleetco Operations Hub. You are now authorized to begin shifts and track your telemetry.</p>
            
            <div class="setup-card">
                <div class="step">
                    <div class="step-num">1</div>
                    <div>
                        <div class="step-content">Access Co-Pilot</div>
                        <div class="step-desc">Open the mobile dashboard to start your tracking session.</div>
                    </div>
                </div>
                <div class="step">
                    <div class="step-num">2</div>
                    <div>
                        <div class="step-content">Vehicle Sync</div>
                        <div class="step-desc">Scan your vehicle QR or select from the active fleet.</div>
                    </div>
                </div>
                <div class="step" style="margin-bottom: 0;">
                    <div class="step-num">3</div>
                    <div>
                        <div class="step-content">Go Live</div>
                        <div class="step-desc">Start your shift to transmit real-time GPS and safety data.</div>
                    </div>
                </div>
            </div>
            
            <a href="{{ $mobileLink }}" class="cta">Launch Driver Co-Pilot</a>
        </div>
        
        <div class="footer">
            &copy; {{ date('Y') }} Fleetco Professional. Secure Telematics Network.<br>
            <span style="margin-top: 8px; display: block;">Authorized Personnel Only.</span>
        </div>
    </div>
</body>
</html>
