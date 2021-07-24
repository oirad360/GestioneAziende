//sideNav è la barra di ricerca laterale, è presente in ogni pagina, permette di cercare prodotti e utenti, aggiungere/rimuovere prodotti dalla wishlist e aggiungere al carrello
//dunque a seconda della pagina in cui mi trovo, fare queste operazioni potrebbe richiedere di aggiornare anche i contenuti della pagina che ho di sfondo
function mostraSideNav(){
    const sideNav=document.querySelector("#sideNav")
    if(sideNav.classList.contains("hidden")){
        sideNav.classList.remove("hidden")
    } else {
        containerProdotti.innerHTML=""
        containerUtenti.innerHTML=""
        inputUtenti.value=""
        inputProdotti.value=""
        sideNav.classList.add("hidden")
    }
    
}

function nascondiSideNav(){
    const sideNav=document.querySelector("#sideNav")
    containerProdotti.innerHTML=""
    containerUtenti.innerHTML=""
    inputUtenti.value=""
    inputProdotti.value=""
    sideNav.classList.add("hidden")
}

function onResponse(response){
    return response.json()
}

function onJsonProdottiSide(json){//stampo tutti i prodotti trovati dalla ricerca nel db
    containerProdotti.innerHTML=""
    if(json.length>0){
        for(item of json){
            const blocco=document.createElement('div')
            blocco.classList.add("bloccoSide")
            const titolo=document.createElement('h1')
            titolo.innerText=item.titolo
            blocco.appendChild(titolo)
            const img=document.createElement('img')
            img.src=app_url+"/assets/"+item.immagine
            const link=document.createElement('a')
            link.href=app_url+"/recensioni/"+item.titolo
            link.appendChild(img)
            blocco.appendChild(link)
            const prezzo=document.createElement('p')
            prezzo.innerText=item.prezzo+"€"
            blocco.appendChild(prezzo)
            const tipo=document.querySelector('section').dataset.tipo
            if(item.wishlist==0 || item.wishlist==null){
                const bottoneWishlist=document.createElement('div')
                bottoneWishlist.classList.add('bottoneWishlist')
                bottoneWishlist.addEventListener('click',aggiungiWishlistSide)
                if(tipo==="prodotti") bottoneWishlist.addEventListener('click', ricercaSuggerimenti)
                blocco.appendChild(bottoneWishlist)
            } else {
                const bottoneRimuoviWishlist=document.createElement('div')
                bottoneRimuoviWishlist.classList.add('bottoneRimuoviWishlist')
                bottoneRimuoviWishlist.addEventListener('click',rimuoviWishlistSide)
                if(tipo==="prodotti") bottoneRimuoviWishlist.addEventListener('click', cancellaSuggerimenti)
                blocco.appendChild(bottoneRimuoviWishlist)
            }
            let flag=false
            const bottoneCarrello=document.createElement('p')
            if(item.disponibilita==0){
                blocco.classList.add('nonDisponibileSide')
                bottoneCarrello.innerText="Non disponibile"
                flag=true
            }
            if(item.inArrivo==1){
                blocco.classList.add('inArrivoSide')
                bottoneCarrello.innerText="In arrivo"
                flag=true
            }
            if(!flag){
                bottoneCarrello.innerText="Aggiungi al carrello"
                bottoneCarrello.classList.add("aggiungiCarrello")
                bottoneCarrello.addEventListener('click',aggiungiCarrelloSide)
            }
            blocco.appendChild(bottoneCarrello)
            containerProdotti.appendChild(blocco)
        }
    } else {
        const p=document.createElement('p')
        p.innerText="Nessun risultato"
        containerProdotti.appendChild(p)
    }
}

function onJsonUtentiSide(json){//stampo tutti gli utenti che ho trovato dalla ricerca nel database
    containerUtenti.innerHTML=""
    console.log(json)
    if(json.length>0){
        for(item of json){
            const profile=document.createElement('div')
            profile.classList.add("user")
            const propic=document.createElement('div')
            propic.classList.add("propic")
            if(item.propic==="defaultAvatar.jpg"){
                propic.style="background-image: url("+app_url+"/assets/defaultAvatar.jpg);"
            } else {
                propic.style="background-image: url("+app_url+"/uploads/"+item.propic+");"
            }
            profile.appendChild(propic)
            propic.addEventListener('click',onProPicClick)
            const blocco=document.createElement('div')
            const user=document.createElement('p')
            user.innerText=item.username
            blocco.appendChild(user)
            if(item.impiego){
            const impiego=document.createElement('p')
            impiego.innerText=item.impiego
            blocco.appendChild(impiego)
            }
            const link=document.createElement('a')
            link.href=app_url+"/profiloEsterno/"+item.username
            link.appendChild(blocco)
            profile.appendChild(link)
            containerUtenti.appendChild(profile)
        }
    } else {
        const p=document.createElement('p')
        p.innerText="Nessun risultato"
        containerUtenti.appendChild(p)
    }
}

function onResponseAggiornaSide(response){
    if(response.ok){//aggiorno i prodotti nella sideNav dopo aver aggiunto/rimosso prodotti dalla wishlist
        const datiPostSide={method:'POST', body: new FormData(document.forms["formSide"])}
        fetch(app_url+"/fetchProdottiSide/"+inputProdotti.value,datiPostSide).then(onResponse).then(onJsonProdottiSide)
        const tipo=document.querySelector('section').dataset.tipo//nella section è presente un dataset per specificare la pagina in cui mi trovo
        switch (tipo){//a seconda della pagina in cui mi trovo dovrò anche ricaricare i contenuti di essa
            case "prodotti":
                //se mi trovo nella pagina "prodotti" dovrò ricaricarne il contenuto chiamando la sua fetch
                 //azienda è già definita in prodotti.js
                fetch(app_url+"/prodotti/fetchProdotti/"+azienda,datiPost).then(onResponse).then(onJsonProdotti)
            break
            case "profilo"://stesso discorso se mi trovo nella pagina del profilo o nella pagina delle recensioni, si tratta sempre di aggiornare i nuovi elementi in wishlist
                fetch(app_url+"/profilo/fetchWishlist").then(onResponse).then(onJsonWishlist)
            break
            case "recensioni":
                const titolo = document.querySelector("section .containerProdotti .blocco h1").innerText
                fetch(app_url+"/recensioni/fetchProdottoRecensioni/"+titolo).then(onResponse).then(onJsonProdotto)
            break
            default: 
            break
        }
    }
}
function impostaFiltri(){
    datiPost={method: 'POST', body: new FormData(form)}
}
function rimuoviWishlistSide(event){
    const titolo=event.currentTarget.parentNode.childNodes[0].innerText
    fetch(app_url+"/rimuoviWishlist/"+titolo).then(onResponseAggiornaSide)
}

function aggiungiWishlistSide(event){
    const titolo=event.currentTarget.parentNode.childNodes[0].innerText
    fetch(app_url+"/aggiungiWishlist/"+titolo).then(onResponseAggiornaSide)
}

function onResponseCarrello(response){
    const tipo=document.querySelector('section').dataset.tipo
    if(response.ok && tipo==="carrello"){//se mi trovo nella pagina "carrello", devo ricaricare anche il suo contenuto chiamando la fetch che aggiorna quella pagina
        fetch(app_url+"/carrello/fetchCarrello").then(onResponse).then(onJsonCarrello)
    }
    return response.text()
}

function onTextCarrello(text){//restituisce il numero di prodotti nel carrello, dunque aggiungo il contatore
    const countCarrello=document.querySelector("#countCarrello")
    countCarrello.innerText=text
}

function aggiungiCarrelloSide(event){
    const titolo=event.currentTarget.parentNode.childNodes[0].innerText
    fetch(app_url+"/aggiungiCarrello/"+titolo).then(onResponseCarrello).then(onTextCarrello)
}

function cercaProdotti(){
    if(inputProdotti.value!==""){
        const datiPostSide={method:'POST', body: new FormData(document.forms["formSide"])}
        fetch(app_url+"/fetchProdottiSide/"+inputProdotti.value,datiPostSide).then(onResponse).then(onJsonProdottiSide)
    } else containerProdotti.innerHTML=""
}
function cercaUtenti(event){
    if(event.currentTarget.value!=="")
    fetch(app_url+"/fetchUtenti/"+event.currentTarget.value).then(onResponse).then(onJsonUtentiSide)
    else containerUtenti.innerHTML=""
}
function mostraProdotti(){
    const prodottiSide=document.querySelector("#prodottiSide")
    if(prodottiSide.classList.contains("hidden")){
        prodottiSide.classList.remove("hidden")
        bottoneMostraProdotti.classList.add("capovolgi")
    } else {
        prodottiSide.classList.add("hidden")
        bottoneMostraProdotti.classList.remove("capovolgi")
    }
}

function mostraUtenti(){
    const utentiSide=document.querySelector("#utentiSide")
    if(utentiSide.classList.contains("hidden")){
        utentiSide.classList.remove("hidden")
        bottoneMostraUtenti.classList.add("capovolgi")
    } else {
        utentiSide.classList.add("hidden")
        bottoneMostraUtenti.classList.remove("capovolgi")
    }
}


containerProdotti=document.querySelector("#prodottiSide .containerProdotti")
containerUtenti=document.querySelector("#utentiSide .containerProdotti")

const searchButton=document.querySelector("#searchButton")
searchButton.addEventListener('click',mostraSideNav)

const close=document.querySelector("#close")
close.addEventListener('click', mostraSideNav)

const inputProdotti=document.querySelector("#prodottiSide input")
inputProdotti.addEventListener('keyup',cercaProdotti)

document.forms['formSide'].categoria.addEventListener('change',cercaProdotti)
document.forms['formSide'].ordine.addEventListener('change',cercaProdotti)

const inputUtenti=document.querySelector("#utentiSide input")
inputUtenti.addEventListener('keyup',cercaUtenti)

const bottoneMostraProdotti=document.querySelector("#mostraProdotti")
bottoneMostraProdotti.addEventListener('click', mostraProdotti)

const bottoneMostraUtenti=document.querySelector("#mostraUtenti")
bottoneMostraUtenti.addEventListener('click', mostraUtenti)

let datiPost;
if(document.querySelector('section').dataset.tipo==="prodotti"){
    const form=document.forms['form']
    form.addEventListener('submit',impostaFiltri);
    datiPost={method: 'POST', body: new FormData(form)}
}

