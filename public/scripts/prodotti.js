//--------------------------------------------------DEFINIZIONE DI FUNZIONI---------------------------------------

function onResponse(response){
    return response.json()
}

function onJsonProdotti(json){  //ogni oggetto contiene le varie info del prodotto
    const main=document.querySelector('main')
    const wishlistContainer=document.querySelector("#wishlist")
    const titoloWishlist=document.querySelector('#titoloWishlist')
    prodotti=[]
    if(json.length>0){
        main.innerHTML=""
        wishlistContainer.innerHTML=""
        for(const item of json){ //scorro gli item della lista di oggetti in questione
            if(item.mostra){
                const blocco=document.createElement('div') //creo nel DOM tutti gli elementi necessari per la composizione del 'blocco' contenente il prodotto e le sue info
                blocco.classList.add('blocco')//le info da inserire nel blocco vengono recuperare dagli item della lista, gli stili sono già presenti nel css sotto forma di classi che vengono aggiunte da qui
                const titolo=document.createElement('h1')
                titolo.innerText=item.titolo
                blocco.appendChild(titolo)
                const img=document.createElement('img')
                img.src=app_url+"/assets/"+item.immagine
                const link=document.createElement('a')
                link.href=app_url+"/recensioni/"+item.titolo
                link.appendChild(img)
                blocco.appendChild(link)
                const bottoneWishlist=document.createElement('div')
                if(item.wishlist==0 || item.wishlist==null){
                    bottoneWishlist.classList.add('bottoneWishlist')
                    bottoneWishlist.addEventListener('click', aggiungiWishlist)
                    bottoneWishlist.addEventListener('click', ricercaSuggerimenti)
                } else {
                    bottoneWishlist.classList.add('cuoreRosso')
                    titoloWishlist.classList.remove("hidden")
                }
                blocco.appendChild(bottoneWishlist)
                const prezzo=document.createElement('p')
                prezzo.innerText=item.prezzo+"€"
                blocco.appendChild(prezzo)
                const bottoneCarrello=document.createElement('p')
                blocco.appendChild(bottoneCarrello)
                const bottoneScheda=document.createElement('p')
                bottoneScheda.innerText="Scheda tecnica"
                bottoneScheda.classList.add('bottoneScheda')
                blocco.appendChild(bottoneScheda)
                bottoneScheda.addEventListener('click',mostraScheda)
                const scheda=document.createElement('p')
                scheda.innerText=item.descrizione
                scheda.classList.add('scheda','hidden')//la descrizione deve essere invisibile sin dall'inizio, poichè deve essere mostrata solo al click del pulsante apposito
                const bloccoEsterno=document.createElement('div')
                bloccoEsterno.appendChild(blocco)
                bloccoEsterno.appendChild(scheda)
                let flag=false
                if(item.disponibilita==0) { //questi if servono a dare un colore di sfondo diverso nel caso in cui il prodotto risulti non disponibile o in arrivo
                    blocco.classList.add('nonDisponibile')
                    scheda.classList.add('nonDisponibile')
                    bottoneCarrello.innerText="Non disponibile"
                    flag=true
                }
                if(item.inArrivo==1) {
                    blocco.classList.add('inArrivo')
                    scheda.classList.add('inArrivo')
                    bottoneCarrello.innerText="In arrivo"
                    flag=true
                }
                if(!flag) {//se il prodotto non è disponibile o in arrivo non può essere aggiunto al carrello
                    bottoneCarrello.innerText="Aggiungi al carrello"
                    bottoneCarrello.addEventListener('click',aggiungiCarrello)
                    bottoneCarrello.classList.add("aggiungiCarrello")
                }
                main.appendChild(bloccoEsterno) //appendo il blocco nel div Main (contiente tutti i prodotti)
                prodotti.push(bloccoEsterno) //inserisco il blocco appena creato nella lista che ho già definito (servirà per altre funzioni)
            }
            if(item.wishlist==1){//se il prodotto è in wishlist creo una copia del blocco e la appendo alla sezione wishlist
                const blocco=document.createElement('div')
                blocco.classList.add('blocco')
                const titolo=document.createElement('h1')
                titolo.innerText=item.titolo
                blocco.appendChild(titolo)
                const img=document.createElement('img')
                img.src=app_url+"/assets/"+item.immagine
                const link1=document.createElement('a')
                link1.href=app_url+"/recensioni/"+item.titolo
                link1.appendChild(img)
                blocco.appendChild(link1)
                const bottoneRimuoviWishlist=document.createElement('div') //il blocco della wishlist presenta un nuovo bottone per eliminarlo dalla wishlist
                bottoneRimuoviWishlist.classList.add('bottoneRimuoviWishlist') 
                blocco.appendChild(bottoneRimuoviWishlist)
                bottoneRimuoviWishlist.addEventListener('click',rimuoviWishlist)
                bottoneRimuoviWishlist.addEventListener('click',cancellaSuggerimenti)
                wishlistContainer.appendChild(blocco)
            }
        }
    } else {
        const h1=document.createElement('h1')
        h1.innerText="Nessun prodotto disponibile"
        main.appendChild(h1)
    }
}
function onResponseCarrello(response){
    return response.text()
}

function onTextCarrello(text){//restituisce il numero di prodotti nel carrello, dunque aggiungo il contatore
    const countCarrello=document.querySelector("#countCarrello")
    countCarrello.innerText=text
}


function aggiungiCarrello(event){
    const titolo=event.currentTarget.parentNode.childNodes[0].innerText
    fetch(app_url+"/aggiungiCarrello/"+titolo).then(onResponseCarrello).then(onTextCarrello)
}

function mostraScheda(event){
    const bottoneScheda=event.currentTarget
    const bloccoEsterno=bottoneScheda.parentNode.parentNode //tramite il currentTarget (bottone appena premuto) risalgo all'intero blocco del prodotto utilizzando parentNode e lo salvo in una costante
    const scheda=bloccoEsterno.querySelector('.scheda') //ottengo la scheda tecnica del prodotto grazie a una query nel blocco
    if(scheda.classList.contains("hidden")){
        scheda.classList.remove('hidden')
        bottoneScheda.innerText='Nascondi'
        bloccoEsterno.childNodes[0].classList.add("noBorderBottomRadius")
    } else {
        scheda.classList.add('hidden')
        bottoneScheda.innerText='Scheda tecnica'
        bloccoEsterno.childNodes[0].classList.remove("noBorderBottomRadius")
    }
}

function onResponseAggiorna(response){
    if(response.ok){
        const formData={method:'POST', body: new FormData(form)}
        fetch(app_url+"/prodotti/fetchProdotti/"+azienda,formData).then(onResponse).then(onJsonProdotti)
    }
}

function aggiungiWishlist(event){
    const titolo=event.currentTarget.parentNode.childNodes[0].innerText
    fetch(app_url+"/aggiungiWishlist/"+titolo).then(onResponseAggiorna)
}

function rimuoviWishlist(event){
    const bottoneRimuoviWishlist=event.currentTarget
    const blocco=bottoneRimuoviWishlist.parentNode
    const titolo=blocco.childNodes[0].innerText
    fetch(app_url+"/rimuoviWishlist/"+titolo).then(onResponseAggiorna)
}

function filtra(event){
    event.preventDefault()
    const formData={method:'POST', body: new FormData(form)}
    fetch(app_url+"/prodotti/fetchProdotti/"+azienda,formData).then(onResponse).then(onJsonProdotti)
}

function cerca(event){
    const searchString=event.currentTarget.value.toLowerCase(); //memorizzo la stringa inserita in una costante (trasformo tutto in lowerCase così da avere la ricerca case-insensitive)
    for(const item of prodotti){ //vado a cercare nella lista prodotti confrontando il titolo dei prodotti (in lowerCase) con la stringa cercata, se il titolo del generico item NON include la stringa cercata lo nascondo, altrimenti lo mostro
        if(!item.childNodes[0].childNodes[0].innerText.toLowerCase().includes(searchString)){
            item.classList.add('hidden')
        } else {
            item.classList.remove('hidden')
        }
    }
}
//-----------------------------------------------------------MAIN----------------------------------------------------------------
let prodotti=[] //inizializzo una lista vuota per i prodotti e un contatore inizialmente nullo per contare gli elementi in wishlist
const form=document.forms["form"]
form.addEventListener('submit', filtra)

const formData={method:'POST', body: new FormData(form)}
const azienda = document.querySelector('section').dataset.id
fetch(app_url+"/prodotti/fetchProdotti/"+azienda,formData).then(onResponse).then(onJsonProdotti)//carico tutti i prodotti dell'azienda


const search=document.querySelector('section input') //aggiungo la funzionalità alla barra di ricerca per filtrare i contenuti tramite input testuale
search.addEventListener('keyup', cerca)

