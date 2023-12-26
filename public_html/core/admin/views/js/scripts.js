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

createFile()

function createFile () {

    let files = document.querySelectorAll('input[type=file]')

    let fileStore = [];

    if (files.length) {

        files.forEach(item => {

            item.onchange = function () {

                let multiple = false;

                let parentContainer

                let container

                // Проверка наличия аттрибута multiple HTML
                if (item.hasAttribute('multiple')) {

                    multiple = true

                    parentContainer = this.closest('.gallery_container')

                    if (!parentContainer) return false;

                    container = parentContainer.querySelectorAll('.empty_container')

                    // Добавление новых ячеек. Если кол-во добавляемых файлов больше числа существующих ячеек
                    if (container.length < this.files.length) {

                        for(let index = 0; index < this.files.length - container.length; index++) {

                            let element = document.createElement('div')

                            element.classList.add('vg-dotted-square', 'vg-center', 'empty_container')

                            parentContainer.append(element)

                        }

                        container = parentContainer.querySelectorAll('.empty_container')

                    }

                }

                let fileName = item.name

                let attributeName = fileName.replace(/[\[\]]/g, '')

                for (let i in this.files) {

                    if (this.files.hasOwnProperty(i)) {

                        if (multiple) {

                            if (typeof fileStore[fileName] === 'undefined') fileStore[fileName] = []

                            let elementId = fileStore[fileName].push(this.files[i]) - 1

                            container[i].setAttribute(`data-deleteFileId-${attributeName}`, elementId)

                            showImage(this.files[i], container[i])

                            deleteNewFiles(elementId, fileName, attributeName, container[i])

                        } else {

                            container = this.closest('.img_container').querySelector('.img_show')

                            showImage(this.files[i], container)

                        }

                    }

                }

            }
        })

        function deleteNewFiles (elementId, fileName, attributeName, container) {

            container.addEventListener('click', function () {

                this.remove()

                delete fileStore[fileName][elementId]

            })

        }

        function showImage(item, container) {

            let reader = new FileReader()

            container.innerHTML = ''

            reader.readAsDataURL(item)

            reader.onload = e => {

                container.innerHTML = '<img class="img_item" src="" alt="">'

                container.querySelector('img').setAttribute('src', e.target.result)

                container.classList.remove('empty_container')

            }
        }


    }

}

