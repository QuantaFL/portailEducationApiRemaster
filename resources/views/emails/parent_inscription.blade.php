<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; color: #222; }
        .container { background: #fff; border-radius: 8px; padding: 32px; max-width: 600px; margin: 30px auto; box-shadow: 0 2px 8px #e0e0e0; }
        .logo { width: 80px; margin-bottom: 16px; }
        .title { color: #1976d2; font-size: 22px; font-weight: bold; margin-bottom: 8px; }
        .subtitle { font-size: 17px; margin-bottom: 20px; }
        .footer { margin-top: 30px; font-size: 13px; color: #888; }
    </style>
</head>
<body>
<div class="container">
    <img src="{{ asset('images/school_logo.png') }}" class="logo" alt="Logo école" />
    <div class="title">Certificat d'inscription de votre enfant</div>
    <div class="subtitle">Madame, Monsieur,</div>
    <p>Veuillez trouver en pièce jointe le certificat d'inscription de votre enfant <strong>{{ $studentName }}</strong> {{ $studentRole }}.</p>
    <p>Nous restons à votre disposition pour toute information complémentaire.</p>
    <div class="footer">
        &copy; {{ date('Y') }} QUANTA SCHOOL – Tous droits réservés
    </div>
</div>
</body>
</html>
