import './page/bepo-turbo-suggest-target-list';
import './page/bepo-turbo-suggest-target-detail';
import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';

const { Module } = Shopware;

Module.register('bepo-turbo-suggest-target', {
    type: 'plugin',
    name: 'TurboSuggest',
    title: 'bepo-turbo-suggest.general.mainMenuItemGeneral',
    description: 'bepo-turbo-suggest.general.descriptionTextModule',
    color: '#ff3d58',
    icon: 'regular-search',

    routes: {
        list: {
            component: 'bepo-turbo-suggest-target-list',
            path: 'list',
            meta: {
                parentPath: 'sw.settings.index'
            }
        },
        detail: {
            component: 'bepo-turbo-suggest-target-detail',
            path: 'detail/:id?',
            meta: {
                parentPath: 'bepo.turbo.suggest.target.list'
            }
        }
    },

    navigation: [{
        id: 'bepo-turbo-suggest-target',
        label: 'bepo-turbo-suggest.general.mainMenuItemGeneral',
        color: '#ff3d58',
        icon: 'regular-search',
        path: 'bepo.turbo.suggest.target.list',
        parent: 'sw-marketing',
        position: 100
    }],

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    }
});
