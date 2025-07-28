<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contrat de Travail Enseignant</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Roboto:wght@400;500&display=swap');
        body {
            font-family: 'Roboto', Arial, sans-serif;
            font-size: 13pt;
            line-height: 1.7;
            margin: 0;
            padding: 0;
            background: #f7f7fa;
            color: #222;
        }
        .contract-container {
            background: #fff;
            max-width: 900px;
            margin: 40px auto 40px auto;
            box-shadow: 0 8px 32px rgba(44,62,80,0.12);
            border-radius: 18px;
            padding: 48px 60px 48px 60px;
            border: 1.5px solid #e0e0e0;
        }
        .header {
            text-align: center;
            margin-bottom: 36px;
        }
        .header img {
            max-width: 120px;
            margin-bottom: 10px;
        }
        .header p {
            margin: 0;
            font-size: 13pt;
        }
        .school-name {
            font-family: 'Playfair Display', serif;
            font-size: 28pt;
            font-weight: 700;
            color: #1a237e;
            margin-bottom: 4px;
            letter-spacing: 1px;
        }
        .school-contact {
            color: #444;
            font-size: 12pt;
        }
        .school-meta {
            color: #666;
            font-size: 11pt;
        }
        .title {
            text-align: center;
            font-family: 'Playfair Display', serif;
            font-size: 22pt;
            margin-bottom: 40px;
            color: #263238;
            letter-spacing: 1px;
            text-transform: uppercase;
            border-bottom: 2px solid #1a237e;
            padding-bottom: 10px;
        }
        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 16pt;
            margin-top: 36px;
            color: #1a237e;
            text-decoration: none;
            border-left: 5px solid #1a237e;
            padding-left: 12px;
            margin-bottom: 10px;
        }
        .content-indent {
            margin-left: 36px;
            margin-bottom: 18px;
        }
        ul {
            list-style-type: disc;
            margin-left: 36px;
            padding-left: 0;
        }
        li {
            margin-bottom: 6px;
        }
        .signature-block {
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
        }
        .signature-item {
            text-align: center;
            width: 44%;
        }
        .signature-box {
            border: 1.5px solid #1a237e;
            height: 80px;
            width: 220px;
            margin: 18px auto 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11pt;
            color: #555;
            border-radius: 8px;
            background: #f5f7fa;
        }
        .attachment-section {
            margin-top: 60px;
            border-top: 1.5px solid #e0e0e0;
            padding-top: 24px;
        }
        .attachment-box {
            border: 1.5px solid #1a237e;
            height: 100px;
            width: 100%;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11pt;
            color: #555;
            border-radius: 8px;
            background: #f5f7fa;
        }
        .footer {
            text-align: center;
            margin-top: 60px;
            font-size: 11pt;
            color: #888;
        }
        .info-label {
            color: #1a237e;
            font-weight: 500;
        }
        .content-indent strong {
            color: #263238;
        }
        @media print {
            body, .contract-container {
                background: #fff !important;
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
            }
        }
    </style>
</head>
<body>
    <div class="contract-container">
        <div class="header">
            <img src="{{ public_path('images/school_logo.png') }}" alt="Logo de l'école">
            <div class="school-name">{{ $data['nom_etablissement'] ?? 'NOM DE L\'ÉTABLISSEMENT SCOLAIRE' }}</div>
            <div class="school-contact">{{ $data['adresse_etablissement'] ?? 'ADRESSE COMPLÈTE DE L\'ÉTABLISSEMENT' }}</div>
            <div class="school-contact">{{ $data['telephone_etablissement'] ?? 'TÉLÉPHONE DE L\'ÉTABLISSEMENT' }} - {{ $data['email_etablissement'] ?? 'EMAIL DE L\'ÉTABLISSEMENT' }}</div>
            <div class="school-meta">Statut Juridique : {{ $data['statut_juridique'] ?? 'STATUT JURIDIQUE DE L\'ÉTABLISSEMENT' }}</div>
            <div class="school-meta">NINEA : {{ $data['ninea_etablissement'] ?? 'NINEA DE L\'ÉTABLISSEMENT' }}</div>
        </div>
        <div class="title">CONTRAT DE TRAVAIL D'ENSEIGNANT</div>

        <p>Entre les soussignés :</p>

        <p class="content-indent">
            <strong>1. L'EMPLOYEUR :</strong><br>
            <strong>{{ $data['nom_etablissement'] ?? 'NOM DE L\'ÉTABLISSEMENT SCOLAIRE' }}</strong><br>
            Représenté par : <strong>{{ $data['nom_representant_legal'] ?? 'NOM ET PRÉNOM DU REPRÉSENTANT LÉGAL' }}</strong><br>
            Qualité : <strong>{{ $data['qualite_representant_legal'] ?? 'QUALITÉ DU REPRÉSENTANT LÉGAL' }}</strong><br>
            Adresse : <strong>{{ $data['adresse_etablissement'] ?? 'ADRESSE COMPLÈTE DE L\'ÉTABLISSEMENT' }}</strong><br>
            Ci-après désigné "l'Employeur".
        </p>

        <p class="content-indent" style="margin-top: 20px;">
            <strong>2. L'EMPLOYÉ :</strong><br>
            Nom : <strong>{{ $data['nom_enseignant'] ?? 'NOM DE L\'ENSEIGNANT' }}</strong><br>
            Prénom(s) : <strong>{{ $data['prenom_enseignant'] ?? 'PRÉNOM(S) DE L\'ENSEIGNANT' }}</strong><br>
            Date et Lieu de Naissance : <strong>{{ $data['date_lieu_naissance_enseignant'] ?? 'DATE ET LIEU DE NAISSANCE DE L\'ENSEIGNANT' }}</strong><br>
            Nationalité : <strong>{{ $data['nationalite_enseignant'] ?? 'NATIONALITÉ DE L\'ENSEIGNANT' }}</strong><br>
            Numéro CNI/Passeport : <strong>{{ $data['cni_passeport_enseignant'] ?? 'NUMÉRO CNI OU PASSEPORT DE L\'ENSEIGNANT' }}</strong><br>
            Adresse : <strong>{{ $data['adresse_enseignant'] ?? 'ADRESSE COMPLÈTE DE L\'ENSEIGNANT' }}</strong><br>
            Téléphone : <strong>{{ $data['telephone_enseignant'] ?? 'TÉLÉPHONE DE L\'ENSEIGNANT' }}</strong><br>
            Email : <strong>{{ $data['email_enseignant'] ?? 'EMAIL DE L\'ENSEIGNANT' }}</strong><br>
            Ci-après désigné "l'Employé".
        </p>

        <p style="margin-top: 30px;">Il a été convenu et arrêté ce qui suit :</p>

        <h2 class="section-title">ARTICLE 1 : OBJET DU CONTRAT ET INTITULÉ DU POSTE</h2>
        <p>Le présent contrat a pour objet de définir les termes et conditions de l'emploi de l'Employé en qualité d'<strong>ENSEIGNANT</strong> au sein de l'établissement scolaire {{ $data['nom_etablissement'] ?? 'NOM DE L\'ÉTABLISSEMENT SCOLAIRE' }}.</p>
        <p>L'Employé sera chargé d'enseigner les matières suivantes : <strong>{{ $data['matieres_enseignees'] ?? 'MATIÈRES ENSEIGNÉES' }}</strong> aux niveaux : <strong>{{ $data['niveaux_enseignement'] ?? 'NIVEAUX D\'ENSEIGNEMENT' }}</strong>.</p>

        <h2 class="section-title">ARTICLE 2 : TYPE DE CONTRAT ET DURÉE</h2>
        <p>Le présent contrat est un <strong>Contrat à Durée Indéterminée (CDI)</strong>.</p>
        <p>La prise de fonction est fixée au <strong>{{ $data['date_prise_fonction'] ?? 'DATE DE PRISE DE FONCTION' }}</strong>.</p>

        <h2 class="section-title">ARTICLE 3 : RÉMUNÉRATION</h2>
        <p>En contrepartie de ses services, la rémunération de l'Employé sera calculée en fonction du nombre et de la nature des affectations pédagogiques qui lui seront confiées au cours de l'année scolaire, selon une grille tarifaire interne à l'établissement. Chaque affectation donnera lieu à une somme déterminée, dont le détail sera communiqué à l'Employé au moment de l'affectation.</p>
        <p>Le paiement des sommes dues sera effectué à la fin de chaque mois civil, par <strong>{{ $data['mode_paiement'] ?? 'MODE DE PAIEMENT' }}</strong>, et correspondra aux affectations réalisées durant la période concernée.</p>
        <p>L'Employeur s'engage à affilier l'Employé aux organismes sociaux (IPRES, CSS) et à s'acquitter des cotisations patronales et salariales conformément à la législation en vigueur. Les informations relatives à l'affiliation seront gérées par le système interne de l'établissement.</p>

        <h2 class="section-title">ARTICLE 4 : HORAIRES DE TRAVAIL</h2>
        <p>La durée hebdomadaire de travail de l'Employé est fixée à <strong>{{ $data['nombre_heures_travail'] ?? 'NOMBRE D\'HEURES' }} heures</strong>, réparties du <strong>{{ $data['jour_debut_semaine'] ?? 'JOUR DE DÉBUT DE SEMAINE' }}</strong> au <strong>{{ $data['jour_fin_semaine'] ?? 'JOUR DE FIN DE SEMAINE' }}</strong>, selon l'emploi du temps établi par l'administration de l'école. Cet emploi du temps pourra être modifié en fonction des nécessités de service, dans le respect des dispositions légales.</p>
        <p>Ces heures incluent les heures d'enseignement, de préparation des cours, de correction des copies, de participation aux réunions pédagogiques et aux activités parascolaires.</p>

        <h2 class="section-title">ARTICLE 5 : OBLIGATIONS ET RESPONSABILITÉS DE L'EMPLOYÉ</h2>
        <p>L'Employé s'engage à :</p>
        <ul>
            <li>Assurer l'enseignement des matières et niveaux qui lui sont confiés avec professionnalisme et dévouement.</li>
            <li>Préparer ses cours, évaluer les élèves et assurer un suivi pédagogique rigoureux.</li>
            <li>Respecter les programmes officiels et les directives pédagogiques de l'établissement.</li>
            <li>Participer activement à la vie scolaire (réunions, conseils de classe, activités culturelles et sportives, etc.).</li>
            <li>Adopter un comportement exemplaire, respectueux des valeurs de l'établissement et des règles de déontologie de la profession.</li>
            <li>Respecter le règlement intérieur de l'établissement, dont il déclare avoir pris connaissance.</li>
            <li>Faire preuve de discrétion et de confidentialité concernant toutes les informations relatives à l'établissement, aux élèves, à leurs familles et au personnel.</li>
            <li>Se conformer aux instructions de la Direction de l'établissement dans le cadre de ses fonctions.</li>
        </ul>

        <h2 class="section-title">ARTICLE 6 : OBLIGATIONS DE L'EMPLOYEUR</h2>
        <p>L'Employeur s'engage à :</p>
        <ul>
            <li>Verser à l'Employé la rémunération convenue aux dates fixées.</li>
            <li>Fournir à l'Employé les moyens et les outils nécessaires à l'exercice de ses fonctions.</li>
            <li>Assurer un environnement de travail sûr et propice à l'épanouissement professionnel.</li>
            <li>Respecter les dispositions du Code du Travail sénégalais et les conventions collectives applicables.</li>
            <li>Assurer la couverture sociale de l'Employé conformément à la législation en vigueur.</li>
        </ul>

        <h2 class="section-title">ARTICLE 7 : CLAUSE DE CONFIDENTIALITÉ</h2>
        <p>L'Employé s'engage à observer la plus stricte confidentialité concernant toutes les informations, données, méthodes pédagogiques, listes d'élèves, informations financières ou toute autre information sensible dont il pourrait avoir connaissance dans le cadre de ses fonctions au sein de l'établissement. Cette obligation de confidentialité perdurera après la cessation du présent contrat, quelle qu'en soit la cause.</p>

        <h2 class="section-title">ARTICLE 8 : RÉSILIATION OU RUPTURE ANTICIPÉE</h2>
        <p>Le présent contrat pourra être résilié ou rompu de manière anticipée dans les conditions prévues par le Code du Travail sénégalais, notamment :</p>
        <ul>
            <li>Par accord mutuel des parties.</li>
            <li>Par démission de l'Employé, sous réserve du respect du préavis légal.</li>
            <li>Par licenciement de l'Employeur pour motif légitime (faute lourde, motif économique, etc.), dans le respect des procédures légales et conventionnelles.</li>
            <li>En cas de force majeure.</li>
        </ul>
        <p>Les délais de préavis et les indemnités de rupture seront ceux prévus par le Code du Travail sénégalais et les textes d'application en vigueur.</p>

        <h2 class="section-title">ARTICLE 9 : DROIT APPLICABLE ET RÈGLEMENT DES LITIGES</h2>
        <p>Le présent contrat est régi par le droit sénégalais, et notamment par la <strong>Loi n° 97-17 du 1er décembre 1997 portant Code du Travail</strong>, telle que modifiée, ainsi que par les textes d'application et les conventions collectives applicables.</p>
        <p>Tout litige né de l'exécution ou de l'interprétation du présent contrat sera soumis, avant toute action judiciaire, à une tentative de conciliation devant l'Inspection du Travail et de la Sécurité Sociale compétente. À défaut de conciliation, le litige sera porté devant le Tribunal du Travail de <strong>{{ $data['ville_tribunal_competent'] ?? 'VILLE DU TRIBUNAL COMPÉTENT' }}</strong>, seul compétent.</p>

        <h2 class="section-title">ARTICLE 10 : DISPOSITIONS GÉNÉRALES</h2>
        <p>Le présent contrat annule et remplace toute autre convention, écrite ou orale, antérieure entre les parties et ayant le même objet.</p>
        <p>Toute modification du présent contrat devra faire l'objet d'un avenant écrit et signé par les deux parties.</p>

        <p style="margin-top: 40px;">Fait à <strong>{{ $data['lieu_signature'] ?? 'LIEU DE SIGNATURE (géré par le système)' }}</strong>, le <strong>{{ $data['date_signature'] ?? 'DATE DU JOUR DE LA SIGNATURE (gérée par le système)' }}</strong>.</p>

        <div class="signature-block">
            <div class="signature-item">
                <p style="font-weight: bold; margin-bottom: 50px;">L'EMPLOYEUR</p>
                <p style="margin-bottom: 5px;">{{ $data['nom_representant_legal'] ?? 'NOM ET PRÉNOM DU REPRÉSENTANT LÉGAL' }}</p>
                <p style="margin-bottom: 5px;">{{ $data['qualite_representant_legal'] ?? 'QUALITÉ DU REPRÉSENTANT LÉGAL' }}</p>
                <p style="margin-top: 50px;">Signature :</p>
                <div class="signature-box">
                    (Cachet de l'établissement)
                </div>
            </div>
            <div class="signature-item">
                <p style="font-weight: bold; margin-bottom: 50px;">L'EMPLOYÉ</p>
                <p style="margin-bottom: 5px;">{{ $data['nom_enseignant'] ?? 'NOM DE L\'ENSEIGNANT' }}</p>
                <p style="margin-top: 50px;">Lu et approuvé :</p>
                <p style="margin-top: 50px;">Signature manuscrite :</p>
                <div class="signature-box"></div>
            </div>
        </div>



        <div class="footer">
            <p>{{ $data['nom_etablissement'] ?? 'NOM DE L\'ÉTABLISSEMENT SCOLAIRE' }} - {{ $data['adresse_etablissement'] ?? 'ADRESSE COMPLÈTE DE L\'ÉTABLISSEMENT' }}</p>
            <p>Contrat de travail - Enseignant - Page 1 sur 1</p>
        </div>
    </div>
</body>
</html>
