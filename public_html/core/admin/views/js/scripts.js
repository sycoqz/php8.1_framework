document.querySelector('.sitemap-button').onclick = (e) => {

    e.preventDefault();

    createSitemap();

}

let linksCounter = 0;

function createSitemap() {

    linksCounter++;

    console.log(linksCounter);
    Ajax({data: {ajax: 'sitemap', linksCounter: linksCounter}})
        .then((result) => {
            console.log('Success - ' + result);
            console.log(linksCounter);
        })
        .catch((result) => {
            console.log(linksCounter);
            console.log('Fail - ' + result);
            createSitemap();
        });

}

