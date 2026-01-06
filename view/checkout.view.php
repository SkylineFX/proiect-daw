<?php
$pageTitle = 'Finalizare Comanda';
require_once APP_ROOT . '/view/partials/header.php';
?>

<div class="common-container max-w-[800px] mx-auto my-12">
    <h1>Finalizare Comanda</h1>

    <?php if (isset($error)): ?>
        <p style="color: red; margin-bottom: 1rem;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST" action="checkout.php">
        <?php csrf_field(); ?>
        
        <section class="mb-8 p-6 bg-white border border-gray-200 rounded shadow-sm">
            <h2 class="text-xl font-bold mb-4">1. Adresa de Livrare</h2>
            
            <?php if (!empty($savedAddresses)): ?>
                <div class="mb-4">
                    <p class="font-semibold mb-2">Adrese Salvate:</p>
                    <?php foreach ($savedAddresses as $addr): ?>
                        <div class="flex items-center mb-2">
                            <input type="radio" name="address_selection" value="<?php echo $addr['id']; ?>" id="addr_<?php echo $addr['id']; ?>" class="mr-2" onchange="toggleNewAddress(false)">
                            <label for="addr_<?php echo $addr['id']; ?>">
                                <?php echo htmlspecialchars($addr['city'] . ', ' . $addr['address_line'] . ' (' . $addr['postal_code'] . ')'); ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="flex items-center mb-4">
                    <input type="radio" name="address_selection" value="new" id="addr_new" class="mr-2" checked onchange="toggleNewAddress(true)">
                    <label for="addr_new" class="font-semibold text-blue-600">Adauga o adresa noua</label>
                </div>
            <?php else: ?>
                <input type="hidden" name="address_selection" value="new">
            <?php endif; ?>

            <div id="new-address-form" class="<?php echo !empty($savedAddresses) ? '' : 'block'; ?>">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="input-group">
                        <label for="city">Oras:</label>
                        <input type="text" id="city" name="city" class="w-full p-2 border rounded">
                    </div>
                    <div class="input-group">
                        <label for="postal_code">Cod Postal:</label>
                        <input type="text" id="postal_code" name="postal_code" class="w-full p-2 border rounded">
                    </div>
                </div>
                <div class="input-group mt-4">
                    <label for="address_line">Adresa (Strada, Numar, Bloc, etc.):</label>
                    <textarea id="address_line" name="address_line" rows="2" class="w-full p-2 border rounded"></textarea>
                </div>
                
                <div class="mt-4 flex items-center">
                    <input type="checkbox" id="save_address" name="save_address" value="1" class="mr-2">
                    <label for="save_address">Salveaza aceasta adresa pentru viitor</label>
                </div>
            </div>
        </section>

        <section class="mb-8 p-6 bg-gray-50 border border-gray-200 rounded">
            <h2 
                class="text-xl font-bold mb-4"
            >
                2. Sumar Comanda
            </h2>
            <div class="flex justify-between items-center text-lg font-bold">
                <span>Total de Plata:</span>
                <span><?php echo number_format($cartTotal, 2); ?> RON</span>
            </div>
            <p 
                class="text-sm text-gray-500 mt-2"
            >
                Plata se va face ramburs la curier.
            </p>
        </section>

        <button 
            type="submit" 
            class="w-full h-12 border-black border-2 p-2.5 bg-[#A6FAFF] hover:bg-[#79F7FF] hover:shadow-[2px_2px_0px_rgba(0,0,0,1)] active:bg-[#00E1EF] rounded-sm transition-all duration-200"
        >
            Plasare Comanda
        </button>
    </form>
</div>

<script>
function toggleNewAddress(show) {
    const form = document.getElementById('new-address-form');
    
    const inputs = form.querySelectorAll('input:not([type="checkbox"]), textarea');
    inputs.forEach(input => {
        if (!show) {
            input.removeAttribute('required');
            input.disabled = true;
            form.style.opacity = '0.5';
        } else {
            input.setAttribute('required', 'required');
            input.disabled = false;
            form.style.opacity = '1';
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const newAddrRadio = document.getElementById('addr_new');
    if (newAddrRadio && newAddrRadio.checked) {
        toggleNewAddress(true);
    } else if (document.querySelector('input[name="address_selection"]:checked')) {
         toggleNewAddress(false);
    } else {
        // Fallback if nothing checked (e.g. no saved addresses)
        toggleNewAddress(true);
    }
});
</script>

<?php require_once APP_ROOT . '/view/partials/footer.php'; ?>
