<!DOCTYPE html>
<html lang="fr" data-theme="benka">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Presence">

    <link rel="apple-touch-icon" sizes="180x180" href="/icons/icon-180x180.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2563eb">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <title><?php echo $__env->yieldContent('title', 'Presence Chantier'); ?></title>

    <link rel="stylesheet" href="<?php echo e(\App\Helpers\AssetHelper::asset('resources/css/app.css')); ?>">
    <script type="module" src="<?php echo e(\App\Helpers\AssetHelper::asset('resources/js/app.js')); ?>"></script>

    <style>
        * { -webkit-tap-highlight-color: transparent; }
        html, body { -webkit-user-select: none; user-select: none; }
        input, textarea, select { -webkit-user-select: text; user-select: text; }
    </style>
</head>
<body class="bg-base-200 min-h-screen" data-page="<?php echo $__env->yieldContent('page-name'); ?>">
    <?php echo $__env->yieldContent('content'); ?>
</body>
</html>
<?php /**PATH C:\Users\Shadow\benka\resources\views/layouts/guest.blade.php ENDPATH**/ ?>