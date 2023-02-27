// noinspection JSUnusedGlobalSymbols,JSCheckFunctionSignatures

import { nodeResolve } from '@rollup/plugin-node-resolve';
import {getBabelOutputPlugin} from '@rollup/plugin-babel';
import {terser} from 'rollup-plugin-terser';

const output = (name) => {
    return {
        input: `js/${name}.js`,
        output: [
            {
                file: `public/assets/bundle/${name}.js`,
                format: 'esm'
            },
            {
                file: `public/assets/bundle/${name}.min.js`,
                format: 'esm',
                plugins: [getBabelOutputPlugin({ presets: ['@babel/preset-env'] }), terser()],
                sourcemap: true
            }
        ],
        plugins: [nodeResolve()]
    }
};

export default [
    output('generic'),
    output('archive'),
    output('user_profile')
];