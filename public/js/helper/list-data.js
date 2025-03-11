
async function $listDatafetch ({urlString, queryData}) {
    let query = new URLSearchParams(queryData).toString()
    const response = await fetch(urlString + '?' + query);
    return await response.json();
}

