document.addEventListener('DOMContentLoaded', () => {
    updateCartUI(); // Fetch initial count

    // Attach listener to all "Add to Cart" buttons
    const addButtons = document.querySelectorAll('.btn-add');
    addButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const productId = btn.getAttribute('data-id');
            if (productId) {
                addToCart(productId);
            }
        });
    });
});

async function addToCart(productId, quantity = 1) {
    try {
        const response = await fetch(CART_API_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'add',
                product_id: productId,
                quantity: quantity
            })
        });

        const data = await response.json();
        if (data.success) {
            alert('Produs adaugat in cos!');
            updateCartCount(data.count);
        } else {
            console.error('Cart Error:', data.message);
        }
    } catch (error) {
        console.error('Network Error:', error);
    }
}


function updateCartCount(count) {
    const cartBadge = document.getElementById('cart-count');
    if (cartBadge) {
        cartBadge.innerText = count > 0 ? `(${count})` : '';
    }
}
