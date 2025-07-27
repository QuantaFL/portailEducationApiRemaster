<html>
<body>
    <h2>Bienvenue sur le portail éducatif</h2>
    <p>Bonjour {{ $name }},</p>
    <p>Votre compte ({{ $role }}) a été créé avec succès.</p>
    <p>Voici vos identifiants de connexion :</p>
    <ul>
        <li>Email : <strong>{{ $email }}</strong></li>
        <li>Mot de passe : <strong>{{ $password }}</strong></li>
    </ul>
    <p>Merci de changer votre mot de passe après la première connexion.</p>
    <p>Cordialement,<br>L'équipe du portail éducatif</p>
</body>
</html>

