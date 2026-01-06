<?php
$pageTitle = 'Dashboard - Profilul Meu';
require_once APP_ROOT . '/view/partials/header.php';
?>

<div 
    class="common-container max-w-[1200px] min-h-[calc(100vh-6rem)] mx-auto my-12"
>
    <div 
        class="flex justify-between items-center mb-6"
    >
        <h1>Dashboard</h1>
        <?php if (is_admin()): ?>
            <span class="bg-[#FF0C81] text-white px-3 py-1 rounded text-sm font-bold">
                ADMIN ACCOUNT
            </span>
        <?php endif; ?>
    </div>

    <!-- Feedback Messages -->
    <?php if (!empty($error) || !empty($_SESSION['flash_error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo htmlspecialchars($error ?? $_SESSION['flash_error']); ?>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <?php if (!empty($success) || !empty($_SESSION['flash_success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php echo htmlspecialchars(!empty($success) ? $success : $_SESSION['flash_success']); ?>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Profile Card -->
        <div class="md:col-span-1">
            <div class="bg-white p-6 rounded border-2 border-black">
                <h2 class="text-xl font-bold mb-4 border-b pb-2">
                    Informatii Profil
                </h2>
                <div class="space-y-3">
                    <div>
                        <span class="text-gray-500 text-sm block">
                            Utilizator
                        </span>
                        <span class="font-medium text-lg">
                            <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-500 text-sm block">
                            Rol
                        </span>
                        <span class="font-medium capitalize">
                            <?php echo htmlspecialchars($_SESSION['role']); ?>
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-500 text-sm block">
                            Data Inregistrarii
                        </span>
                        <span class="font-medium">
                            <?php echo date('d M Y', strtotime($register_date)); ?>
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-500 text-sm block">
                            User ID
                        </span>
                        <span class="font-mono bg-gray-100 px-2 py-1 rounded text-sm">
                            #<?php echo htmlspecialchars($_SESSION['user_id']); ?>
                        </span>
                    </div>
                </div>
                
                <div class="mt-6 pt-4 border-t flex flex-col gap-2">
                    <a href="change_password.php" class="block w-full text-center border-black border-2 p-2.5 bg-[#FFD43D] hover:shadow-[2px_2px_0px_rgba(0,0,0,1)] active:bg-[#00E1EF] rounded-sm transition-all duration-200 text-black font-bold">
                        Schimba Parola
                    </a>
                    <a href="logout.php" class="block w-full text-center border-black border-2 p-2.5 bg-[#FFA2A2] hover:shadow-[2px_2px_0px_rgba(0,0,0,1)] active:bg-[#F2C94C] rounded-sm transition-all duration-200 text-black font-bold">
                        Deconectare
                    </a>
                </div>
                
                <?php if (is_admin()): ?>
                    <div class="mt-4">
                        <a href="admin/products.php" class="block text-center border-black border-2 p-2.5 bg-[#A6FAFF] hover:bg-[#79F7FF] hover:shadow-[2px_2px_0px_rgba(0,0,0,1)] active:bg-[#00E1EF] rounded-sm transition-all duration-200 text-black font-bold">
                            Admin: Produse
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Orders Section -->
        <div class="md:col-span-2">
            <div class="bg-white p-6 rounded border-2 border-black">
                <h2 class="text-xl font-bold mb-4 border-b pb-2">
                    <?php echo is_admin() ? 'Administreaza comenzile' : 'Istoricul Comenzilor'; ?>
                </h2>

                <?php if (empty($orders)): ?>
                    <p class="text-gray-500">Nu exista comenzi inregistrate.</p>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-gray-600 uppercase text-xs">
                                    <th class="p-3 border-b">ID</th>
                                    <th class="p-3 border-b">Data</th>
                                    <?php if (is_admin()): ?><th class="p-3 border-b">Utilizator</th><?php endif; ?>
                                    <th class="p-3 border-b">Total</th>
                                    <th class="p-3 border-b">Status</th>
                                    <th class="p-3 border-b">Actiuni</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                <?php foreach ($orders as $order): ?>
                                    <tr class="hover:bg-gray-50 border-b last:border-0">
                                        <td class="p-3 font-mono">#<?php echo $order['id']; ?></td>
                                        <td class="p-3 text-gray-500"><?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?></td>
                                        <?php if (is_admin()): ?>
                                            <td class="p-3 font-semibold text-blue-600"><?php echo htmlspecialchars($order['username']); ?></td>
                                        <?php endif; ?>
                                        <td class="p-3 font-bold"><?php echo number_format($order['total_amount'], 2); ?> RON</td>
                                        <td class="p-3">
                                            <?php 
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'paid' => 'bg-blue-100 text-blue-800',
                                                'shipped' => 'bg-green-100 text-green-800',
                                                'cancelled' => 'bg-red-100 text-red-800'
                                            ];
                                            $colorClass = $statusColors[$order['status']] ?? 'bg-gray-100 text-gray-800';
                                            ?>
                                            <span class="px-2 py-1 rounded-full text-xs font-bold <?php echo $colorClass; ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                            <div class="text-xs text-gray-400 mt-1">
                                                <?php echo $order['item_count']; ?> produse
                                            </div>
                                        </td>
                                        <td class="p-3">
                                            <form 
                                                method="POST" 
                                                onsubmit="return confirm('Confirmi actiunea?');" 
                                                class="flex flex-col gap-1"
                                            >
                                                <input 
                                                    type="hidden" 
                                                    name="order_id" 
                                                    value="<?php echo $order['id']; ?>"
                                                >
                                                
                                                <?php if ($order['status'] === 'pending'): ?>
                                                    <!-- Cancel Action: Available to Admin or Owner -->
                                                    <button 
                                                        type="submit" 
                                                        name="action" 
                                                        value="cancel" 
                                                        class="block text-center bg-red-300 hover:bg-red-400 text-black text-xs font-bold border-black border-1 p-1 rounded-sm transition-colors duration-200"
                                                    >
                                                        Anuleaza
                                                    </button>
                                                <?php endif; ?>

                                                <?php if (is_admin() && $order['status'] !== 'cancelled' && $order['status'] !== 'shipped'): ?>
                                                    <button
                                                        type="submit" 
                                                        name="action" 
                                                        value="ship" 
                                                        class="block text-center bg-green-300 hover:bg-green-400 text-black text-xs font-bold border-black border-1 p-1 rounded-sm transition-colors duration-200"
                                                    >
                                                        Marcheaza Livrat
                                                    </button>
                                                <?php endif; ?>
                                            </form>
                                            
                                            <!-- Invoice Download -->
                                            <a 
                                                href="invoice.php?order_id=<?php echo $order['id']; ?>" 
                                                class="block text-center mt-1 bg-blue-100 text-blue-800 text-xs font-bold border-blue-300 border p-1 rounded-sm hover:bg-blue-200 transition-colors duration-200"
                                            >
                                                Descarca Factura
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/view/partials/footer.php'; ?>