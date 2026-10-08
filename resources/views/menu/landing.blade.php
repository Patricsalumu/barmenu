<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BarMenu</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: #111111;
            color: #f5f1ec;
            font-family: Arial, sans-serif;
        }
        .card {
            width: min(640px, calc(100% - 32px));
            background: #191817;
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 16px;
            padding: 36px 28px;
            text-align: center;
        }
        h1 {
            margin: 0 0 12px;
            font-size: clamp(2rem, 5vw, 3rem);
        }
        p {
            margin: 0 0 20px;
            color: #c9c4c0;
            line-height: 1.6;
        }
        .badge {
            display: inline-block;
            margin-bottom: 18px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(200, 59, 50, 0.12);
            color: #f08d7f;
            font-size: 12px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="badge">BarMenu</div>
        <h1>Choisissez votre établissement</h1>
        <p>Le domaine principal ne correspond à aucune entreprise. Accédez à votre espace via un sous-domaine dédié.</p>
        <p>Exemples : <strong>lecomptoir.barmenu.site</strong>, <strong>casabrisa.barmenu.site</strong>.</p>
    </div>
</body>
</html>
