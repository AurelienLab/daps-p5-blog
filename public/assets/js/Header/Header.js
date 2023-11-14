export class Header {
    constructor(elementSelector) {
        this.headerElement = document.querySelector(elementSelector);
    }

    init = () => {
        const headerElement = this.headerElement
        document.querySelector('.js-toggle-menu').addEventListener('click', function (e) {
            e.preventDefault()
            headerElement.classList.toggle('open')
        })

        document.addEventListener('scroll', function (e) {
            //TODO: Ajouter une class scrolled au header
        })
    }
}
