// noinspection JSUnusedGlobalSymbols,JSCheckFunctionSignatures

import {getBabelOutputPlugin} from '@rollup/plugin-babel';
import {terser} from 'rollup-plugin-terser';

const output = (name) => {
    return {
        input: `js/${name}.js`,
        output: [
            {
                file: `assets/bundle/${name}.js`,
                format: 'esm'
            },
            {
                file: `assets/bundle/${name}.min.js`,
                format: 'esm',
                plugins: [getBabelOutputPlugin({ presets: ['@babel/preset-env'] }), terser()],
                sourcemap: true
            }
        ]
    }
};

export default [
    output('generic'),
    output('archive')
];