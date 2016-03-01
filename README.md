# eZ Support Tools

This bundle provides information about the system eZ Platform/Studio is running on, intended to help system
administrators and Support engineers. It is extracted from PlatformUI, and currently depends on it.

The `InfoProviderInterface` allows extensible info providers.

## TODO

Add a Command which provides CLI/file output, using the same InfoProviders.

`SystemInfoController` uses a variadic constructor, to allow a variable number of InfoProviders. I'm
pretty sure this is the wrong way to do this.

The frontend isn't working. To test it, edit PlatformUI `services.yml` and `routing_pjax.yml`, and replace
`ezsystems.platformui.helper.systeminfo` with `ezsystems.ezsupporttools.controller.systeminfo`.
