# gdo6 (Core)

The core module is a core module, which means it is automatically installed.
In theory a non db installation should be able to ship static sites via the core mechanisms.
Of course rendering depends on the UI module, which is just another core namespace.

## Javascript minifier

To make use of the Javascript minifer in module core, install the following npm packages.

    npm install -g ng-annotate
    npm install -g uglyfijs
