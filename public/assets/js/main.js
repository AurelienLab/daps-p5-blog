import {Header} from "./Header/Header.js";

import {hljs} from './lib/highlight/highlight.js'

const header = new Header('.header')

document.addEventListener('DOMContentLoaded', function () {
    header.init()
    hljs.highlightAll();
})
