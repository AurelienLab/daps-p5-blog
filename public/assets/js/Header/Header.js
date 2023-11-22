export class Header {
    constructor(elementSelector) {
        this.headerElement = document.querySelector(elementSelector);
    }

    init = () => {
        const headerElement = this.headerElement
        document.querySelector('.js-toggle-menu').addEventListener('click', function (e) {
            e.preventDefault()
            headerElement.classList.toggle('open')

            if (headerElement.classList.contains('open')) {
                document.body.classList.add('burger-open');
            } else {
                document.body.classList.remove('burger-open');
            }
        })

        document.addEventListener('scroll', function (e) {
            if (window.scrollY > 90) {
                headerElement.classList.add('scrolled')
            } else {
                headerElement.classList.remove('scrolled')
            }
        })
    }
}
