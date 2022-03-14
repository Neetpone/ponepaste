punishedponepaste
=================


# Building the JS
When you change the JS, you need to rebuild it. `assets/bundle.js` is used in dev, `assets/bundle.min.js` is used in production.

You need Yarn (version 1, not version 2 - 2 may work, but I haven't tried it.) After that, whenever you change anything under `js/`, you need to run `yarn rollup --config`. Good luck!