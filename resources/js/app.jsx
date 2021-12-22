import React from 'react';
import {render} from 'react-dom';
import {createInertiaApp} from '@inertiajs/inertia-react';
import DefaultLayout from './Layouts/DefaultLayout';

createInertiaApp({
    resolve: (name) => new Promise((resolve, reject) => import(`./Pages/${name}`)
        .then((module) => {
            const page = module.default;

            if (typeof page.layout === 'undefined') {
                page.layout = (page) => <DefaultLayout children={page}/>;
            }

            resolve(module);
        })
        .catch(reject)),
    setup: ({el, App, props}) => render(<App {...props} />, el)
});
