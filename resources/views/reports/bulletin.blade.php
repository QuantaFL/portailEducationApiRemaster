<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bulletin de Notes - {{ $student_info['first_name'] }} {{ $student_info['last_name'] }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header img { max-width: 100px; }
        .student-info, .parent-info, .term-info { margin-bottom: 15px; border: 1px solid #eee; padding: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .appreciation { font-weight: bold; margin-top: 10px; }
        .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        {{-- Remplacez ceci par le chemin de votre logo --}}
        <img src="{{ public_path('images/school_logo.png') }}" alt="Logo de l'école">
        <h1>Bulletin de Notes</h1>
        <p>Année Académique: {{ $term_info['academic_year_label'] }}</p>
        <p>Trimestre: {{ $term_info['name'] }}</p>
    </div>

    <div class="student-info">
        <h2>Informations de l'élève</h2>
        <p><strong>Nom:</strong> {{ $student_info['last_name'] }}</p>
        <p><strong>Prénom:</strong> {{ $student_info['first_name'] }}</p>
        <p><strong>Matricule:</strong> {{ $student_info['matricule'] }}</p>
        <p><strong>Classe:</strong> {{ $student_info['class_name'] }}</p>
    </div>

    <div class="parent-info">
        <h2>Informations du parent</h2>
        <p><strong>Nom:</strong> {{ $parent_info['last_name'] }}</p>
        <p><strong>Prénom:</strong> {{ $parent_info['first_name'] }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Matière</th>
                <th>Coefficient</th>
                <th>Note Devoir</th>
                <th>Note Examen</th>
                <th>Moyenne</th>
                <th>Appréciation</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($subject_results as $result)
            <tr>
                <td>{{ $result['subject_name'] }}</td>
                <td>{{ $result['coefficient'] }}</td>
                <td>{{ $result['quiz_mark'] ?? 'N/A' }}</td>
                <td>{{ $result['exam_mark'] ?? 'N/A' }}</td>
                <td>{{ $result['average'] ?? 'N/A' }}</td>
                <td>{{ $result['appreciation'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="overall-summary">
        <h3>Moyenne Générale: {{ $overall_average }}</h3>
        <p class="appreciation">Appréciation Générale: {{ $overall_appreciation }}</p>
    </div>

    <div class="footer">
        <p>Fait à DAKAR, le {{ date('d/m/Y') }}</p>
        <p>Signature de la Direction</p>
    </div>
</body>
</html>
