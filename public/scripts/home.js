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
chimica=9
economia=6
analisi1=9
algebra=9
fondamenti=9
fisica1=9
so=6
fisica2=9
archi=6
tds=9
oop=6
elettrotecnica=9
analisi2=9
elettronica=9
startup=6
calcolatori=9
infmus=6
db=12
auto=12
cd=6
iot=6
//171
M=(chimica*25+economia*30+analisi1*19+algebra*23+fondamenti*26+fisica1*27+
    so*25+fisica2*28+archi*28+tds*27+oop*24+elettrotecnica*22+analisi2*20+elettronica*28+
    startup*30+calcolatori*28+infmus*23+db*30+auto*24+cd*30+iot*24)/171
console.log(M)
P=2 //entro 3 anni, 1 entro 4
L=1/3+1/3
Cmax=(8/27)*M
voto=(11/3)*M+P+L
console.log(voto)
console.log(Cmax)