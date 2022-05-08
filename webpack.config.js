const path = require('path');

module.exports = {
    resolve: {
        alias: {
            vue$: path.join(__dirname, 'node_modules/vue/dist/vue.esm-bundler.js'),
            '@': path.resolve('resources/js'),
        },
    },
};
