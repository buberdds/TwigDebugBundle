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

    private $debugFiles  = false;
    private $debugBlocks = false;

    /**
     * Define the debugging options.
     *
     * @param \Twig_Environment $env Twig Environnement
     */
    public function __construct(\Twig_Environment $env)
    {
        $bundleName = substr($this->getTemplateName(), 0, strpos($this->getTemplateName(), ':'));

        //Make sure we're not debugging the profiler tool bar
        if ($bundleName != 'WebProfilerBundle' && isset($_GET['templates'])) {
            $this->debugFiles = true;
        }

        if (isset($_GET['blocks'])) {
            $this->debugBlocks = true;
        }

        parent::__construct($env);
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
        $color = self::random_color();
        $iColor = self::color_inverse($color);
        $tpl = $this->getTemplateName();
        $arrStack = debug_backtrace();
        $arrStack = array_map(function($arr){
            $buffer = '<li>';
            if (isset($arr['class'])) {
                $buffer .= "{$arr['class']}::{$arr['function']}()";
            } else {
                $buffer .= "{$arr['file']} at line {$arr['line']}";
            }
            $buffer .= '</li>';
            return $buffer;
        }, $arrStack);
        $txtStack = implode('', $arrStack);
        $uuid = uniqid();
        $jsInfoDisplay = "document.getElementById(\"$uuid\").style.display";

        $basic = 'Template : ' . $tpl;
        $info = '<br>Path : ' . $this->env->getLoader()->getCacheKey($tpl);
        $info .= '<br>Stack trace:';
        $info .= '<pre><ol>';
        $info .= $txtStack;
        $info .= '</ol></pre>';


        echo "<div style='border:3px solid #$color;'>";
        echo "<div style='background-color:#$color;color:#$iColor;padding:2px;'>";
        echo $basic . " (<a href='#' onclick='$jsInfoDisplay = $jsInfoDisplay==\"block\" ? \"none\" : \"block\"' style='color:#$iColor;'>+</a>)";
        echo "<div id='$uuid' style='display:none;'>$info</div>";
        echo "</div>";

        parent::display($context, $blocks);

        echo '</div>';
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
        if (!$this->debugBlocks) {
            parent::displayBlock($name, $context, $blocks);
            return null;
        }

        //Ok, debugging is activated for blocks
        $color = self::random_color();
        $iColor = self::color_inverse($color);
        $info = 'Block : ' . $name;

        echo "<div style='border:2px dotted #$color;'>";
        echo "<div style='background-color:#$color;color:#$iColor;padding:2px;'>$info</div>";

        parent::displayBlock($name, $context, $blocks);

        echo '</div>';
    }


    /**
     * Generate a random color HEX code
     *
     * @return string
     *
     * @author Shoo
     * @src http://www.jonasjohn.de/snippets/php/random-color.htm
     */
    public static function random_color()
    {
        return dechex(rand(0, 10000000));
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