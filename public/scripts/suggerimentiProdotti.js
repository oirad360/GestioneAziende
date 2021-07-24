//----------------------------------------------------------------DEFINIZIONE DELLE FUNZIONI----------------------------------------------------
function onResponse(response){
    return response.json()
}

function onJson(json){
    if(json['contents']!==-1){
        const wishlist=document.querySelectorAll('#wishlist .blocco')//controllo se il prodotto di cui è arrivata la response è ancora in wishlist
        let foundItem=false                                     //perchè potrei averlo eliminato nel frattempo che la response era in arrivo (se sono abbastanza veloce), in tal caso esco dalla onJson
        const titoloProdotto=json.prodotto
        for(const item of wishlist){
            if(item.childNodes[0].innerText===titoloProdotto){
                foundItem=true
                break
            }
        }
        if(foundItem){
            //Math.floor(Math.random() * (max - min + 1)) + min ottengo un intero random compreso tra max e min (estremi inclusi)
            const numSuggerimenti = Math.floor(Math.random()*(4 - 1 + 1)) + 1 //numSuggerimenti compreso fra 1 e 4
            console.log("esecuzione della onJson, riceverai "+numSuggerimenti+" suggerimenti per "+titoloProdotto);
            
            const listaOldIndex=[]
            const listaResults=[]
            for(let i=0; i<numSuggerimenti; i++){
                let newIndex
                let duplicato
                do{//genero indici random per scegliere quale risultato prendere dalla lista json.contents (con un meccanismo per non ottenere duplicati)
                    duplicato=false
                    newIndex = Math.floor(Math.random()*(json.contents.length))//num compreso fra 0 e json.contents.length-1 (è il numero di risultati arrivati)
                    for(const oldIndex of listaOldIndex){
                        if(newIndex===oldIndex){
                            duplicato=true
                            break
                        }
                    }
                }while(duplicato)
                listaResults.push({
                    url: json.contents[newIndex].url,
                    creator: json.contents[newIndex].creator
                })//riempio la lista dei risultati, metto url dell'immagine e creator
                listaOldIndex.push(newIndex)
            }
            console.log("ecco i risultati associati agli indici appena generati: ")
            console.log(listaResults)
            const bloccoSuggerimenti=document.querySelector("#suggerimenti")//appendo tutti i suggerimenti ottenuti nella sezione dei suggerimenti
            const bloccoCounter=document.querySelector("#numRisultati")
            if(countSuggerimenti===0){
                bloccoSuggerimenti.classList.remove("hidden")
            }
            for(const result of listaResults){
                const img=document.createElement('img')
                img.src=result.url
                img.addEventListener('click', onThumbnailClick);
                const bloccoResult=document.createElement('div')
                bloccoResult.classList.add('bloccoResult')
                const titoloNoSpaces=titoloProdotto.split(" ").join("")
                bloccoResult.classList.add(titoloNoSpaces) //tutti i blocchi di suggerimenti inerenti al prodotto avranno una classe per essere riconosciuti, il nome della classe è il titolo del prodotto senza spazi
                bloccoResult.appendChild(img)
                const num=Math.floor(Math.random()*(5 - 3 + 1)) + 3
                const recensione=document.createElement('img')
                recensione.src=app_url+"/assets/"+num+".png"
                bloccoResult.appendChild(recensione)
                const creator=document.createElement('p')
                creator.innerText="Creator: "+result.creator
                bloccoResult.appendChild(creator)
                document.querySelector('#containerSuggerimenti').appendChild(bloccoResult)
                countSuggerimenti++
            }
            bloccoCounter.innerText=countSuggerimenti
        } else {
            console.log("il prodotto "+titoloProdotto+" non è in wishlist, esco dalla onJson")
        }
    } else {
        console.log("non ci sono suggerimenti per "+json.prodotto)
    }
}
function ricercaSuggerimenti(event){
    const titoloProdotto=event.currentTarget.parentNode.childNodes[0].innerText//prendo il titolo del prodotto che ho aggiunto in wishlist
    console.log("cerco suggerimenti per "+titoloProdotto)
    fetch(app_url+"/prodotti/creativeCommons_api/"+titoloProdotto).then(onResponse).then(onJson)
}

function cancellaSuggerimenti(event){
    const listaSuggerimenti = document.querySelectorAll('.bloccoResult')
    const titoloProdotto=event.currentTarget.parentNode.childNodes[0].innerText
    const titoloNoSpaces=titoloProdotto.split(" ").join("")

    for(const item of listaSuggerimenti){//rimuovo tutti i suggerimenti che hanno la classe uguale al titolo (senza spazi) del prodotto che ho rimosso
        if(item.classList.contains(titoloNoSpaces)){
            item.remove()
            countSuggerimenti--
        }
    }
    if(countSuggerimenti===0){
        document.querySelector("#suggerimenti").classList.add("hidden")
    } else {
        document.querySelector("#numRisultati").innerText=countSuggerimenti
    }
    
    for(const item of prodotti){//prodotti è una lista già definita in "prodotti.js", contiene tutti i prodotti della pagina
        if(item.childNodes[0].childNodes[0].innerText===titoloProdotto){
            item.childNodes[0].childNodes[2].addEventListener('click',ricercaSuggerimenti) //riaggiungo l'eventListener per ricercare i suggerimenti al bottone per aggiungere il prodotto alla Wishlist
        }
    }
}

function mostraSuggerimenti(event){//mostra/nasconde i suggerimenti ottenuti
    const containerSuggerimenti=document.querySelector("#containerSuggerimenti")
    const testo=document.querySelector("#text")
    if(containerSuggerimenti.classList.contains("hidden")){
        containerSuggerimenti.classList.remove("hidden")
        testo.classList.remove("hidden")
    } else {
        containerSuggerimenti.classList.add("hidden")
        testo.classList.add("hidden")
    }
}

//----------------------------------------------------------------MAIN----------------------------------------------------
let countSuggerimenti=0;

const bottoneSuggerimenti=document.querySelector("#cerchio")
bottoneSuggerimenti.addEventListener('click', mostraSuggerimenti)
