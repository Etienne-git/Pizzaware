function addToCart(pizza){
    "use strict";
    let cart = document.getElementById('cart');
    let newItem = document.createElement('option');
    newItem.value = pizza.title;
    newItem.appendChild(document.createTextNode(pizza.title));
    cart.appendChild(newItem);
    let price = document.getElementById('totalPrice');
    price.firstChild.nodeValue = (Number(price.firstChild.nodeValue) + Number(pizza.dataset.price)).toFixed(2);
    orderDisabled();
}

function deleteAll(){
    "use strict";
    let cart = document.getElementById('cart');
    for(let i = cart.length; i >= 0; i--)
        cart.remove(i);
    document.getElementById('totalPrice').firstChild.nodeValue = 0.0;
    orderDisabled();
}

function deleteSelected(){
    "use strict";
    let cart = document.getElementById('cart');
    let selected = document.getElementById('cart').selectedOptions;
    let price = document.getElementById('totalPrice');
    while(selected.length > 0) {
        let itemPrice = document.getElementById(selected[0].value).dataset.price;
        price.firstChild.nodeValue = (Number(price.firstChild.nodeValue) - Number(itemPrice)).toFixed(2);
        cart.remove(selected[0].index);
    }
    orderDisabled();
}

function orderDisabled(){
    "use strict";
    document.getElementById('orderButton').disabled = !(document.getElementById('cart').length > 0 && document.getElementById('addressInput').value !== "");
}

function selectAll(){
    "use strict";
    let cart = document.getElementById('cart');
    for(let i = 0; i < cart.length; i++){
        cart[i].selected = true;
    }
}