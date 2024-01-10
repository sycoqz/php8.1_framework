
function MCEInit(element, height = 400) {

    tinymce.init({
        selector: `textarea[id="${element || tinyMceDefaultAreas}"]`,
        placeholder: 'Печатать...',
        height: height,
        language: 'ru',
        browser_spellcheck: true,
        relative_urls: false,
        plugins: "advlist autolink lists link image charmap preview anchor pagebreak" +
            " searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime visualblocks" +
            " media nonbreaking save table directionality emoticons media",
        toolbar: "insertfile undo redo | styleselect | bold italic | forecolor backcolor | " +
            " alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | visualblocks" +
            " link image | formatselect fontsizeselect | code media emoticons paste pastetext",
        image_advtab: true,
        image_title: true,
        automatic_uploads: true,
        file_picker_types: 'image',
        images_reuse_filename: true,
        images_upload_handler: (file) => new Promise((resolve, reject) => {
            try {

                let formData = new FormData();

                formData.append('file', file.blob(), file.filename());

                formData.append('ajax', 'wyswyg_file')

                formData.append('table', document.querySelector('input[name="table"]').value)

                Ajax({
                    url: document.querySelector('#main-form').getAttribute('action'),
                    data: formData,
                    contentType: false,
                    processData: false,
                    type: 'post'
                }).then(result => {

                    resolve(JSON.parse(result).location)

                })

            }
            catch (e) {

                reject(e.message);

            }

        }),
        file_picker_callback: function (callback) {

            let input = document.createElement('input')

            input.setAttribute('type', 'file')

            // Ограничение типа файла для загрузки
            input.setAttribute('accept', 'image/*')

            input.click()

            input.onchange = function () {

                let reader = new FileReader()

                reader.readAsDataURL(this.files[0])

                reader.onload = () => {

                    let blobCache = tinymce.activeEditor.editorUpload.blobCache

                    let base64 = reader.result.split(',')[1]

                    let blobInfo = blobCache.create(this.files[0].name, this.files[0], base64)

                    blobCache.add(blobInfo)

                    callback(blobInfo.blobUri(), {title: this.files[0].name})

                }

            }

        }
    })

}

MCEInit()

let mceElements = document.querySelectorAll('input.tinyMceInit')

if (mceElements.length) {

    mceElements.forEach(item => {

        item.onchange = () => {

            let blockContent = item.closest('.vg-content')

            let textArea = item.closest('.vg-element').querySelector('textarea')

            let textAreaName = textArea.getAttribute('id')

            if (textAreaName) {

                if (item.checked) {

                    MCEInit(textAreaName, blockContent ? 400 : 300)

                } else {

                    tinymce.remove(`[id="${textAreaName}"]`)

                    if (!blockContent) textArea.value = textArea.value.replace(/<\/?[^>]+(>|$)/g, '')

                }

            }

        }

    })

}