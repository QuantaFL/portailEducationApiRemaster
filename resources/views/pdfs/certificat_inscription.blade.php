<html>
<head>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .title { font-size: 22px; font-weight: bold; }
        .content { margin: 30px; font-size: 16px; }
        .footer { margin-top: 40px; text-align: right; font-size: 14px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Certificat d'inscription</div>
        <div>Année scolaire {{ date('Y') }} - {{ date('Y')+1 }}</div>
    </div>
    <div class="content">
        <p>Nous certifions que <strong>{{ $name }}</strong> est inscrit(e) en tant que <strong>{{ $role }}</strong> sur le portail éducatif.</p>
        <p>Email de l'élève : <strong>{{ $email }}</strong></p>
        <p>Date d'inscription : <strong>{{ date('d/m/Y') }}</strong></p>
    </div>
    <div class="footer">
        <p>Fait à Dakar, le {{ date('d/m/Y') }}</p>
        <p>L'administration</p>
    </div>
</body>
</html>

