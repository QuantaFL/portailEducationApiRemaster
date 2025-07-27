<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; color: #222; }
        .container { background: #fff; border-radius: 8px; padding: 32px; max-width: 600px; margin: 30px auto; box-shadow: 0 2px 8px #e0e0e0; }
        .logo { width: 80px; margin-bottom: 16px; }
        .title { color: #1976d2; font-size: 24px; font-weight: bold; margin-bottom: 8px; }
        .subtitle { font-size: 18px; margin-bottom: 20px; }
        .info { background: #e3f2fd; padding: 12px 18px; border-radius: 6px; margin-bottom: 18px; }
        .important { color: #b71c1c; font-weight: bold; }
        .footer { margin-top: 30px; font-size: 13px; color: #888; }
    </style>
</head>
<body>
<div class="container">
    <img src="{{ asset('images/school_logo.png') }}" class="logo" alt="Logo école" />
    <div class="title">Bienvenue sur le portail éducatif</div>
    <div class="subtitle">Bonjour {{ $name }},</div>
    <div class="info">
        Votre compte <strong>{{ $role }}</strong> a été créé avec succès.<br>
        <ul>
            <li>Email : <strong>{{ $email }}</strong></li>
            <li>Mot de passe : <strong>{{ $password }}</strong></li>
        </ul>
        <span style="font-size:13px;">Merci de changer votre mot de passe après la première connexion.</span>
    </div>
    <p>Vous trouverez en pièces jointes :</p>
    <ul>
        <li><strong>Certificat d'inscription</strong> (PDF personnalisé pour l'élève)</li>
        <li><strong>Règlement intérieur</strong> (PDF à lire attentivement, à destination des parents)</li>
    </ul>
    <p class="important">Rappel aux parents : le paiement des frais de scolarité doit être effectué <u>au plus tard le 05 de chaque mois</u>. Tout retard pourra entraîner des pénalités ou la suspension temporaire de l’accès aux cours.</p>
    <p>Nous vous souhaitons une excellente année scolaire !<br>L’équipe pédagogique</p>
    <div class="footer">
        &copy; {{ date('Y') }} Portail éducatif – Tous droits réservés
    </div>
</div>
</body>
</html>
