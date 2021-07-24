function onResponse(response){
    return response.json()
}

function onJsonWishlist(json){//carica tutti i prodotti che ho in wishlist
    const titoloWishlist=document.querySelector('#titoloWishlist')
    const wishlistContainer=document.querySelector('#wishlist')
    wishlistContainer.innerHTML="";
    if(json.length>0){
        titoloWishlist.innerText="La tua wishlist"
        for(item of json){
            const blocco=document.createElement('div')
            blocco.classList.add('blocco')
            const titolo=document.createElement('h1')
            titolo.innerText=item.titolo
            blocco.appendChild(titolo)
            const img=document.createElement('img')
            img.src=app_url+"/assets/"+item.immagine
            const link=document.createElement('a')
            link.href=app_url+"/recensioni/"+item.titolo
            link.appendChild(img)
            blocco.appendChild(link)
            const bottoneRimuoviWishlist=document.createElement('div')
            bottoneRimuoviWishlist.classList.add('bottoneRimuoviWishlist') 
            blocco.appendChild(bottoneRimuoviWishlist)
            bottoneRimuoviWishlist.addEventListener('click',rimuoviWishlist)
            wishlistContainer.appendChild(blocco)
        }
    } else {
        titoloWishlist.innerText="Non ci sono prodotti in wishlist"
    }
}

function onJsonAcquisti(json){//carica tutti gli acquisti effettuati
    const acquisti=document.querySelector("#titoloAcquisti")
    if(json.length>0){
        acquisti.innerText="I tuoi acquisti"
        const acquistiContainer=document.querySelector("#acquisti")
        for(item of json){
            const blocco=document.createElement('div')
            blocco.classList.add('blocco')
            const titolo=document.createElement('h1')
            titolo.innerText=item.titolo
            blocco.appendChild(titolo)
            const img=document.createElement('img')
            img.src=app_url+"/assets/"+item.immagine
            const link=document.createElement('a')
            link.href=app_url+"/recensioni/"+item.titolo
            link.appendChild(img)
            blocco.appendChild(link)
            const quantita=document.createElement('p')
            quantita.innerText="Quantità: "+item.quantita
            blocco.appendChild(quantita)
            acquistiContainer.appendChild(blocco)
        }
    } else {
        acquisti.innerText="Non ci sono acquisti"
    }
}
function onResponseAggiornaPreferiti(response){
    if(response.ok){
        fetch(app_url+"/profilo/fetchWishlist").then(onResponse).then(onJsonWishlist)
    }
}
function onResponseAggiornaRecensioni(response){
    if(response.ok){
        fetch(app_url+"/profilo/fetchRecensioniProfilo").then(onResponse).then(onJsonRecensioni)
    }
}
function rimuoviWishlist(event){
    const bottoneRimuoviWishlist=event.currentTarget
    const blocco=bottoneRimuoviWishlist.parentNode
    const titolo=blocco.childNodes[0].innerText
    fetch(app_url+"/rimuoviWishlist/"+titolo).then(onResponseAggiornaPreferiti)
}

function mostraFormImpiego(event){//mostra/nasconde il form per cambiare impiego
    if(form.classList.contains("hidden")){
        form.classList.remove("hidden")
        event.currentTarget.innerText="Annulla"
    }else{
        form.classList.add("hidden")
        event.currentTarget.innerText="Cambia impiego"
    }
}

function cambiaImpiego(event){
    event.preventDefault()
    const formData={method:'post',body: new FormData(form)}
    fetch(app_url+"/profilo/cambiaImpiego",formData).then(onResponse).then(onJsonImpiego)
}

function onJsonImpiego(json){
    console.log(json)
    const select=form.impiego
    select.innerHTML=""
    impieghiPassati.innerHTML=""
    for(item of json.elementiForm){ //aggiorno il form di scelta dell'azienda dopo aver cambiato impiego
        const option=document.createElement("option")
        option.value=item.id
        option.innerText=item.nome
        select.appendChild(option)
    }
    for(item of json.impieghiPassati){//aggiorna la sezione degli impieghi passati dopo aver cambiato impiego
        const riga=document.createElement('div')
        const strong=document.createElement('strong')
        strong.innerText=item.nome
        const span=document.createElement('span')
        span.innerText=": dal "+item.dataAssunzione+" al "+item.fineImpiego
        riga.appendChild(strong)
        riga.appendChild(span)
        impieghiPassati.appendChild(riga)
    }
    const strong=document.querySelector("strong");
    strong.innerText=json.impiego.nome
    const dataAssunzione=document.querySelector("#dataAssunzione")
    dataAssunzione.innerText=json.impiego.dataAssunzione
}

function onResponseText(response){
    return response.text()
}

function onJsonImage(json){
    const errors=document.querySelectorAll(".errorphp")
    for(error of errors){
        error.remove()
    }
    if(json.errors){//faccio una verifica degli errori anche lato php per sicurezza
        for(error of json.errors){
            const span=document.createElement('span')
            span.innerText=error
            span.classList.add("errorphp")
            formPropic.querySelector('div').appendChild(span)
        }
    }else{//metto la nuova immagine di profilo nell'header
        const propic=document.querySelector('.profile .propic')
        propic.style="background-image:url("+app_url+"/uploads/"+json.fileName+");"
    }
}

function cambiaImmagine(event){
    event.preventDefault()
    const errors=document.querySelectorAll(".error")
    let flag=false
    for(error of errors){//verifico se sono presenti errori (segnalati da checkImmagine), se sì imposto un flag
        if(!error.classList.contains("hidden")){
            flag=true
            break;
        }
    }
    if(!formPropic.image.files[0]){//verifico che il campo per caricare l'immagine non sia vuoto, se sì imposto un flag e mostro l'errore
        document.querySelector("#compila").classList.remove("hidden")
        flag=true
    } else {
        document.querySelector("#compila").classList.add("hidden")
    }
    if(!flag){//se il flag è false (vuol dire che non ha trovato errori) si passa alla fetch per cambiare immagine
        const formData={method: 'POST',body: new FormData(formPropic)}
        fetch(app_url+"/profilo/cambiaImmagine",formData).then(onResponse).then(onJsonImage)
    }
}
function checkImmagine(){//verifico che l'immagine scelta sia del formato giusto e che non occupi più di 2MB, in tal caso mostro gli errori
    document.querySelector("#compila").classList.add("hidden")
    const imageSize=formPropic.image.files[0].size
    const imageExt=formPropic.image.files[0].name.split(".").pop()
    const errors=document.querySelectorAll(".error")
    if(!["jpeg","jpg","png","gif"].includes(imageExt)){
        errors[0].classList.remove("hidden")
    } else {
        errors[0].classList.add("hidden")
    }
    if(imageSize>2000000){
        errors[1].classList.remove("hidden")
    } else {
        errors[1].classList.add("hidden")
    }
}
function mostraFormImmagine(event){
    if(formPropic.classList.contains("hidden")){
        formPropic.classList.remove("hidden")
        event.currentTarget.innerText="Annulla"
    }else{
        document.querySelector("#compila").classList.add("hidden")
        const errors=document.querySelectorAll(".error")
        for(error of errors){
            error.classList.add("hidden")
        }
        formPropic.classList.add("hidden")
        formPropic.image.value=null
        event.currentTarget.innerText="Cambia immagine del profilo"
    }
}
function mostraImpieghiPassati(event){
    if(impieghiPassati.classList.contains("hidden")){
        impieghiPassati.classList.remove("hidden")
        event.currentTarget.innerText="Annulla"
    }else{
        impieghiPassati.classList.add("hidden")
        event.currentTarget.innerText="Mostra impieghi passati"
    }
}

function onJsonRecensioni(json){ //carica tutte le recensioni pubblicate da noi
    const container=document.querySelector("#recensioni")
    const titoloRecensioni=document.querySelector("#titoloRecensioni")
    container.innerHTML=""
    if(json.length>0){
        titoloRecensioni.innerText="Le tue recensioni"
        for(item of json){
            const blocco=document.createElement('div')
            blocco.dataset.id=item.id
            blocco.classList.add("recensione")
            const riga=document.createElement('div')
            riga.classList.add("riga")
            const prodotto=document.createElement('a')
            prodotto.classList.add("titoloProdotto")
            prodotto.innerText=item.titolo
            prodotto.href=app_url+"/recensioni/"+prodotto.innerText
            riga.appendChild(prodotto)
            const data=document.createElement('p')
            data.innerText=item.data
            riga.appendChild(data)
            blocco.appendChild(riga)
            const voto=document.createElement('img')
            voto.classList.add("voto")
            voto.src=app_url+"/assets/"+item.voto+".png"
            blocco.appendChild(voto)
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
            const riga1=document.createElement('div')
            riga1.classList.add('riga')
            riga1.appendChild(bloccoLike)
            const bottoneEliminaRecensione=document.createElement('span')
            bottoneEliminaRecensione.classList.add('bottoneEliminaRecensione')
            bottoneEliminaRecensione.innerText="Elimina recensione"
            bottoneEliminaRecensione.addEventListener('click',eliminaRecensione)
            riga1.appendChild(bottoneEliminaRecensione)
            blocco.appendChild(riga1)
            container.appendChild(blocco)
        }
    } else {
        titoloRecensioni.innerText="Non hai ancora scritto recensioni"
    }
}

function like(event){
    const id=event.currentTarget.parentNode.parentNode.parentNode.dataset.id
    fetch(app_url+"/like/"+id).then(onResponseAggiornaRecensioni)
}
function dislike(event){
    const id=event.currentTarget.parentNode.parentNode.parentNode.dataset.id
    fetch(app_url+"/dislike/"+id).then(onResponseAggiornaRecensioni)
}
function eliminaRecensione(event){
    const id=event.currentTarget.parentNode.parentNode.dataset.id
    fetch(app_url+"/profilo/eliminaRecensione/"+id).then(onResponseAggiornaRecensioni)
}

fetch(app_url+"/profilo/fetchWishlist").then(onResponse).then(onJsonWishlist)
fetch(app_url+"/profilo/fetchAcquisti").then(onResponse).then(onJsonAcquisti)
fetch(app_url+"/profilo/fetchRecensioniProfilo").then(onResponse).then(onJsonRecensioni)
const button=document.querySelector("#cambiaImpiego")
if(button) button.addEventListener('click', mostraFormImpiego)
const form=document.forms['cambiaImpiego']
if(form) form.addEventListener('submit', cambiaImpiego)
const formPropic=document.forms['propic']
formPropic.addEventListener('submit',cambiaImmagine)
formPropic.image.addEventListener('change',checkImmagine)
const buttonImmagine=document.querySelector("#cambiaImmagine")
buttonImmagine.addEventListener('click',mostraFormImmagine)
const mostraImpieghi=document.querySelector('#mostraImpieghi')
if(mostraImpieghi) mostraImpieghi.addEventListener('click',mostraImpieghiPassati)
const impieghiPassati=document.querySelector('#impieghiPassati')