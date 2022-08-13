let request = new XMLHttpRequest();

window.onload = function(){
    "use strict";
    window.setInterval(requestData, 2000);
}

function requestData() { // fordert die Daten asynchron an
    "use strict";
    request.open("GET", "KundenStatus.php"); // URL für HTTP-GET
    request.onreadystatechange = processData; //Callback-Handler zuordnen
    request.send(null); // Request abschicken
}

function processData() {
    "use strict";
    if(request.readyState == 4) { // Uebertragung = DONE
        if (request.status == 200) { // HTTP-Status = OK
            if(request.responseText != null)
                process(request.responseText);// Daten verarbeiten
            else console.error ("Dokument ist leer");
        }
        else console.error ("Uebertragung fehlgeschlagen");
    } else ; // Uebertragung laeuft noch
}

function process(customerOrder){
    "use strict";
    let orderStatus = document.getElementById("orderStatus");
    if(customerOrder === "")
        orderStatus.appendChild(document.createTextNode("no ordered pizzas received"));
    else {
        while (orderStatus.firstChild)
            orderStatus.removeChild(orderStatus.firstChild);
        const order = JSON.parse(customerOrder);
        let status = order.statuses;
        let names = order.names;
        let IDs = Object.keys(status);

        IDs.forEach(ID => {
            orderStatus.appendChild(createArticleInfo(ID, status[ID], names[ID]));
        })
    }
}


function createArticleInfo(ID, status, name){
    "use strict";
    let article = document.createElement("article");
    let headline = document.createElement("h3");
    let nameText = document.createTextNode(name);
    headline.appendChild(nameText);
    let radioOrdered = createRadio(ID, status, 0, "ordered_");
    let labelOrdered = createLabel(radioOrdered, "Bestellt");
    let radioOven = createRadio(ID, status, 1, "oven_");
    let labelOven = createLabel(radioOven, "Im Ofen");
    let radioDone = createRadio(ID, status, 2, "done_");
    let labelDone = createLabel(radioDone, "Fertig");
    let radioWay = createRadio(ID, status, 3, "way_");
    let labelWay = createLabel(radioWay, "Auf dem Weg");
    article.appendChild(headline);
    article.appendChild(radioOrdered);
    article.appendChild(labelOrdered);
    article.appendChild(document.createElement("br"));
    article.appendChild(radioOven);
    article.appendChild(labelOven);
    article.appendChild(document.createElement("br"));
    article.appendChild(radioDone);
    article.appendChild(labelDone);
    article.appendChild(document.createElement("br"));
    article.appendChild(radioWay);
    article.appendChild(labelWay);
    article.appendChild(document.createElement("br"));
    return article;
}

function createRadio(ID, status, requiredStatus, idString) {
    "use strict";
    let input = document.createElement("input");
    input.type = "radio";
    input.id = idString + ID;
    input.name = "checkOrder_" + ID;
    input.disabled = true;
    if(Number(status) === requiredStatus)
        input.checked = true;
    return input;
}

function createLabel(radio, text){
    "use strict";
    let label = document.createElement("label");
    label.htmlFor = radio.id;
    let textNode = document.createTextNode(text);
    label.appendChild(textNode);
    return label;
}