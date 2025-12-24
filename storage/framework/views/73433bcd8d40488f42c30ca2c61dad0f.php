<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tableau de bord</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui, -apple-system, sans-serif; background: #f3f4f6; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .card { background: white; border-radius: 8px; padding: 20px; margin: 20px 0; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        h1 { color: #1f2937; margin: 20px 0; }
        h2 { color: #374151; margin: 15px 0 10px 0; font-size: 18px; }
        p { color: #6b7280; line-height: 1.6; margin: 10px 0; }
        .btn { display: inline-block; padding: 10px 20px; background: #2563eb; color: white; border-radius: 6px; margin: 10px 10px 10px 0; border: none; cursor: pointer; text-decoration: none; }
        .btn:hover { background: #1d4ed8; }
        .btn-danger { background: #dc2626; }
        .btn-danger:hover { background: #b91c1c; }
        form { display: inline; }
        .user-info { background: #f0f9ff; padding: 15px; border-left: 4px solid #2563eb; border-radius: 4px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>✓ Tableau de Bord</h1>
            <div class="user-info">
                <p><strong>Bienvenue</strong> <?php echo e(auth()->user()->name); ?>!</p>
                <p><strong>Email:</strong> <?php echo e(auth()->user()->email); ?></p>
                <p><strong>Date/Heure:</strong> <?php echo e(now()->format('d/m/Y H:i:s')); ?></p>
            </div>
        </div>

        <div class="card">
            <h2>Navigation</h2>
            <p>
                <a href="/dashboard" class="btn">📊 Tableau de Bord</a>
                <a href="/employees" class="btn">👥 Employés</a>
                <a href="/history" class="btn">📋 Historique</a>
            </p>
        </div>

        <div class="card">
            <h2>Actions</h2>
            <form method="POST" action="/logout">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-danger">🚪 Déconnexion</button>
            </form>
        </div>

        <div class="card">
            <h2>Informations Système</h2>
            <p>
                <strong>Application:</strong> Présence Chantier<br>
                <strong>Session ID:</strong> <code><?php echo e(session()->getId()); ?></code><br>
                <strong>Environnement:</strong> <?php echo e(config('app.env')); ?>

            </p>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\Shadow\benka\resources\views/dashboard.blade.php ENDPATH**/ ?>