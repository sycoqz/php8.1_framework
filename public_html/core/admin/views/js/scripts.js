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

        let form = document.querySelector('#main-form')

        if (form) {

            form.onsubmit = function (e) {

                if (!isEmpty(fileStore)) {

                    e.preventDefault()

                    let forData = new FormData(this)

                    for (let i in fileStore) {

                        if (fileStore.hasOwnProperty(i)) {

                            forData.delete(i)

                            //Получение чистого имени свойства
                            let rowName = i.replace(/[\[\]]/g, '')

                            fileStore[i].forEach((item, index) => {

                                forData.append(`${rowName}[${index}]`, item)

                            })

                        }

                    }

                    forData.append('ajax', 'editData')

                    Ajax({
                        url: this.getAttribute('action'),
                        type: 'post',
                        data: forData,
                        processData: false,
                        contentType: false
                    }).then(result => {

                        try {

                            result = JSON.parse(result)

                            if (!result.success) throw new Error()

                            location.reload()

                        } catch (e) {

                            errorAlert()

                        }

                    })

                }

            }

        }

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

changeMenuPosition()

function changeMenuPosition() {

    let form = document.querySelector('#main-form')

    if (form) {

        let selectParent = form.querySelector('select[name=parent_id]')

        let selectPosition = form.querySelector('select[name=menu_position]')

        if (selectParent && selectPosition) {

            let defaultParent = selectParent.value

            let defaultPosition = +selectPosition.value

            selectParent.addEventListener('change', function () {

                let defaultChoice = false

                if (this.value === defaultParent) defaultChoice = true

                // Отправка данных на сервер

                Ajax({
                    data:{
                        table: form.querySelector('input[name=table]').value,
                        'parent_id': this.value,
                        ajax: 'change_parent',
                        iteration: !form.querySelector('#tableId') ? 1 : +!defaultChoice
                    }
                }).then(result => {

                    result = +result

                    if (!result) return errorAlert()

                    let newSelect = document.createElement('select')

                    newSelect.setAttribute('name', 'menu_position')

                    newSelect.classList.add('vg-input', 'vg-text', 'vg-full', 'vg-firm-color1')

                    for (let i = 1; i <= result; i++) {

                        let selected = defaultChoice && i === defaultPosition ? 'selected' : ''

                        newSelect.insertAdjacentHTML('beforeend',
                            `<option ${selected} value="${i}">${i}</option>`)

                    }

                    selectPosition.before(newSelect)

                    selectPosition.remove()

                    selectPosition = newSelect

                })

            })

        }

    }



}

