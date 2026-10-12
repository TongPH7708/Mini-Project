let itemIdentifier = document.getElementById("item-identifier");
let itemName= document.getElementById("item-name");
let itemPrice = document.getElementById("item-price");
let itemQuantity = document.getElementById("item-quantity");
let soldItems = [];

itemIdentifier.addEventListener("keydown", function(insert) {

    if (insert.code === "Enter") {

        let productId = itemIdentifier.value;

        if (!productId) {
            return;
        }

        fetch(`cashier.php?id=${productId}`)
            .then(response => response.json())
            .then(data => {

                if (data.success) {

                    itemName.value =
                        data.product.name;

                    itemPrice.value =
                        data.product.selling_price;

                    console.log(data.product);

                } else {

                    alert(data.message);

                    itemName.value = "";
                    itemPrice.value = "";

                }

            })
            
            .catch(error => {
                console.error("Error:", error);
            });

            itemQuantity.focus()
    }
});

itemQuantity.addEventListener("keydown", function(insert) {

    if (insert.code === "Enter") {

        if(itemQuantity.value === ""){
            alert("Please insert item quantity");
            return;
        }else{

            function calculateAmount(quantity, price){
                return quantity * price;
            }

            let quantity= Number(itemQuantity.value);
            let price = Number(itemPrice.value);
            let amount = calculateAmount(quantity, price);

            soldItems.push({
            id: Number(itemIdentifier.value),
            quantity: quantity
            });


            let list =document.getElementById("cashierList")
            list.insertAdjacentHTML( "beforeend",
            `<div class="productsRow">`+
            `<div class="cashierID"><p>${itemIdentifier.value}</p></div>`+
            `<div class="cashierName"><p>${itemName.value}</p></div>` +
            `<div class="cashierQty"><p>${quantity}</p></div>` +
            `<div class="cashierPrice"><p>RM${price}</p></div>`+
            `<div class="cashierAmount"><p>RM${amount}</div>`+
            `</div>`)

            calculateGrandTotal();

            itemIdentifier.value = "";
            itemName.value = "";
            itemPrice.value = "";
            itemQuantity.value = "";

            itemIdentifier.focus();
        }

    }
})


let grandTotal = document.getElementById("grandTotal");

function calculateGrandTotal() {
    let amounts = document.querySelectorAll(".cashierAmount");
    let total = 0;

    amounts.forEach(function(item) {
        let amount = Number(item.textContent.replace("RM", "").trim());
        total += amount;
    });

    grandTotal.innerHTML =
    `<p>RM` + total.toFixed(2) + `</p>`
}


let endTransaction = document.getElementById("endTransaction");

endTransaction.addEventListener("click", async function () {
    

    const response = await fetch("cashier.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                items: soldItems
            })
        });
    
    document.getElementById("cashierList").innerHTML = "";

    
    grandTotal.innerHTML = "<p>RM0.00</p>";

    
    itemIdentifier.value = "";
    itemName.value = "";
    itemPrice.value = "";
    itemQuantity.value = "";

    
    itemIdentifier.focus();

});

let refresh= document.getElementById("refresh")

refresh.addEventListener("click", function () {
    
    
    document.getElementById("cashierList").innerHTML = "";

    
    grandTotal.innerHTML = "<p>RM0.00</p>";

    
    itemIdentifier.value = "";
    itemName.value = "";
    itemPrice.value = "";
    itemQuantity.value = "";

    
    itemIdentifier.focus();

});
