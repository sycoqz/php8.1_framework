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

                            showImage(this.files[i], container[i], function () {

                                parentContainer.sortable({excludedElements: 'label .empty_container'})

                            })

                            deleteNewFiles(elementId, fileName, attributeName, container[i])

                        } else {

                            container = this.closest('.img_container').querySelector('.img_show')

                            showImage(this.files[i], container)

                        }

                    }

                }

            }

            let dropArea = item.closest('.img_wrapper')

            if (dropArea) {

                dragAndDrop(dropArea, item)

            }

        })

        let form = document.querySelector('#main-form')

        if (form) {

            form.onsubmit = function (e) {

                createJsSortable(form)

                if (!isEmpty(fileStore)) {

                    e.preventDefault()

                    let formData = new FormData(this)

                    for (let i in fileStore) {

                        if (fileStore.hasOwnProperty(i)) {

                            formData.delete(i)

                            //Получение чистого имени свойства
                            let rowName = i.replace(/[\[\]]/g, '')

                            fileStore[i].forEach((item, index) => {

                                formData.append(`${rowName}[${index}]`, item)

                            })

                        }

                    }

                    formData.append('ajax', 'editData')

                    Ajax({
                        url: this.getAttribute('action'),
                        type: 'post',
                        data: formData,
                        processData: false,
                        contentType: false
                    }).then(result => {

                        try {

                            result = JSON.parse(result)

                            if (!result.success) throw new Error()

                            location.reload()

                        } catch (e) {

                            alert('Произошла внутренняя ошибка')

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

        function showImage(item, container, callback) {

            let reader = new FileReader()

            container.innerHTML = ''

            reader.readAsDataURL(item)

            reader.onload = e => {

                container.innerHTML = '<img class="img_item" src="" alt="">'

                container.querySelector('img').setAttribute('src', e.target.result)

                container.classList.remove('empty_container')

                callback && callback()

            }
        }

        function dragAndDrop(area, input) {

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach((eventName, index) => {

                area.addEventListener(eventName, e => {

                    e.preventDefault()

                    e.stopPropagation()

                    if (index < 2) {

                        area.style.background = 'lightblue'

                    } else {

                        area.style.background = '#fff'

                        if (index === 3) { // drop

                            input.files = e.dataTransfer.files

                            input.dispatchEvent(new Event('change'))

                        }

                    }

                })

            })

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

blockParameters()

function blockParameters() {

    let wraps = document.querySelectorAll('.select_wrap')

    if (wraps.length) {

        let selectAllIndexes = []

        wraps.forEach(item => {

            let next = item.nextElementSibling

            if (next && next.classList.contains('option_wrap')) {

                item.addEventListener('click', e => {

                    if (!e.target.classList.contains('select_all')) {

                        next.slideToggle()

                    } else {

                        // Кнопка выделить всё
                        if (getComputedStyle(next)['display'] === 'block') {

                            let index = [...document.querySelectorAll('.select_all')].indexOf(e.target)

                            if (typeof selectAllIndexes[index] === 'undefined') selectAllIndexes[index] = false

                            selectAllIndexes[index] = !selectAllIndexes[index]

                            next.querySelectorAll('input[type=checkbox]').
                            forEach(element => element.checked = selectAllIndexes[index])

                        }

                    }

                })

            }

        })

    }

}

showHideMenuSearch()

function showHideMenuSearch() {

    document.querySelector('#hideButton').addEventListener('click', () => {

        document.querySelector('.vg-carcass').classList.toggle('vg-hide')

    })

    let searchButton = document.querySelector('#searchButton')

    let searchInput = searchButton.querySelector('input[type=text]')

    searchButton.addEventListener('click', () => {

        searchButton.classList.add('vg-search-reverse')

        searchInput.focus()

    })

    // Закрытие поиска. Переход на страницу редактирования искомого объекта
    searchInput.addEventListener('blur', e => {

        if (e.relatedTarget && e.relatedTarget.tagName === 'A')
            return

        searchButton.classList.remove('vg-search-reverse')

    })

}

// Самозамыкающаяся функция
let searchResultHover = (() => {

    let searchResult = document.querySelector('.search_res')

    let searchInput = document.querySelector('#searchButton input[type=text]')

    let defaultInputValue = null

    // Обработка переходов между запросами через стрелочки
    function searchKeyArrows(e) {

        if (!(document.querySelector('#searchButton').classList.contains('vg-search-reverse')) ||
            (e.key !== 'ArrowUp' && e.key !== 'ArrowDown')) return;

        let children = [...searchResult.children]

        if (children.length) {

            e.preventDefault()

            let activeItem = searchResult.querySelector('.search_act')

            let activeIndex = activeItem ? children.indexOf(activeItem) : -1

            if (e.key === 'ArrowUp')
                activeIndex = activeIndex <= 0 ? children.length - 1 : --activeIndex
            else
                activeIndex = activeIndex === children.length - 1 ? 0 : ++activeIndex

            children.forEach(item => item.classList.remove('search_act'))

            children[activeIndex].classList.add('search_act')

            searchInput.value = children[activeIndex].innerText.replace(/\(.+?\)\s*$/, '')

        }

    }

    function setDefaultValue() {

        searchInput.value = defaultInputValue

    }

    searchResult.addEventListener('mouseleave', setDefaultValue)

    window.addEventListener('keydown', searchKeyArrows)

    return () => {

        defaultInputValue = searchInput.value

        if (searchResult.children.length) {

            let children = [...searchResult.children]

            children.forEach(item => {

                item.addEventListener('mouseover', () => {

                    children.forEach(element => element.classList.remove('search_act'))

                    item.classList.add('search_act')

                    searchInput.value = item.innerText

                })

            })

        }

    }

})()

searchResultHover()

search()

function search() {

    let searchInput = document.querySelector('input[name=search]')

    if (searchInput) {

        searchInput.oninput = () => {

            if (searchInput.value.length > 1) {

                Ajax(
                    {
                        data:{
                            data:searchInput.value,
                            table:document.querySelector('input[name="search_table"]').value,
                            ajax:'search'
                        }
                    }
                ).then(result => {

                    try {

                        result = JSON.parse(result)

                        let resultBlock = document.querySelector('.search_res')

                        let counter = result.length > 20 ? 20 : result.length

                        if (resultBlock) {

                            resultBlock.innerHTML = '';

                            for (let i = 0; i < counter; i++) {

                                resultBlock.insertAdjacentHTML('beforeend',
                                    `<a href="${result[i]['alias']}">${result[i]['name']}</a>`)

                            }

                            searchResultHover()

                        }

                    }
                    catch (e) {

                        console.log(e)
                        alert('Ошибка в системе поиска по административной панели')

                    }
                })

            }

        }

    }

}

let galleries = document.querySelectorAll('.gallery_container')

if (galleries.length) {

    galleries.forEach(item => {

        item.sortable({
            excludedElements: 'label .empty_container',
            stop: function (dragElement) {

                console.log(this)
                console.log(dragElement)

            }
        })

    })

}

function createJsSortable(form) {

    if (form) {

        let sortable = form.querySelectorAll('input[type=file][multiple]')

        if (sortable.length) {

            sortable.forEach(item => {

                let container = item.closest('.gallery_container')

                let name = item.getAttribute('name')

                if (name && container) {

                    name = name.replace(/\[\]/g,'')

                    // Формирование массива отсортированных полей и создание input'a при отсутствии.
                    let inputSorting = form.querySelector(`input[name="js-sorting[${name}]"]`)

                    if (!inputSorting) {

                        inputSorting = document.createElement('input')

                        inputSorting.name = `js-sorting[${name}]`

                        form.append(inputSorting)

                    }

                    let result = []

                    for (let i  in container.children) {

                        if (container.children.hasOwnProperty(i)) {

                            // Проверка не участвующих элементов
                            if (!container.children[i].matches('label')
                                && !container.children[i].matches('.empty_container')) {

                                // Уже сохранённые объекты и добавленные объекты соответственно
                                if (container.children[i].tagName === 'A') {

                                    result.push(container.children[i].querySelector('img').getAttribute('src'))

                                } else {

                                    result.push(container.children[i].getAttribute(`data-deletefileid-${name}`))

                                }

                            }

                        }

                    }

                    console.log(result)

                    // Создание строки из массива/объекта
                    inputSorting.value = JSON.stringify(result)

                }

            })

        }

    }

}
