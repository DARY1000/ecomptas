<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de paiement — eCompta360</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f4f6f9; color: #1f2937; }
        .wrapper { max-width: 580px; margin: 0 auto; padding: 32px 16px; }
        .card { background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 16px rgba(0,0,0,.06); }
        .header { background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%); padding: 32px 32px 28px; text-align: center; }
        .logo-icon { width: 40px; height: 40px; background: #ffffff; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; vertical-align: middle; margin-right: 10px; }
        .logo-text { font-size: 22px; font-weight: 900; color: #ffffff; vertical-align: middle; }
        .body { padding: 36px 32px; }
        h1 { font-size: 22px; font-weight: 800; color: #111827; margin-bottom: 8px; }
        .subtitle { color: #6b7280; font-size: 14px; margin-bottom: 28px; line-height: 1.5; }
        .receipt { border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; margin-bottom: 24px; }
        .receipt-header { background: #f9fafb; padding: 14px 20px; border-bottom: 1px solid #e5e7eb; }
        .receipt-header p { font-size: 12px; color: #6b7280; }
        .receipt-header strong { font-size: 13px; color: #111827; display: block; margin-bottom: 2px; }
        .receipt-row { display: flex; justify-content: space-between; padding: 12px 20px; font-size: 13px; border-bottom: 1px solid #f3f4f6; }
        .receipt-row:last-child { border-bottom: none; }
        .receipt-label { color: #6b7280; }
        .receipt-value { font-weight: 600; color: #111827; }
        .total-row { background: #f0fdf4; padding: 14px 20px; display: flex; justify-content: space-between; border-top: 2px solid #dcfce7; }
        .total-label { font-weight: 700; color: #166534; font-size: 14px; }
        .total-value { font-weight: 800; color: #166534; font-size: 16px; }
        .status-badge { display: inline-block; background: #dcfce7; color: #166534; font-size: 12px; font-weight: 700; padding: 4px 12px; border-radius: 20px; margin-bottom: 20px; }
        .btn { display: inline-block; background: #1d4ed8; color: #ffffff !important; font-weight: 700; font-size: 15px; padding: 14px 32px; border-radius: 10px; text-decoration: none; margin: 16px 0 0; }
        .footer { text-align: center; padding: 24px 32px; background: #f9fafb; border-top: 1px solid #f3f4f6; }
        .footer p { font-size: 12px; color: #9ca3af; line-height: 1.6; }
        .footer a { color: #6b7280; text-decoration: none; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="card">
        <!-- Header -->
        <div class="header">
            <div>
                <div class="logo-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1d4ed8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="logo-text">eCompta360</span>
            </div>
        </div>

        <!-- Body -->
        <div class="body">
            <span class="status-badge">✓ Paiement confirmé</span>
            <h1>Votre paiement a été reçu</h1>
            <p class="subtitle">
                Merci {{ $user->name }}. Votre abonnement eCompta360 est actif.
                Retrouvez ci-dessous le récapitulatif de votre transaction.
            </p>

            <!-- Reçu -->
            <div class="receipt">
                <div class="receipt-header">
                    <strong>Reçu de paiement</strong>
                    <p>Référence : {{ $abonnement->transaction_id ?? 'N/A' }} · {{ $abonnement->created_at->format('d/m/Y à H:i') }}</p>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Cabinet</span>
                    <span class="receipt-value">{{ $abonnement->tenant?->nom }}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Offre souscrite</span>
                    <span class="receipt-value">{{ $abonnement->plan?->nom }}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Mode de paiement</span>
                    <span class="receipt-value">{{ ucfirst(str_replace('_', ' ', $abonnement->processeur_paiement ?? 'Mobile Money')) }}</span>
                </div>
                @if($abonnement->debut_periode && $abonnement->fin_periode)
                <div class="receipt-row">
                    <span class="receipt-label">Période</span>
                    <span class="receipt-value">
                        {{ $abonnement->debut_periode->format('d/m/Y') }} → {{ $abonnement->fin_periode->format('d/m/Y') }}
                    </span>
                </div>
                @endif
                <div class="receipt-row">
                    <span class="receipt-label">Statut</span>
                    <span class="receipt-value" style="color:#166534;">Payé</span>
                </div>
                <div class="total-row">
                    <span class="total-label">Total payé</span>
                    <span class="total-value">{{ number_format($abonnement->montant_xof, 0, ',', ' ') }} FCFA</span>
                </div>
            </div>

            <div style="text-align:center;">
                <a href="{{ route('dashboard') }}" class="btn">Accéder à mon espace →</a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Conservez cet email comme justificatif de paiement.</p>
            <p style="margin-top:6px;">Une question ? <a href="mailto:contact@ecompta360.com">contact@ecompta360.com</a></p>
            <p style="margin-top:8px;">© {{ date('Y') }} eCompta360 · Comptabilité Intelligente · Zone OHADA</p>
        </div>
    </div>
</div>
</body>
</html>
