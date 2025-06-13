import { $, $$, toggleEl, escape } from './dom';
import { TagsInput } from "./tag_input";

const decompress = base64string => {
    const bytes = Uint8Array.from(atob(base64string), c => c.charCodeAt(0));
    const cs = new DecompressionStream('gzip');
    const writer = cs.writable.getWriter();
    writer.write(bytes);
    writer.close();
    return new Response(cs.readable).arrayBuffer().then(function (arrayBuffer) {
        return new TextDecoder().decode(arrayBuffer);
    });
}

const setupSignupModal = () => {
    const signupButton = $('[data-target~="#signin"],[data-target~="#signup"]');

    if (signupButton) {
        signupButton.href = 'javascript:void(0)';

        signupButton.addEventListener('click', () => {
            $('.modal').classList.add('is-active');
        });

        $('.modal-button-close').addEventListener('click', () => {
            $('.modal').classList.remove('is-active');
        });
    }
}

const globalSetup = () => {
    Array.prototype.forEach.call($$('.js-tag-input'), (el) => {
        new TagsInput(el).attach();
    });

    setupSignupModal();

    const embedButton = $('.panel-tools .embed-tool');

    if (embedButton){
        embedButton.addEventListener('click', (evt) => {
            if (evt.target && evt.target.closest('.panel-tools')) {
                toggleEl(evt.target.closest('.panel-tools').querySelector('.panel-embed'));
            }
        });
    }

    const expandButton = $('.expand-tool');

    if (expandButton) {
        expandButton.addEventListener('click', (evt) => {
            if (evt.target && evt.target.closest('.panel')) {
                const panel = evt.target.closest('.panel');

                if (panel.classList.contains('panel-fullsize')) {
                    panel.classList.remove('panel-fullsize');
                } else {
                    panel.classList.add('panel-fullsize');
                }
            }
        });
    }

    // Notifications
    (document.querySelectorAll('.notification .delete') || []).forEach(($delete) => {
        const $notification = $delete.parentNode;

        $delete.addEventListener('click', () => {
            $notification.parentNode.removeChild($notification);
        });
    });

    // Hamburger menu
    const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);
    if ($navbarBurgers.length > 0) {
        $navbarBurgers.forEach(el => {
            el.addEventListener('click', () => {
                const target = el.dataset.target;
                const $target = document.getElementById(target);
                el.classList.toggle('is-active');
                $target.classList.toggle('is-active');
            });
        });
    }

    // CAPTCHA refresh
    const captchaContainer = $('.captcha_container');

    if (captchaContainer) {
        const refreshElement = captchaContainer.querySelector('a');
        const imageElement = captchaContainer.querySelector('img');

        if (refreshElement && imageElement) {
            refreshElement.addEventListener('click', () => {
                let src = imageElement.src;

                if (src.indexOf('&refresh') !== -1) {
                    // yeah, it's kinda cancerous. fuck off.
                    src = src.split('&rand=')[0];
                } else {
                    src += '&refresh';
                }

                imageElement.src = src + '&rand=' + Math.random();
            });
        }
    }

    const hiddenElements = $$('.js-hidden');

    if (hiddenElements) {
        Array.prototype.forEach.call(hiddenElements, (elem) => {
            toggleEl(elem);
        });
    }

    // Used for encoding email to try to avoid spam.
    const encodedElements = $$('[data-encoded-text]');

    if (encodedElements) {
        [...encodedElements].forEach(elem => {
           decompress(elem.dataset.encodedText).then(data => {
               setTimeout(() => elem[elem.dataset.encodedAttr] = data, 1500);
           });
        });
    }
}

export { globalSetup };