export class Modal {
    constructor(elementSelector) {
        this.modalElement = elementSelector;
    }

    init = () => {
        this.modalElement.querySelectorAll('.js-modal-close').forEach((el) => el.addEventListener('click', (e) => this.close()))
    }

    close() {
        this.modalElement.classList.remove('open')
    }
}
