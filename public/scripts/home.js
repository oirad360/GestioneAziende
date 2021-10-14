function onResponse(response){
    return response.json()
}

function onJson(json){
    const container=document.querySelector(".container")
    for(item of json){
        const blocco=document.createElement('div')
        blocco.classList.add("blocco")
        blocco.dataset.id=item.nome
        const a=document.createElement('a')
        a.href="prodotti/"+item.nome
        const img=document.createElement('img')
        img.src="assets/"+item.logo
        a.appendChild(img)
        blocco.appendChild(a)
        const h1=document.createElement('h1')
        h1.innerText=item.nome.toUpperCase()
        blocco.appendChild(h1)
        const p1=document.createElement('p')
        p1.innerText="In catalogo: "+item.numProdotti
        const p2=document.createElement('p')
        p2.innerText="Disponibili: "+item.numDisponibili
        const p3=document.createElement('p')
        p3.innerText="In arrivo: "+item.numInArrivo
        blocco.appendChild(p1)
        blocco.appendChild(p2)
        blocco.appendChild(p3)
        container.appendChild(blocco)
    }
}

fetch(app_url+"/home/fetchAziende").then(onResponse).then(onJson)