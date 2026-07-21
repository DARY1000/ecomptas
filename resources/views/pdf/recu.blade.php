<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu — {{ $abonnement->transaction_id }}</title>
    <style>
        @page { margin: 40px 45px; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 12px; }
        .top { width: 100%; margin-bottom: 28px; }
        .top td { vertical-align: top; }
        h1 { font-size: 26px; font-weight: 700; color: #111827; }
        .logo-box { text-align: right; }
        .logo-mark { display: inline-block; width: 34px; height: 34px; background: #065f46; border-radius: 8px;
                     color: #ffffff; font-weight: 900; font-size: 18px; text-align: center; line-height: 34px; }
        .meta { margin-top: 14px; }
        .meta p { font-size: 11px; color: #374151; margin-bottom: 2px; }
        .meta strong { display: inline-block; width: 130px; color: #111827; }
        .parties { width: 100%; margin: 22px 0 26px; }
        .parties td { vertical-align: top; width: 50%; }
        .parties h3 { font-size: 10px; text-transform: uppercase; letter-spacing: .04em; color: #6b7280; margin-bottom: 6px; }
        .parties p { font-size: 11.5px; line-height: 1.5; color: #111827; }
        .amount-line { font-size: 15px; font-weight: 700; color: #111827; margin-bottom: 4px; }
        .amount-sub { font-size: 11px; color: #6b7280; margin-bottom: 22px; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.items thead th { text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: .03em;
                                color: #6b7280; padding-bottom: 8px; border-bottom: 1px solid #d1d5db; }
        table.items thead th.num { text-align: right; }
        table.items tbody td { padding: 10px 0; font-size: 12px; border-bottom: 1px solid #f3f4f6; }
        table.items tbody td.num { text-align: right; }
        table.items tbody .desc-sub { font-size: 10.5px; color: #6b7280; margin-top: 2px; }
        .totals { width: 100%; margin-top: 4px; }
        .totals table { width: 220px; margin-left: auto; border-collapse: collapse; }
        .totals td { padding: 5px 0; font-size: 11.5px; border-bottom: 1px solid #f3f4f6; }
        .totals td.num { text-align: right; }
        .totals .grand td { font-weight: 700; font-size: 12.5px; border-bottom: none; padding-top: 8px; }
        .footer { margin-top: 40px; padding-top: 14px; border-top: 1px solid #e5e7eb; font-size: 10px; color: #9ca3af; }
    </style>
</head>
<body>

    <table class="top">
        <tr>
            <td>
                <h1>Reçu</h1>
                <div class="meta">
                    <p><strong>Numéro de reçu</strong> {{ $abonnement->transaction_id ?? '—' }}</p>
                    <p><strong>Date de paiement</strong> {{ $abonnement->created_at->translatedFormat('d F Y') }}</p>
                </div>
            </td>
            <td class="logo-box">
                @if($logoPath && file_exists(public_path('storage/'.$logoPath)))
                    <img src="{{ public_path('storage/'.$logoPath) }}" alt="eCompta360" style="height:34px;">
                @else
                    <span class="logo-mark">e</span>
                @endif
            </td>
        </tr>
    </table>

    <table class="parties">
        <tr>
            <td>
                <h3>De</h3>
                <p>
                    <strong>eCompta360</strong><br>
                    Cotonou, Bénin<br>
                    Zone OHADA<br>
                    contact@ecompta360.com
                </p>
            </td>
            <td>
                <h3>Facturé à</h3>
                <p>
                    <strong>{{ $tenant->nom }}</strong><br>
                    @if($tenant->adresse){{ $tenant->adresse }}<br>@endif
                    @if($tenant->ville){{ $tenant->ville }}<br>@endif
                    {{ $tenant->email_contact }}
                </p>
            </td>
        </tr>
    </table>

    <div class="amount-line">{{ number_format((float) $abonnement->montant_xof, 0, ',', ' ') }} FCFA payés le {{ $abonnement->created_at->translatedFormat('d F Y') }}</div>
    <div class="amount-sub">Paiement par {{ ucfirst(str_replace('_', ' ', $abonnement->processeur_paiement ?? 'Mobile Money')) }}</div>

    <table class="items">
        <thead>
            <tr>
                <th>Description</th>
                <th class="num">Qté</th>
                <th class="num">Prix unitaire</th>
                <th class="num">Montant</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    Abonnement eCompta360 — Plan {{ $abonnement->plan?->nom }}
                    <div class="desc-sub">{{ $abonnement->debut_le?->format('d/m/Y') }} — {{ $abonnement->expire_le?->format('d/m/Y') }}</div>
                </td>
                <td class="num">1</td>
                <td class="num">{{ number_format((float) $abonnement->montant_xof, 0, ',', ' ') }} FCFA</td>
                <td class="num">{{ number_format((float) $abonnement->montant_xof, 0, ',', ' ') }} FCFA</td>
            </tr>
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr><td>Sous-total</td><td class="num">{{ number_format((float) $abonnement->montant_xof, 0, ',', ' ') }} FCFA</td></tr>
            <tr><td>Total</td><td class="num">{{ number_format((float) $abonnement->montant_xof, 0, ',', ' ') }} FCFA</td></tr>
            <tr class="grand"><td>Montant payé</td><td class="num">{{ number_format((float) $abonnement->montant_xof, 0, ',', ' ') }} FCFA</td></tr>
        </table>
    </div>

    <div class="footer">
        eCompta360 — Comptabilité intelligente pour l'espace OHADA. Ce reçu tient lieu de justificatif de paiement.
    </div>

</body>
</html>
