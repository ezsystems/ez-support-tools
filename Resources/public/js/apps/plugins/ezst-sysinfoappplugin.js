YUI.add('ezst-sysinfoappplugin', function (Y) {
    // Good practices:
    // * use a custom namespace 'eZST' here
    // * put the plugins in a 'Plugin' sub namespace
    Y.namespace('eZST.Plugin');

    Y.eZST.Plugin.SysInfoAppPlugin = Y.Base.create('ezstSysInfoAppPlugin', Y.Plugin.Base, [], {
        initializer: function () {
            var app = this.get('host'); // the plugged object is called host

            console.log("Hey, I'm a plugin for PlatformUI App!");
            console.log("And I'm plugged in ", app);

            console.log('Registering the ezstSysInfoView in the app');
            app.views.ezstSysInfoView = {
                type: Y.eZST.SysInfoView,
            };

            console.log("Let's add a route");
            app.route({
                name: "eZSTSysInfo",
                path: "/ezst/sysinfo",
                view: "ezstSysInfoView",
                service: Y.eZST.SysInfoViewService, // the service will be used to load the necessary data
                // we want the navigationHub (top menu) but not the discoveryBar
                // (left bar), we can try different options
                sideViews: {'navigationHub': true, 'discoveryBar': false},
                callbacks: ['open', 'checkUser', 'handleSideViews', 'handleMainView'],
            });

            // adding a new route so that we don't have anything else to change
            // and we can manage the default `offset` value in the view service
            //app.route({
            //    name: "eZSTListOffset",
            //    path: "/ezst/list/:offset/",
            //    view: "ezstListView",
            //    service: Y.eZST.ListViewService,
            //    sideViews: {'navigationHub': true, 'discoveryBar': false},
            //    callbacks: ['open', 'checkUser', 'handleSideViews', 'handleMainView'],
            //});
            //app.route({
            //    name: "eZSTListOffsetTypeIdentifier",
            //    path: "/ezst/list/:offset/:typeIdentifier",
            //    view: "ezstListView",
            //    service: Y.eZST.ListViewService,
            //    sideViews: {'navigationHub': true, 'discoveryBar': false},
            //    callbacks: ['open', 'checkUser', 'handleSideViews', 'handleMainView'],
            //});
        },
    }, {
        NS: 'ezstTypeApp' // don't forget that
    });

    // registering the plugin for the app
    // with that, the plugin is automatically instantiated and plugged in
    // 'platformuiApp' component.
    Y.eZ.PluginRegistry.registerPlugin(
        Y.eZST.Plugin.SysInfoAppPlugin, ['platformuiApp']
    );
});
