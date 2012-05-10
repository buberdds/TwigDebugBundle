<?php

/*
 * This file is part of the IdealTech TwigDebugBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IdealTech\TwigDebugBundle\Twig;

/**
 * Default base class for compiled templates.
 *
 * @author Francois Mazerolle <fmazerolle@idealtechnology.net>
 */
abstract class Template extends \Twig_Template
{
    /**
     * @var boolean
     */
    protected $debugFiles  = false;

    /**
     * Define the debugging options.
     *
     * @param \Twig_Environment $env Twig Environnement
     */
    public function __construct(\Twig_Environment $env)
    {
        @session_start();

        //Check if we need to start a debug session
        //(this condition won't be triggered by AJAX-requests, so we need the session)
        if (isset($_GET['twig_debug'])) {
            $_SESSION['twig_debug'] = (bool)$_GET['twig_debug'];
        }

        //Check if a debug session was previously started
        if (isset($_SESSION['twig_debug'])) {
            $this->debugFiles = (bool)$_SESSION['twig_debug'];
        }

        //Do not debug the profiler tool bar
        $bundleName = substr($this->getTemplateName(), 0, strpos($this->getTemplateName(), ':'));
        if ($bundleName == 'WebProfilerBundle') {
            $this->debugFiles = false;
        }

        parent::__construct($env);
    }
    /**
     * Render a debug template.
     * This avoid debugger template to be debugged -- causing an infinite loop.
     *
     * @param string $file    File path relative to the bundle view folder.
     * @param array  $arrVars Array of variables to pass to the view.
     *
     * @return null Content is directly outputted.
     */
    protected function renderDebug($file, $arrVars)
    {
        $loader = new \Twig_Loader_Filesystem(__DIR__ . "/../Resources/views");
        $env = new \Twig_Environment($loader);
        $template = $env->loadTemplate($file);
        echo $template->render($arrVars);
    }

    /**
     * Displays the template with the given context.
     *
     * @param array $context An array of parameters to pass to the template
     * @param array $blocks  An array of blocks to pass to the template
     *
     * @return null
     */
    public function display(array $context, array $blocks = array())
    {
    	  $this->displayWithErrorHandling($this->env->mergeGlobals($context), $blocks);
        if ($this->debugFiles) {
            $arrVars['templatePath'] = $this->env->getLoader()->getCacheKey($this->getTemplateName());
            $this->renderDebug('templateList.html.twig', $arrVars);
        }
    }
}