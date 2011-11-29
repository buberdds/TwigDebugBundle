sf2TwigDebug
==========

sf2TwigDebug is an easy to use template debugger intended for programmers as well as integrators.
If you have ever lost more than 5 seconds figuring out where a template for a specific portion of a webpage located,
or which controller was calling the render for a template; this bundle is for you.

Note that this project is still in a development/beta state.

Requirements
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
If it's the first bundle that you use from IdealTech, make sure to appropriately define the namespace
Open the /app/autoload.php and add the IdealTech namespace in the registerNamespaces array:

       'IdealTech'                      => __DIR__.'/../vendor/bundles',

3.
Make it so the bundle is loaded when you're using the development environment:
Open the /app/appKernel.php and add the TwigDebug bundle in the dev bundle condition:

    if (in_array($this->getEnvironment(), array('dev', 'test'))) {
        //...
        $bundles[] = new IdealTech\TwigDebugBundle\IdealTechTwigDebugBundle();

4.
You must add the following configuration in your configuration file which is loaded
into the development environment:

    twig:
        base_template_class:  "IdealTech\TwigDebugBundle\Twig\Template"


Usage
-----
When in the development environment, simply add a ?templates "GET" variable in the URL.
The page will output extra debug information.

ps.: It is normal to experience layout problems when the extra debugging divs are displayed.

You may also add the experimental ?block variable to visualize where the blocks are used.