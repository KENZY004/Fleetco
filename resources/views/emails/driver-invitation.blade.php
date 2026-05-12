<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Invitation — FleetCo</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #0a0a0a; font-family: 'Inter', -apple-system, sans-serif; color: #fff; }
        .wrapper { max-width: 600px; margin: 0 auto; padding: 40px 20px; }
        .header { text-align: center; padding: 40px 0 32px; border-bottom: 1px solid rgba(255,138,0,0.2); }
        .logo { font-size: 11px; font-weight: 900; letter-spacing: 0.5em; text-transform: uppercase; color: #ff8a00; }
        .divider { width: 40px; height: 1px; background: #ff8a00; margin: 16px auto; }
        .content { padding: 40px 0; }
        .label { font-size: 10px; font-weight: 700; letter-spacing: 0.3em; text-transform: uppercase; color: #ff8a00; margin-bottom: 12px; }
        h1 { font-size: 28px; font-weight: 800; line-height: 1.2; margin-bottom: 20px; }
        p { font-size: 14px; line-height: 1.8; color: #a1a1aa; margin-bottom: 16px; }
        .fleet-badge { display: inline-block; background: rgba(255,138,0,0.1); border: 1px solid rgba(255,138,0,0.3); color: #ff8a00; font-size: 11px; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase; padding: 6px 14px; border-radius: 100px; margin-bottom: 24px; }
        .cta-wrap { text-align: center; padding: 32px 0; }
        .cta { display: inline-block; background: #ff8a00; color: #000; font-size: 11px; font-weight: 900; letter-spacing: 0.3em; text-transform: uppercase; text-decoration: none; padding: 18px 40px; border-radius: 100px; }
        .meta-box { background: #111; border: 1px solid rgba(255,255,255,0.05); border-radius: 16px; padding: 20px 24px; margin: 24px 0; }
        .meta-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 12px; }
        .meta-row:last-child { border-bottom: none; }
        .meta-label { color: #71717a; font-weight: 600; }
        .meta-value { color: #fff; font-weight: 600; }
        .footer { text-align: center; padding: 32px 0 0; border-top: 1px solid rgba(255,255,255,0.05); }
        .footer p { font-size: 11px; color: #3f3f46; }
        .url-fallback { word-break: break-all; font-size: 11px; color: #52525b; margin-top: 16px; font-family: monospace; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <div class="logo">Fleet Co</div>
            <div class="divider"></div>
            <p style="font-size: 11px; color: #52525b; letter-spacing: 0.2em; text-transform: uppercase;">Secure Driver Portal Invitation</p>
        </div>

        <div class="content">
            <div class="label">Operator Invitation</div>
            <h1>You've been<br>invited to drive.</h1>

            <div class="fleet-badge">● {{ $fleetName }}</div>

            <p>
                <strong style="color:#fff;">{{ $inviter->name }}</strong> has invited you to join 
                <strong style="color:#fff;">{{ $fleetName }}</strong> on the FleetCo Driver Co-Pilot platform. 
                Accept your invitation to get started with your secure driver account.
            </p>

            <div class="meta-box">
                <div class="meta-row">
                    <span class="meta-label">Invited by</span>
                    <span class="meta-value">{{ $inviter->name }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Fleet</span>
                    <span class="meta-value">{{ $fleetName }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Expires</span>
                    <span class="meta-value">{{ $expiresAt }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Your email</span>
                    <span class="meta-value">{{ $invitation->email }}</span>
                </div>
            </div>

            <div class="cta-wrap">
                <a href="{{ $registerUrl }}" class="cta">Accept Invitation →</a>
            </div>

            <p style="font-size: 12px; text-align: center; color: #52525b;">
                This link will expire in 48 hours and can only be used once.
            </p>

            <div class="url-fallback">
                If the button doesn't work, copy and paste this URL:<br>
                {{ $registerUrl }}
            </div>
        </div>

        <div class="footer">
            <p>This invitation was sent to {{ $invitation->email }}</p>
            <p style="margin-top: 8px;">© {{ date('Y') }} FleetCo. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
