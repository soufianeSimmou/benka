<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Debug - Présence</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: monospace; background: #1e1e1e; color: #d4d4d4; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { color: #4ec9b0; margin-bottom: 20px; }
        .section { background: #252526; padding: 15px; margin: 10px 0; border-radius: 4px; border-left: 3px solid #0e639c; }
        .key { color: #9cdcfe; }
        .value { color: #ce9178; }
        .error { color: #f48771; }
        .success { color: #6a9955; }
        pre { overflow-x: auto; }
        button { background: #0e639c; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }
        button:hover { background: #1177bb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Debug Panel - Présence</h1>

        <div class="section">
            <h2>Statut Serveur</h2>
            <p><span class="key">Timestamp:</span> <span class="success"><?php echo e(now()->format('Y-m-d H:i:s')); ?></span></p>
            <p><span class="key">Environment:</span> <span class="success"><?php echo e(env('APP_ENV')); ?></span></p>
            <p><span class="key">Debug Mode:</span> <span class="<?php echo e(env('APP_DEBUG') ? 'success' : 'error'); ?>"><?php echo e(env('APP_DEBUG') ? 'ON' : 'OFF'); ?></span></p>
        </div>

        <div class="section">
            <h2>Authentification</h2>
            <?php if(auth()->check()): ?>
                <p><span class="key">Utilisateur:</span> <span class="success"><?php echo e(auth()->user()->email); ?></span></p>
                <p><span class="key">Nom:</span> <span class="success"><?php echo e(auth()->user()->name); ?></span></p>
            <?php else: ?>
                <p><span class="error">Non authentifié</span></p>
            <?php endif; ?>
            <p><span class="key">Session ID:</span> <span class="value"><?php echo e(session()->getId()); ?></span></p>
        </div>

        <div class="section">
            <h2>Base de Données</h2>
            <p><span class="key">Connection:</span> <span class="success"><?php echo e(env('DB_CONNECTION')); ?></span></p>
            <p><span class="key">Host:</span> <span class="value"><?php echo e(env('DB_HOST')); ?></span></p>
            <p><span class="key">Database:</span> <span class="value"><?php echo e(env('DB_DATABASE')); ?></span></p>
        </div>

        <div class="section">
            <h2>Tests API</h2>
            <button onclick="testApi('/api/user')">Test /api/user</button>
            <button onclick="testApi('/api/employees')">Test /api/employees</button>
            <button onclick="testLogin()">Test Login</button>
            <pre id="api-result">Cliquez sur un bouton pour tester...</pre>
        </div>

        <div class="section">
            <h2>Requête Actuelle</h2>
            <p><span class="key">URL:</span> <span class="value"><?php echo e(request()->url()); ?></span></p>
            <p><span class="key">Method:</span> <span class="value"><?php echo e(request()->method()); ?></span></p>
            <p><span class="key">User Agent:</span> <span class="value"><?php echo e(request()->userAgent()); ?></span></p>
        </div>

        <script>
            async function testApi(endpoint) {
                const result = document.getElementById('api-result');
                result.textContent = `Requête à ${endpoint}...`;

                try {
                    const response = await fetch(endpoint, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await response.json();
                    result.textContent = `${endpoint}\nStatus: ${response.status}\n\n${JSON.stringify(data, null, 2)}`;
                } catch (error) {
                    result.textContent = `Erreur: ${error.message}`;
                }
            }

            async function testLogin() {
                const result = document.getElementById('api-result');
                result.textContent = 'Récupération du formulaire de login...';

                try {
                    const loginPage = await fetch('/login');
                    const html = await loginPage.text();
                    const tokenMatch = html.match(/name="_token" value="([^"]+)"/);

                    if (!tokenMatch) {
                        result.textContent = 'Token CSRF non trouvé';
                        return;
                    }

                    const token = tokenMatch[1];
                    result.textContent = `Token CSRF trouvé: ${token}\n\nTentative de connexion...`;

                    const loginResponse = await fetch('/login', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-CSRF-TOKEN': token
                        },
                        body: `email=admin@example.com&password=password&_token=${token}`
                    });

                    result.textContent = `Login Response:\nStatus: ${loginResponse.status}\nURL: ${loginResponse.url}\n\nRedirigé vers: ${loginResponse.redirected ? 'Oui' : 'Non'}`;
                } catch (error) {
                    result.textContent = `Erreur: ${error.message}`;
                }
            }
        </script>
    </div>
</body>
</html>
<?php /**PATH C:\Users\Shadow\benka\resources\views/debug.blade.php ENDPATH**/ ?>