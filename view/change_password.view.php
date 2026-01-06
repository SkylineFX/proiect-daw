<?php
$pageTitle = 'Schimba Parola';
require_once APP_ROOT . '/view/partials/header.php';
?>

<div class="common-container max-w-[600px] mx-auto my-12 min-h-[calc(100vh-6rem)]">
    <div class="bg-white p-8 rounded-md border-2 border-black">
        <h1 class="text-2xl font-bold mb-6">Schimba Parola</h1>

        <!-- Feedback Messages -->
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <?php csrf_field(); ?>
            
            <div class="mb-4">
                <label for="old_password" class="block text-gray-700 font-bold mb-2">Parola Veche</label>
                <input type="password" id="old_password" name="old_password" required 
                       class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:border-blue-500">
            </div>

            <div class="mb-4">
                <label for="new_password" class="block text-gray-700 font-bold mb-2">Noua Parola</label>
                <input type="password" id="new_password" name="new_password" required 
                       class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:border-blue-500">
            </div>

            <div class="mb-6">
                <label for="confirm_password" class="block text-gray-700 font-bold mb-2">Confirma Noua Parola</label>
                <input type="password" id="confirm_password" name="confirm_password" required 
                       class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:border-blue-500">
            </div>

            <div class="flex items-center justify-between">
                <a href="dashboard.php" class="text-black font-bold hover:underline">Inapoi la Profil</a>
                <button type="submit" 
                        class="bg-[#A6FAFF] hover:bg-[#79F7FF] text-black font-bold py-2 px-6 border-2 border-black rounded shadow-[2px_2px_0px_rgba(0,0,0,1)] active:shadow-none active:translate-x-[2px] active:translate-y-[2px] transition-all">
                    Schimba Parola
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once APP_ROOT . '/view/partials/footer.php'; ?>
