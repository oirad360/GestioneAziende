function onResponse(response){
    return response.json()
}
function onJsonProdotto(json){
    container.innerHTML=""
//carico il blocco del prodotto di cui sto guardando le recensioni, è uguale ai blocchi caricati in prodotti.js
        const blocco=document.createElement('div') 
            blocco.classList.add('bloccoOrizzontale')
            const bloccoInterno=document.createElement('div')
            bloccoInterno.classList.add('blocco')
            const titolo=document.createElement('h1')
            titolo.innerText=json.titolo
            bloccoInterno.appendChild(titolo)
            const img=document.createElement('img')
            img.src=app_url+"/assets/"+json.immagine
            bloccoInterno.appendChild(img)
            const bottoneWishlist=document.createElement('div')
            if(json.wishlist==0 || json.wishlist==null){
                const bottoneWishlist=document.createElement('div')
                bottoneWishlist.classList.add('bottoneWishlist')
                bottoneWishlist.addEventListener('click',aggiungiWishlist)
                bloccoInterno.appendChild(bottoneWishlist)
            } else {
                const bottoneRimuoviWishlist=document.createElement('div')
                bottoneRimuoviWishlist.classList.add('bottoneRimuoviWishlist')
                bottoneRimuoviWishlist.addEventListener('click',rimuoviWishlist)
                bloccoInterno.appendChild(bottoneRimuoviWishlist)
            }
            bloccoInterno.appendChild(bottoneWishlist)
            const prezzo=document.createElement('p')
            prezzo.innerText=json.prezzo+"€"
            bloccoInterno.appendChild(prezzo)
            const bottoneCarrello=document.createElement('p')
            bloccoInterno.appendChild(bottoneCarrello)
            const bottoneScheda=document.createElement('p')
            bottoneScheda.innerText="Scheda tecnica"
            bottoneScheda.addEventListener('click',mostraScheda)
            const bloccoScheda=document.createElement('div')
            bloccoScheda.classList.add('schedaLaterale'/*,'hidden'*/)//la descrizione deve essere invisibile sin dall'inizio, poichè deve essere mostrata solo al click del pulsante apposito
            const scheda=document.createElement('p')
            scheda.innerText=json.descrizione
            const descrizione=document.createElement('h2')
            descrizione.innerText="Scheda tecnica"
            bloccoScheda.appendChild(descrizione)
            bloccoScheda.appendChild(scheda)
            blocco.appendChild(bloccoInterno)
            blocco.appendChild(bloccoScheda)

            let flag=false
            if(json.disponibilita==0) { //questi if servono a dare un colore di sfondo diverso nel caso in cui il prodotto risulti non disponibile o in arrivo
                bloccoInterno.classList.add('nonDisponibile')
                bottoneCarrello.innerText="Non disponibile"
                flag=true
            }
            if(json.inArrivo==1) {
                bloccoInterno.classList.add('inArrivo')
                bottoneCarrello.innerText="In arrivo"
                flag=true
            }
            if(!flag) {//se il prodotto non è disponibile o in arrivo non può essere aggiunto al carrello
                bottoneCarrello.innerText="Aggiungi al carrello"
                bottoneCarrello.addEventListener('click',aggiungiCarrello)
                bottoneCarrello.classList.add("aggiungiCarrello")
            }
            container.appendChild(blocco)
}

function onJsonRecensioni(json){//carico tutte le recensioni di quel prodotto, se è presente una mia recensione elimino l'area per pubblicare una recensione (ognuno può pubblicare max 1 recensione)
    const container=document.querySelector("#recensioni")
    container.innerHTML=""
    if(json.contents.length>0){
        let somma=0
        for(item of json.contents){
            if(json.disattivaRecensione){
                if(document.querySelector("#areaRecensione") && document.querySelector("#bottoneRecensione")){
                    document.querySelector("#areaRecensione").remove()
                    document.querySelector("#bottoneRecensione").remove()
                }
            }
            const blocco=document.createElement('div')
            blocco.dataset.id=item.id
            blocco.classList.add("recensione")
            const profile=document.createElement('div')
            profile.classList.add("profile")
            const propic=document.createElement('div')
            propic.classList.add("propic")
            if(item.propic==="defaultAvatar.jpg"){
                propic.style="background-image: url("+app_url+"/assets/defaultAvatar.jpg);"
            } else {
                propic.style="background-image: url("+app_url+"/uploads/"+item.propic+");"
            }
            propic.addEventListener('click',onProPicClick)
            profile.appendChild(propic)
            const link=document.createElement('a')
            link.href=app_url+"/profiloEsterno/"+item.username
            const div=document.createElement('div')
            const user=document.createElement('p')
            user.innerText=item.username
            div.appendChild(user)
            if(item.impiego){
                const impiego=document.createElement('p')
                impiego.innerText=item.impiego
                div.appendChild(impiego)
            }
            link.appendChild(div)
            profile.appendChild(link)
            const riga=document.createElement('div')
            riga.classList.add("riga")
            riga.appendChild(profile)
            const data=document.createElement('p')
            data.innerText=item.data
            riga.appendChild(data)
            blocco.appendChild(riga)
            const voto=document.createElement('img')
            voto.classList.add("voto")
            voto.src=app_url+"/assets/"+item.voto+".png"
            blocco.appendChild(voto)
            somma+=item.voto
            const descrizione=document.createElement('p')
            descrizione.innerText=item.descrizione
            descrizione.classList.add("descrizione")
            blocco.appendChild(descrizione)
            const bloccoLike=document.createElement('div')
            bloccoLike.classList.add("bloccoLike")
            const bottoneLike=document.createElement('div')
            if(item.youLike){
                bottoneLike.classList.add('bottoneDislike')
                bottoneLike.addEventListener('click',dislike)
            } else {
                bottoneLike.classList.add('bottoneLike')
                bottoneLike.addEventListener('click',like)
            }
            bloccoLike.appendChild(bottoneLike)
            const numLike=document.createElement('span')
            if(item.numLike===1){
                numLike.innerText=item.numLike+" utente ha trovato utile questa recensione"
            } else {
                numLike.innerText=item.numLike+" utenti hanno trovato utile questa recensione"
            }
            if(item.numLike!==0){
                numLike.addEventListener('click', onLikeClick)
                numLike.classList.add("hover")
            }
            bloccoLike.appendChild(numLike)
            blocco.appendChild(bloccoLike)
            container.appendChild(blocco)

        }
        const media=somma/json.contents.length
        const votoMedio=document.querySelector('#votoMedio')
        if(json.contents.length>1){
            votoMedio.innerText="Voto medio: "+media+"/5, "+json.contents.length+" recensioni."
        } else{
            votoMedio.innerText="Voto medio: "+media+"/5, "+json.contents.length+" recensione."
        }
        document.querySelector('section').insertBefore(votoMedio,container)
    } else {
        const p=document.createElement('p')
        p.innerText="Non è stata pubblicata nessuna recensione."
        container.appendChild(p)
    }
}
function onResponseCarrello(response){
    return response.text()
}
function onResponseAggiornaPreferito(response){
    if(response.ok){
        fetch(app_url+"/recensioni/fetchProdottoRecensioni/"+product).then(onResponse).then(onJsonProdotto)
    }
}

function onResponseAggiornaRecensioni(response){
    if(response.ok){
        fetch(app_url+"/recensioni/fetchRecensioni/"+product).then(onResponse).then(onJsonRecensioni)
    }
}

function onTextCarrello(text){//restituisce il numero di prodotti nel carrello, dunque aggiungo il contatore
    const countCarrello=document.querySelector("#countCarrello")
    countCarrello.innerText=text
}

function aggiungiCarrello(){
    fetch(app_url+"/aggiungiCarrello/"+product).then(onResponseCarrello).then(onTextCarrello)
}

function aggiungiWishlist(){
    fetch(app_url+"/aggiungiWishlist/"+product).then(onResponseAggiornaPreferito)
}

function rimuoviWishlist(){
    fetch(app_url+"/rimuoviWishlist/"+product).then(onResponseAggiornaPreferito)
}

function mostraScheda(event){
    const bottoneScheda=event.currentTarget
    const bloccoEsterno=bottoneScheda.parentNode.parentNode 
    const scheda=bloccoEsterno.querySelector('.scheda') 
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
function mostraAreaRecensione(){
    const areaRecensione=document.querySelector("#areaRecensione")
    if(areaRecensione.classList.contains("hidden")){
        areaRecensione.classList.remove("hidden")
        bottoneRecensione.innerText="Annulla"
    } else {
        areaRecensione.classList.add("hidden")
        bottoneRecensione.innerText="Scrivi una recensione"
        document.querySelector("textarea").value=""
        formRecensione.voto.value=1

    }
}

function pubblicaRecensione(event){
    event.preventDefault()
    const error=document.querySelector(".error")
    if(formRecensione.testoRecensione.value!==""){//se l'area di testo per la recensione non è vuota passo alla fetch per pubblicare la recensione, altrimenti dò errore
        error.classList.add("hidden")
        const formData={method:'POST', body: new FormData(formRecensione)}
        fetch(app_url+"/recensioni/pubblicaRecensione/"+product, formData).then(onResponseAggiornaRecensioni)
    } else {
        error.classList.remove("hidden")
    }
}

function eliminaErrore(){
    document.querySelector(".error").classList.add("hidden")
}

function like(event){
    const id=event.currentTarget.parentNode.parentNode.dataset.id
    fetch(app_url+"/like/"+id).then(onResponseAggiornaRecensioni)
}
function dislike(event){
    const id=event.currentTarget.parentNode.parentNode.dataset.id
    fetch(app_url+"/dislike/"+id).then(onResponseAggiornaRecensioni)
}

const container=document.querySelector("section .containerProdotti")
const product=document.querySelector('section').dataset.product
const bottoneRecensione=document.querySelector('#bottoneRecensione')
bottoneRecensione.addEventListener('click',mostraAreaRecensione)
const formRecensione=document.forms['formRecensione']
formRecensione.addEventListener('submit',pubblicaRecensione)
formRecensione.testoRecensione.addEventListener('blur',eliminaErrore)
fetch(app_url+"/recensioni/fetchProdottoRecensioni/"+product).then(onResponse).then(onJsonProdotto)
fetch(app_url+"/recensioni/fetchRecensioni/"+product).then(onResponse).then(onJsonRecensioni)
