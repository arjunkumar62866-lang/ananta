$(document).ready(function() {
    $(document).on('click', '.addToCart', function() {
        var proId = $(this).data('data-proId');
        var proQty = $(this).data('data-proQty');

        $.ajax({
            url: 'ajaxAddToCart.php',
            type: 'POST',
            data: { proId: proId, proQty: proQty },
            dataType: 'json',
            success: function(response) {
                if(response.status == 'success') {
                    alert(response.message);
                    // Optional: update cart count
                    var count = parseInt($('#productCount').text()) || 0;
                    $('#productCount').text(count + 1);
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Error adding product to cart');
            }
        });
    });
});
