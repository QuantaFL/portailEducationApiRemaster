<html>
<head>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .logo { width: 100px; margin-bottom: 10px; }
        .title { font-size: 20px; font-weight: bold; }
        .content { margin: 20px; font-size: 15px; }
        .important { color: #b71c1c; font-weight: bold; }
        .footer { margin-top: 30px; font-size: 13px; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ $logo }}" class="logo" alt="Logo école" />
        <div class="title">Règlement Intérieur de l'École</div>
    </div>
    <div class="content">
        <p>Chers parents,</p>
        <p>Merci d’avoir inscrit votre enfant <strong>{{ $name }}</strong> en tant que <strong>{{ $role }}</strong> dans notre établissement.</p>
        <p>Veuillez trouver ci-dessous les règles essentielles à respecter pour garantir un environnement scolaire harmonieux :</p>
        <ul>
            <li>Respect des horaires : les cours commencent à 8h précises.</li>
            <li>Tenue correcte exigée pour tous les élèves.</li>
            <li>Respect du personnel, des camarades et du matériel scolaire.</li>
            <li>Absence à justifier par écrit sous 48h.</li>
            <li>Participation active aux activités pédagogiques et parascolaires.</li>
        </ul>
        <p class="important">Rappel : Le paiement des frais de scolarité doit être effectué au plus tard le <u>05 de chaque mois</u>. Tout retard pourra entraîner des pénalités ou la suspension temporaire de l’accès aux cours.</p>
        <p>Pour toute question, contactez l’administration à tout moment.</p>
    </div>
    <div class="footer">
        <p>Nous vous remercions pour votre confiance.<br>L’équipe pédagogique de Quanta</p>
    </div>
</body>
</html>

