<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Accueil</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui, -apple-system, sans-serif; background: #f3f4f6; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .card { background: white; border-radius: 8px; padding: 20px; margin: 20px 0; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        h1 { color: #1f2937; margin: 20px 0; }
        p { color: #6b7280; line-height: 1.6; }
        .btn { display: inline-block; padding: 10px 20px; background: #2563eb; color: white; border-radius: 6px; margin: 10px 10px 10px 0; border: none; cursor: pointer; text-decoration: none; }
        .btn:hover { background: #1d4ed8; }
        .btn-danger { background: #dc2626; }
        .btn-danger:hover { background: #b91c1c; }
        form { display: inline; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>✓ Connexion Réussie!</h1>
            <p>Bienvenue <strong><?php echo e(auth()->user()->name); ?></strong></p>
            <p>Email: <strong><?php echo e(auth()->user()->email); ?></strong></p>
        </div>

        <div class="card">
            <h2>Menu</h2>
            <p>
                <a href="/" class="btn">📋 Accueil</a>
                <a href="/employees" class="btn">👥 Employés</a>
                <a href="/history" class="btn">📊 Historique</a>
            </p>
            <form method="POST" action="/logout">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-danger">🚪 Déconnexion</button>
            </form>
        </div>

        <div class="card">
            <h3>Informations</h3>
            <p>
                <strong>Application:</strong> Présence Chantier<br>
                <strong>Utilisateur:</strong> <?php echo e(auth()->user()->email); ?><br>
                <strong>Date/Heure:</strong> <?php echo e(now()->format('d/m/Y H:i:s')); ?><br>
                <strong>Session ID:</strong> <?php echo e(session()->getId()); ?>

            </p>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\Shadow\benka\resources\views/home-simple-test.blade.php ENDPATH**/ ?>