document.addEventListener('DOMContentLoaded', () => {

    let messageWrap = document.querySelector('.wq-message__wrap')

    if (messageWrap) {

        let styles = {
            position: 'fixed',
            top: '10%',
            left: '50%',
            transform: 'translateX(-50%)',
            display: 'block',
            zIndex: 9999
        }

        let successStyles = {
            backgroundColor: '#4c8a3c',
            color: 'white',
            marginBottom: '10px',
            padding: '25px 30px',
            borderRadius: '20px'
        }

        let errorStyles = {
            backgroundColor: '#d34343',
            color: 'white',
            marginBottom: '10px',
            padding: '25px 30px',
            borderRadius: '20px'
        }

        if (messageWrap.innerHTML.trim()) {

            for (let i in styles) {

                messageWrap.style[i] = styles[i]

            }

            if (!messageWrap.children.length) {

                messageWrap.innerHTML = `<div>${messageWrap.innerHTML}</div>`

            }

            for (let i in messageWrap.children) {

                if (messageWrap.children.hasOwnProperty(i)) {

                    let typeStyles = /success/i.test(messageWrap.children[i].classList.value) ? successStyles : errorStyles

                    for (let j in typeStyles) {

                        messageWrap.children[i].style[j] = typeStyles[j]

                    }

                }

            }

            ['click', 'scroll'].forEach(event => document.addEventListener(event, hideMessages))

        }

    }

    function hideMessages() {

        let messageWrap = document.querySelector('.wq-message__wrap')

        if (messageWrap) {

            messageWrap.remove()

        }

        ['click', 'scroll'].forEach(event => document.removeEventListener(event, hideMessages))

    }

})