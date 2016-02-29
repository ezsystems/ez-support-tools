YUI.add('ezconf-sysinfoview', function (Y) {
    // Good practice: use a custom namespace 'eZConf' here
    Y.namespace('eZConf');

    // Make sure the first parameter of Y.Base.create() (internal name) is unique
    // it is used for 3 things in PlatformUI:
    // * to identify the view if one wants to write a plugin for it
    // * to find the corresponding template
    // * to generate a CSS class on its container to ease styling
    Y.eZConf.SysInfoView = Y.Base.create('ezconfSysInfoView', Y.eZ.ServerSideView, [], {
        // this is YUI View mechanic to subscribe to DOM events (click, submit,
        // ...) and synthetic event (some custom event provided by YUI) like
        // 'tap' here.
        events: {
            '.ezconf-list-location': {
                // tap is 'fast click' (touch friendly)
                'tap': '_navigateToLocation'
            },
        },

        initializer: function () {
            console.log("Hey, I'm the list view");
            this.containerTemplate = '<div class="ez-view-ezconflistview"/>';
        },

        _navigateToLocation: function (e) {
            var link = e.target;

            e.preventDefault(); // don't want the normal link behavior

            // tell the view service we want to navigate somewhere
            // it's a custom event that will be bubble up to the view service
            // (and the app)
            // the second parameter is the data to add in the event facade, this
            // can be used by any event handler function bound to this event.
            this.fire('navigateTo', {
                routeName: link.getData('route-name'),
                routeParams: {
                    id: link.getData('route-id'),
                    languageCode: link.getData('route-languagecode'),
                }
            });
        },
    });
});
