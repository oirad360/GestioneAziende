function onResponse(response){
    return response.json()
}

function onJsonCarrello(json){
    const main=document.querySelector('main')
    main.innerHTML=""
    const countCarrello=document.querySelector('#countCarrello')
    let num=0
    let spesa=0
    const totale=document.querySelector("#totale")
    if(json.length>0){
        ordina.classList.remove("hidden")
        for(item of json){
            num+=parseInt(item.quantita)
            const blocco=document.createElement('div')
            blocco.classList.add('blocco')
            const titolo=document.createElement('h1')
            titolo.innerText=item.titolo
            blocco.appendChild(titolo)
            const img=document.createElement('img')
            img.src="assets/"+item.immagine
            const link=document.createElement('a')
            link.href="recensioni.php?titolo="+item.titolo
            link.appendChild(img)
            blocco.appendChild(link)
            const prezzo=document.createElement('p')
            prezzo.innerText=item.prezzo+"€"
            blocco.appendChild(prezzo)
            const bottoni=document.createElement('div')
            bottoni.classList.add("containerBottoni")
            const diminuisci=document.createElement('button')
            diminuisci.innerText="-"
            diminuisci.classList.add("diminuisci")
            diminuisci.addEventListener('click',rimuoviCarrello)
            bottoni.appendChild(diminuisci)
            const aumenta=document.createElement('button')
            aumenta.innerText="+"
            aumenta.classList.add("aumenta")
            aumenta.addEventListener('click',aggiungiCarrello)
            bottoni.appendChild(aumenta)
            blocco.appendChild(bottoni)
            const quantità=document.createElement('p')
            quantità.innerText=item.quantita
            blocco.appendChild(quantità)
            main.appendChild(blocco)
            spesa+=item.prezzo*item.quantita
        }
        totale.innerText="Totale: "+spesa+"€"
    }else{
        totale.innerText=""
        ordina.classList.add("hidden")
        const p=document.createElement('p')
        p.innerText="Non hai prodotti nel carrello."
        main.appendChild(p)
    }
    countCarrello.innerText=num
}


function onResponseAggiorna(response){
    if(response.ok){
        fetch(app_url+"/carrello/fetchCarrello").then(onResponse).then(onJsonCarrello)
    }
}
function aggiungiCarrello(event){
    const blocco=event.currentTarget.parentNode.parentNode
    const titolo=blocco.childNodes[0].innerText
    fetch(app_url+"/aggiungiCarrello/"+titolo).then(onResponseAggiorna)
}

function rimuoviCarrello(event){
    const blocco=event.currentTarget.parentNode.parentNode
    const titolo=blocco.childNodes[0].innerText
    fetch(app_url+"/carrello/rimuoviCarrello/"+titolo).then(onResponseAggiorna)

}
function onResponseOrdine(response){
    if(response.ok){
        window.location.replace(app_url+"/profilo")
    }
}

function effettuaOrdine(){
    fetch(app_url+"/carrello/ordina").then(onResponseOrdine)
}


fetch(app_url+"/carrello/fetchCarrello").then(onResponse).then(onJsonCarrello)
const ordina=document.querySelector("#ordina")
ordina.addEventListener('click', effettuaOrdine)