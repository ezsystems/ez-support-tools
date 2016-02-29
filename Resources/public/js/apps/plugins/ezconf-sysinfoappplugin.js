YUI.add('ezconf-sysinfoappplugin', function (Y) {
    // Good practices:
    // * use a custom namespace 'eZConf' here
    // * put the plugins in a 'Plugin' sub namespace
    Y.namespace('eZConf.Plugin');

    Y.eZConf.Plugin.SysInfoAppPlugin = Y.Base.create('ezconfSysInfoAppPlugin', Y.Plugin.Base, [], {
        initializer: function () {
            var app = this.get('host'); // the plugged object is called host

            console.log("Hey, I'm a plugin for PlatformUI App!");
            console.log("And I'm plugged in ", app);

            console.log('Registering the ezconfSysInfoView in the app');
            app.views.ezconfSysInfoView = {
                type: Y.eZConf.SysInfoView,
            };

            console.log("Let's add a route");
            app.route({
                name: "eZConfSysInfo",
                path: "/ezconf/sysinfo",
                view: "ezconfSysInfoView",
                service: Y.eZConf.SysInfoViewService, // the service will be used to load the necessary data
                // we want the navigationHub (top menu) but not the discoveryBar
                // (left bar), we can try different options
                sideViews: {'navigationHub': true, 'discoveryBar': false},
                callbacks: ['open', 'checkUser', 'handleSideViews', 'handleMainView'],
            });

            // adding a new route so that we don't have anything else to change
            // and we can manage the default `offset` value in the view service
            //app.route({
            //    name: "eZConfListOffset",
            //    path: "/ezconf/list/:offset/",
            //    view: "ezconfListView",
            //    service: Y.eZConf.ListViewService,
            //    sideViews: {'navigationHub': true, 'discoveryBar': false},
            //    callbacks: ['open', 'checkUser', 'handleSideViews', 'handleMainView'],
            //});
            //app.route({
            //    name: "eZConfListOffsetTypeIdentifier",
            //    path: "/ezconf/list/:offset/:typeIdentifier",
            //    view: "ezconfListView",
            //    service: Y.eZConf.ListViewService,
            //    sideViews: {'navigationHub': true, 'discoveryBar': false},
            //    callbacks: ['open', 'checkUser', 'handleSideViews', 'handleMainView'],
            //});
        },
    }, {
        NS: 'ezconfTypeApp' // don't forget that
    });

    // registering the plugin for the app
    // with that, the plugin is automatically instantiated and plugged in
    // 'platformuiApp' component.
    Y.eZ.PluginRegistry.registerPlugin(
        Y.eZConf.Plugin.SysInfoAppPlugin, ['platformuiApp']
    );
});
