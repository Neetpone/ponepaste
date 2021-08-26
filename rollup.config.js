// noinspection JSUnusedGlobalSymbols

import { getBabelOutputPlugin } from '@rollup/plugin-babel';
import { terser } from 'rollup-plugin-terser';

export default {
    input: 'js/main.js',
    output: [
        {
            file: 'assets/bundle.js',
            format: 'esm'
        },
        {
            file: 'assets/bundle.min.js',
            format: 'esm',
            plugins: [getBabelOutputPlugin({ presets: ['@babel/preset-env'] }), terser()],
            sourcemap: true
        }
    ]
};
