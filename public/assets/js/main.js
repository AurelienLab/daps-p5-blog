import {Header} from "./Header/Header.js";
import {Modal} from "./Modal/Modal.js";

import {hljs} from './lib/highlight/highlight.js'

const header = new Header('.header')

document.addEventListener('DOMContentLoaded', function () {
    header.init()
    hljs.highlightAll();

    document.querySelectorAll('.modal').forEach((el) => new Modal(el).init())
})
