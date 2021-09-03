const $ = function(selector) {
    return document.querySelector(selector);
};

const $$ = function(selector) {
    return document.querySelectorAll(selector) || [];
};

const makeEl = function(html) {
    const template = document.createElement('template');

    template.innerHTML = html.trim();

    return template.content.firstChild;
};

const clearEl = function(el) {
    while (el.firstChild) {
        el.removeChild(el.firstChild);
    }
};

const escape = function(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}


export { $, $$, makeEl, clearEl, escape };