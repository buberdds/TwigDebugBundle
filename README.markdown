sf2TwigDebug
==========

sf2TwigDebug is an easy to use template debugger intended as well for programmer then integration.
If you ever lost more than 5 seconds figuring our where was located the template of a specific portion of a webpage,
or what controller did call the render for a template, this bundle is for you.

Note that this project is still in a development/beta state.

Requirement
-----------

- Symfony2
- Twig

Installation
------------

1.
Open the /deps file, and add

    [IdealTechTwigDebugBundle]
        git=git://github.com/idealtech/sf2TwigDebug.git
        target=bundles/IdealTech/TwigDebugBundle

Run bin/vendors install

2.
If it's the first bundle that you use from us, make sure to define the namespace
Open the /app/autoload.php and add the IdealTech namespace in the registerNamespaces array:

       'IdealTech'                      => __DIR__.'/../vendor/bundles',

3.
Tell the bundle to be loaded when you're using the development environment:
Open the /app/appKernel.php and add the TwigDebug bundle in the dev bundle condition:

    if (in_array($this->getEnvironment(), array('dev', 'test'))) {
        //...
        $bundles[] = new IdealTech\TwigDebugBundle\IdealTechTwigDebugBundle();

4.
You must add the following configuration in your configuration file that is loaded
for the development environment:

    twig:
        base_template_class:  "IdealTech\TwigDebugBundle\Twig\Template"


Usage
-----
When in development environment, simply add a ?templates GET variable in the URL.
The page you output extra debug information.

ps.: It is normal to experience layout problems when the extra debugging div are displayed.

You may also add the experimental ?block variable to visualize where the block are used.