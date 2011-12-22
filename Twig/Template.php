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
     * @var boolean
     */
    protected $debugHierarchy = false;



    protected static $templateDeep = 1;
    protected static $templateParent = '';
    protected static $arrTemplateHierarchy = array();

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

        if (isset($_GET['hierarchy'])) {
            $this->debugHierarchy = true;
        }


        parent::__construct($env);


        //The twig extentions are not available, so we'll consider that
        //there can only be 1 root template, and dump the JS and the CSS on the page.
        if ($this->debugFiles && self::$templateParent == '') {
            echo '<style type="text/css">';
            echo file_get_contents(__DIR__ . "/../Resources/public/css/twigDebug.css");
            echo '</style>';

            echo '<script type="text/javascript">';
            echo file_get_contents(__DIR__ . "/../Resources/public/js/twigDebug.js");
            echo '</script>';
        }
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
        if (!$this->debugFiles) {
            parent::display($context, $blocks);
            return null;
        }


        //Ok, debugging is activated for files
        $arrVars = array();
        $arrVars['backColor']    = self::random_color();
        $arrVars['foreColor']    = self::color_inverse($arrVars['backColor']);
        $arrVars['templateName'] = $this->getTemplateName();
        $arrVars['templatePath'] = $this->env->getLoader()->getCacheKey($arrVars['templateName']);
        $arrVars['uuid']         = uniqid();
        $arrVars['deep']         = self::$templateDeep;
        $arrVars['parent']       = self::$templateParent;



        $arrVars['stack'] = array_map(function($arr){
            if (isset($arr['class'])) {
                return "{$arr['class']}::{$arr['function']}()";
            } else {
                return "{$arr['file']} at line {$arr['line']}";
            }
        }, debug_backtrace());


        self::$arrTemplateHierarchy[] = array(
            'template' => $this->getTemplateName(),
            'parent'   => self::$templateParent,
            'deep'     => self::$templateDeep,
        );


        //Pass the real content to the debug container.
        $oldParent = self::$templateParent;
        self::$templateParent = $this->getTemplateName();
        self::$templateDeep++;
        ob_start();
        parent::display($context, $blocks);
        $arrVars['content'] = ob_get_contents();
        ob_end_clean();
        self::$templateDeep--;
        self::$templateParent = $oldParent;
        $this->renderDebug('templateContainer.html.twig', $arrVars);


        if ($this->debugHierarchy && self::$templateParent == '') {
            $this->showTemplateHierarchy();

        }
    }

    /**
     * Render the template hierarchy debug.
     *
     * @return null Content is directly outputted.
     */
    protected function showTemplateHierarchy()
    {
        $arrVars = array(
            'arrTpl' => self::$arrTemplateHierarchy,
        );

        $this->renderDebug('templateHierarchy.html.twig', $arrVars);

    }



    /**
     * Displays a block.
     *
     * @param string $name    The block name to display
     * @param array  $context The context
     * @param array  $blocks  The current set of blocks
     *
     * @return null
     */
    public function displayBlock($name, array $context, array $blocks = array())
    {
        if (!$this->debugFiles) {
            parent::displayBlock($name, $context, $blocks);
            return null;
        }

        //Ok, debugging is activated for blocks
        $arrVars = array();
        $arrVars['backColor'] = self::random_color();
        $arrVars['foreColor'] = self::color_inverse($arrVars['backColor']);
        $arrVars['blockName'] = $name;


        //Pass the real content to the debug container.
        ob_start();
        parent::displayBlock($name, $context, $blocks);
        $arrVars['content'] = ob_get_contents();
        ob_end_clean();

        $this->renderDebug('blockContainer.html.twig', $arrVars);
    }


    /**
     * Generate a random color HEX code
     *
     * @return string
     *
     * @author Jonas John
     * @src http://www.jonasjohn.de/snippets/php/random-color.htm
     */
    public static function random_color()
    {
        mt_srand((double) microtime()*1000000);
        $c = '';
        while (strlen($c)<6) {
            $c .= sprintf("%02X", mt_rand(0, 255));
        }
        return $c;
    }

    /**
     * Find the oposite color of a specified color code
     *
     * @param string $color HEX representation of a color code.
     *
     * @return string
     *
     * @src http://www.jonasjohn.de/snippets/php/color-inverse.htm
     * @author Jonas John
     * @author HJ
     */
    public static function color_inverse($color)
    {
        $color = str_replace('#', '', $color);
        if (strlen($color) != 6) {
            $color = str_repeat(substr($color, 0, 1), 2)
                    .str_repeat(substr($color, 1, 1), 2)
                    .str_repeat(substr($color, 2, 1), 2);
        }
        $rgb = '';
        for ($x=0; $x<3; $x++) {
            $c = 255 - hexdec(substr($color, (2*$x), 2));
            $c = ($c < 0) ? 0 : dechex($c);
            $rgb .= (strlen($c) < 2) ? '0'.$c : $c;
        }
        return $rgb;
    }
}