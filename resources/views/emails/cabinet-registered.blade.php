<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue sur eCompta360</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f4f6f9; color: #1f2937; }
        .wrapper { max-width: 580px; margin: 0 auto; padding: 32px 16px; }
        .card { background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 16px rgba(0,0,0,.06); }
        .header { background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%); padding: 32px 32px 28px; text-align: center; }
        .logo-box { display: inline-flex; align-items: center; gap: 10px; }
        .logo-icon { width: 40px; height: 40px; background: #ffffff; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
        .logo-text { font-size: 22px; font-weight: 900; color: #ffffff; letter-spacing: -0.5px; }
        .body { padding: 36px 32px; }
        h1 { font-size: 22px; font-weight: 800; color: #111827; margin-bottom: 8px; }
        .subtitle { color: #6b7280; font-size: 14px; margin-bottom: 28px; line-height: 1.5; }
        .info-box { background: #f0f9ff; border-left: 4px solid #3b82f6; border-radius: 8px; padding: 16px 20px; margin-bottom: 24px; }
        .info-row { display: flex; justify-content: space-between; align-items: center; padding: 6px 0; font-size: 13px; }
        .info-label { color: #6b7280; }
        .info-value { font-weight: 600; color: #111827; }
        .btn { display: inline-block; background: #1d4ed8; color: #ffffff !important; font-weight: 700; font-size: 15px; padding: 14px 32px; border-radius: 10px; text-decoration: none; margin: 8px 0 24px; }
        .divider { border: none; border-top: 1px solid #f3f4f6; margin: 24px 0; }
        .steps { counter-reset: step; }
        .step { display: flex; gap: 12px; margin-bottom: 14px; align-items: flex-start; }
        .step-num { width: 24px; height: 24px; background: #eff6ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; color: #1d4ed8; flex-shrink: 0; margin-top: 1px; }
        .step-text { font-size: 13px; color: #374151; line-height: 1.5; }
        .step-text strong { color: #111827; }
        .footer { text-align: center; padding: 24px 32px; background: #f9fafb; border-top: 1px solid #f3f4f6; }
        .footer p { font-size: 12px; color: #9ca3af; line-height: 1.6; }
        .footer a { color: #6b7280; text-decoration: none; }
        .badge { display: inline-block; background: #dcfce7; color: #166534; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; margin-bottom: 20px; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="card">
        <!-- Header -->
        <div class="header">
            <div class="logo-box">
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
            <span class="badge">✓ Compte activé</span>
            <h1>Bienvenue, {{ $user->name }} !</h1>
            <p class="subtitle">
                Votre espace comptable est prêt. Vous disposez de <strong>15 jours d'essai gratuit</strong>
                pour découvrir toutes les fonctionnalités d'eCompta360.
            </p>

            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Cabinet</span>
                    <span class="info-value">{{ $tenant->nom }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email de connexion</span>
                    <span class="info-value">{{ $user->email }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Offre</span>
                    <span class="info-value">Essai gratuit — 15 jours</span>
                </div>
                @if($tenant->abonnement_expire_le)
                <div class="info-row">
                    <span class="info-label">Expire le</span>
                    <span class="info-value">{{ $tenant->abonnement_expire_le->format('d/m/Y') }}</span>
                </div>
                @endif
            </div>

            <div style="text-align: center;">
                <a href="{{ route('login') }}" class="btn">Accéder à mon espace →</a>
            </div>

            <hr class="divider">

            <p style="font-size:13px; font-weight:700; color:#111827; margin-bottom:12px;">Pour démarrer :</p>
            <div class="steps">
                <div class="step">
                    <div class="step-num">1</div>
                    <div class="step-text"><strong>Connectez-vous</strong> avec votre email et le mot de passe choisi lors de l'inscription.</div>
                </div>
                <div class="step">
                    <div class="step-num">2</div>
                    <div class="step-text"><strong>Uploadez votre première facture</strong> — l'IA l'analyse et génère les écritures SYSCOHADA automatiquement.</div>
                </div>
                <div class="step">
                    <div class="step-num">3</div>
                    <div class="step-text"><strong>Validez les écritures</strong> proposées et exportez votre journal comptable en Excel ou CSV.</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Une question ? Écrivez-nous à <a href="mailto:contact@ecompta360.com">contact@ecompta360.com</a></p>
            <p style="margin-top:8px;">© {{ date('Y') }} eCompta360 · Comptabilité Intelligente · Zone OHADA</p>
        </div>
    </div>
</div>
</body>
</html>
