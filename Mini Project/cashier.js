let cashierFill = document.getElementById("item-identifier");

cashierFill.addEventListener("keydown", function(insert) {
    if (insert.code === "Enter") {

        let productId = cashierFill.value;

        fetch("cashier.php?id=" + productId)
            .then(response => response.json())
            .then(product => {

                if (product.error) {
                    alert(product.error);
                    return;
                }

                document.getElementById("item-name").value = product.name;
                document.getElementById("item-price").value = product.selling_price;
                document.getElementById("item-quantity").value = product.quantity;

            })
            .catch(error => {
                console.error("Error:", error);
            });
    }
});