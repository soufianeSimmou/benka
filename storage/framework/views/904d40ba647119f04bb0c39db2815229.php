<?php $__env->startSection('title', 'Connexion - Presence Chantier'); ?>
<?php $__env->startSection('page-name', 'login'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .bg-pattern {
        background-color: hsl(var(--b2));
        background-image:
            url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%233b82f6' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E"),
            url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%2359b5f9' fill-opacity='0.03' fill-rule='evenodd'/%3E%3C/svg%3E");
    }
</style>
<div class="min-h-screen bg-pattern flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center mb-4">
                <img src="/logobenka.png" alt="Benka Logo" class="w-24 h-24 object-contain">
            </div>
            <h1 class="text-2xl font-bold text-base-content">Presence</h1>
            <p class="text-base-content/60 text-sm mt-1">Suivi de presence du chantier</p>
        </div>

        <!-- Tabs -->
        <div role="tablist" class="tabs tabs-boxed bg-base-300 p-1 mb-6">
            <button type="button" id="tab-login" onclick="switchTab('login')" class="tab tab-active flex-1">Connexion</button>
            <button type="button" id="tab-register" onclick="switchTab('register')" class="tab flex-1">Inscription</button>
        </div>

        <!-- Card -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <?php if(config('services.google.client_id')): ?>
                    <!-- Google Button -->
                    <a href="<?php echo e(route('auth.google')); ?>" class="btn btn-outline w-full gap-2">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Continuer avec Google
                    </a>

                    <?php $__errorArgs = ['google'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="alert alert-error mt-3">
                            <span class="text-sm"><?php echo e($message); ?></span>
                        </div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    <!-- Divider -->
                    <div class="divider text-base-content/40 text-sm">ou</div>
                <?php endif; ?>

                <!-- Login Form -->
                <form method="POST" action="<?php echo e(route('login')); ?>" id="login-form" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <div class="form-control">
                        <label class="label" for="login-email">
                            <span class="label-text">Email</span>
                        </label>
                        <input type="email" id="login-email" name="email" value="<?php echo e(old('email')); ?>" required autocomplete="email" placeholder="votre@email.com" class="input input-bordered w-full">
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <label class="label"><span class="label-text-alt text-error"><?php echo e($message); ?></span></label>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="form-control">
                        <label class="label" for="login-password">
                            <span class="label-text">Mot de passe</span>
                        </label>
                        <input type="password" id="login-password" name="password" required autocomplete="current-password" placeholder="Votre mot de passe" class="input input-bordered w-full">
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <label class="label"><span class="label-text-alt text-error"><?php echo e($message); ?></span></label>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="remember" class="checkbox checkbox-sm border-blue-600 [--chkbg:theme(colors.blue.600)] [--chkfg:white]">
                            <span class="label-text">Se souvenir de moi</span>
                        </label>
                    </div>

                    <button type="submit" class="btn bg-blue-600 hover:bg-blue-700 text-white border-0 w-full">Se connecter</button>
                </form>

                <!-- Register Form -->
                <form method="POST" action="<?php echo e(route('register')); ?>" id="register-form" class="space-y-4 hidden">
                    <?php echo csrf_field(); ?>
                    <div class="form-control">
                        <label class="label" for="register-name">
                            <span class="label-text">Nom complet</span>
                        </label>
                        <input type="text" id="register-name" name="name" value="<?php echo e(old('name')); ?>" required autocomplete="name" placeholder="Jean Dupont" class="input input-bordered w-full">
                    </div>

                    <div class="form-control">
                        <label class="label" for="register-email">
                            <span class="label-text">Email</span>
                        </label>
                        <input type="email" id="register-email" name="email" value="<?php echo e(old('email')); ?>" required autocomplete="email" placeholder="votre@email.com" class="input input-bordered w-full">
                    </div>

                    <div class="form-control">
                        <label class="label" for="register-password">
                            <span class="label-text">Mot de passe</span>
                        </label>
                        <input type="password" id="register-password" name="password" required autocomplete="new-password" placeholder="Minimum 8 caracteres" class="input input-bordered w-full">
                    </div>

                    <div class="form-control">
                        <label class="label" for="register-password-confirm">
                            <span class="label-text">Confirmer le mot de passe</span>
                        </label>
                        <input type="password" id="register-password-confirm" name="password_confirmation" required autocomplete="new-password" placeholder="Confirmez votre mot de passe" class="input input-bordered w-full">
                    </div>

                    <button type="submit" class="btn bg-blue-600 hover:bg-blue-700 text-white border-0 w-full">Creer mon compte</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function switchTab(tab) {
        const loginForm = document.getElementById('login-form');
        const registerForm = document.getElementById('register-form');
        const tabLogin = document.getElementById('tab-login');
        const tabRegister = document.getElementById('tab-register');

        if (tab === 'login') {
            loginForm.classList.remove('hidden');
            registerForm.classList.add('hidden');
            tabLogin.classList.add('tab-active');
            tabRegister.classList.remove('tab-active');
        } else {
            loginForm.classList.add('hidden');
            registerForm.classList.remove('hidden');
            tabRegister.classList.add('tab-active');
            tabLogin.classList.remove('tab-active');
        }
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Shadow\benka\resources\views/auth/login.blade.php ENDPATH**/ ?>