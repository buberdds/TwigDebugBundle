<?php

/*
 * This file is part of the IdealTech TwigDebugBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IdealTech\TwigDebugBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TwigDebugDataCollector extends DataCollector
{
    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = array();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ideal_tech_twig_debug';
    }
}