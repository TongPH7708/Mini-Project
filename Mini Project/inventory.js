let products = [];

fetch("inventory.php?format=json")
.then(response => response.json())
.then(data => {
    products = data;
    console.log(products);
})

let search = document.getElementById('searchBar');

search.addEventListener("input", function(){
    let searchValue = search.value;
    let filteredProducts = products.filter(function(product) {
        return product.name.toLowerCase().includes(searchValue.toLowerCase());
    });
    console.log(filteredProducts)

    let productsGrid = document.querySelector('.productsGrid');
    productsGrid.innerHTML = "";
    filteredProducts.forEach(function(product) {
        let div = document.createElement("div");
        div.className = "productRow";

        div.innerHTML = `
            <div><p>${product.id}</p></div>
            <div><p>${product.name}</p></div>
            <div><p>${product.quantity}</p></div>
            <div><p>${product.category_name}</p></div>
            <div><p>${product.supplier_name}</p></div>
            <div><p>${product.storage_name}</p></div>
            <div><p>${product.selling_price}</p></div>
            <div>
            <form method="POST">
                <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">
                <input type="number" name="number" value="1" min="1" step="1" placeholder="Add/Remove Amount">
                <button type="submit" name="action" value="plus">+</button>
                <button type="submit" name="action" value="minus">-</button>
            </form>
            </div>
        `;

        productsGrid.appendChild(div);
    });

    if (searchValue === ''){

    }
})
