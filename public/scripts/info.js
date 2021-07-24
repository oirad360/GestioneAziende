function onResponse(response){
    return response.json()
}

function onJsonWIKI(json){
    const text=document.querySelector('#text')
    const table=document.querySelector('table')
    if(json.dataset.data.length===0){
        table.innerHTML=""
        text.classList.add('error')
        text.innerText="Nessun risultato trovato per il periodo selezionato."
    } else {
        text.classList.remove('error')
        text.innerText="Risultati trovati: "+json.dataset.data.length
        text.classList.remove('hidden')
        table.innerHTML=""
        const tr1=document.createElement('tr')
        const th1=document.createElement('th')
        th1.innerText="Data"
        tr1.appendChild(th1)
        const th2=document.createElement('th')
        th2.innerText="Apertura"
        tr1.appendChild(th2)
        const th3=document.createElement('th')
        th3.innerText="Chiusura"
        tr1.appendChild(th3)
        const th4=document.createElement('th')
        th4.innerText="Max"
        tr1.appendChild(th4)
        const th5=document.createElement('th')
        th5.innerText="Min"
        tr1.appendChild(th5)
        table.appendChild(tr1)
        for(let i=0; i<json.dataset.data.length; i++){
            const tr2=document.createElement('tr')
            const td1=document.createElement('td')
            td1.innerText=json.dataset.data[i][0]
            tr2.appendChild(td1)
            const td2=document.createElement('td')
            td2.innerText=json.dataset.data[i][1]
            tr2.appendChild(td2)
            const td3=document.createElement('td')
            td3.innerText=json.dataset.data[i][4]
            tr2.appendChild(td3)
            const td4=document.createElement('td')
            td4.innerText=json.dataset.data[i][2]
            tr2.appendChild(td4)
            const td5=document.createElement('td')
            td5.innerText=json.dataset.data[i][3]
            tr2.appendChild(td5)
            table.appendChild(tr2)
        }
    }
}

function onJsonHKEX(json){
    const text=document.querySelector('#text')
    const table=document.querySelector('table')
    if(json.dataset.data.length===0){
        table.innerHTML=""
        text.classList.add('error')
        text.innerText="Nessun risultato trovato per il periodo selezionato."
    } else {
        text.classList.remove('error')
        text.innerText="Risultati trovati: "+json.dataset.data.length
        table.innerHTML=""
        const tr1=document.createElement('tr')
        const th1=document.createElement('th')
        th1.innerText="Data"
        tr1.appendChild(th1)
        const th2=document.createElement('th')
        th2.innerText="Apertura"
        tr1.appendChild(th2)
        const th3=document.createElement('th')
        th3.innerText="Chiusura"
        tr1.appendChild(th3)
        const th4=document.createElement('th')
        th4.innerText="Max"
        tr1.appendChild(th4)
        const th5=document.createElement('th')
        th5.innerText="Min"
        tr1.appendChild(th5)
        table.appendChild(tr1)
        for(let i=0; i<json.dataset.data.length; i++){
            const tr2=document.createElement('tr')
            const td1=document.createElement('td')
            td1.innerText=json.dataset.data[i][0]
            tr2.appendChild(td1)
            const td2=document.createElement('td')
            td2.innerText=json.dataset.data[i][9]
            tr2.appendChild(td2)
            const td3=document.createElement('td')
            td3.innerText=json.dataset.data[i][1]
            tr2.appendChild(td3)
            const td4=document.createElement('td')
            td4.innerText=json.dataset.data[i][7]
            tr2.appendChild(td4)
            const td5=document.createElement('td')
            td5.innerText=json.dataset.data[i][8]
            tr2.appendChild(td5)
            table.appendChild(tr2)
        }
    }
}
function onJsonImpiegati(json){
    const container=document.querySelector("#impiegati")
    container.innerHTML=""
    if(json.length>0){
        for(item of json){
            const profile=document.createElement('div')
            profile.classList.add("user")
            const propic=document.createElement('div')
            propic.classList.add("propic")
            if(item.propic==="defaultAvatar.jpg"){
                propic.style="background-image:url("+app_url+"/assets/defaultAvatar.jpg);"
            } else {
                propic.style="background-image:url("+app_url+"/uploads/"+item.propic+");"
            }
            profile.appendChild(propic)
            propic.addEventListener('click',onProPicClick)
            const blocco=document.createElement('div')
            const user=document.createElement('p')
            user.innerText=item.username
            blocco.appendChild(user)
            const impiego=document.createElement('p')
            impiego.innerText=item.impiego
            blocco.appendChild(impiego)
            const link=document.createElement('a')
            link.href=app_url+"/profiloEsterno/"+item.username
            link.appendChild(blocco)
            profile.appendChild(link)
            container.appendChild(profile)
        }
    } else {
        const p=document.createElement('p')
        p.innerText="Non ci sono impiegati per quell'azienda."
        container.appendChild(p)
    }
}

function onSubmit(event){
    event.preventDefault()
    const formData={method: 'POST', body: new FormData(azioni)}
    const database_code=azioni.database_dataset.value.split("/")[0]
    switch(database_code){
        case "WIKI":
            fetch(app_url+"/info/quandl_api",formData).then(onResponse).then(onJsonWIKI)
        break
        case "HKEX":
            fetch(app_url+"/info/quandl_api",formData).then(onResponse).then(onJsonHKEX)
        break
    }
}

function mostraImpiegati(event){
    event.preventDefault()
    const formData={method:'post',body: new FormData(form)}
    fetch(app_url+"/info/fetchImpiegati",formData).then(onResponse).then(onJsonImpiegati)
}


const azioni=document.forms["azioni"]
azioni.addEventListener('submit',onSubmit)
const date = new Date();
const month = String((date.getMonth()+1)).padStart(2,'0')
const day = String(date.getDate()).padStart(2,'0')
const today = date.getFullYear()+'-'+month+'-'+day
azioni.end_date.value=today
const aYearAgo = (date.getFullYear()-1)+'-'+month+'-'+day
azioni.start_date.value=aYearAgo
const form=document.forms["mostraImpiegati"]
form.addEventListener('submit', mostraImpiegati)