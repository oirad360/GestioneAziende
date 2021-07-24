//questo script è relativo alla pagina di un profilo diverso dal proprio, presenta solo le recensioni di quel profilo e gli impieghi passati
function mostraImpieghiPassati(event){
    if(impieghiPassati.classList.contains("hidden")){
        impieghiPassati.classList.remove("hidden")
        event.currentTarget.innerText="Annulla"
    }else{
        impieghiPassati.classList.add("hidden")
        event.currentTarget.innerText="Mostra impieghi passati"
    }
}
function onResponse(response){
    return response.json()
}

function onJsonRecensioni(json){
    const container=document.querySelector("#recensioni")
    container.innerHTML=""
    if(json.length>0){
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
            blocco.appendChild(bloccoLike)
            container.appendChild(blocco)
        }
    }
}
function onResponseAggiorna(response){
    if(response.ok){
        fetch(app_url+"/profiloEsterno/fetchRecensioniProfiloEsterno/"+username).then(onResponse).then(onJsonRecensioni)
    }
}
function like(event){
    const id=event.currentTarget.parentNode.parentNode.dataset.id
    fetch(app_url+"/like/"+id).then(onResponseAggiorna)
}
function dislike(event){
    const id=event.currentTarget.parentNode.parentNode.dataset.id
    fetch(app_url+"/dislike/"+id).then(onResponseAggiorna)
}


const mostraImpieghi=document.querySelector('#mostraImpieghi')
if(mostraImpieghi) mostraImpieghi.addEventListener('click',mostraImpieghiPassati)
const impieghiPassati=document.querySelector('#impieghiPassati')
const username=document.querySelector("section").dataset.username
fetch(app_url+"/profiloEsterno/fetchRecensioniProfiloEsterno/"+username).then(onResponse).then(onJsonRecensioni)