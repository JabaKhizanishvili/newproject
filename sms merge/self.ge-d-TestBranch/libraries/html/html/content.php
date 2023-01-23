<?php

/**
 * @version		$Id: content.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// no direct access


JLoader::register('TableContent', JPATH_LIBRARIES . DS . 'joomla' . DS . 'database' . DS . 'table' . DS . 'content.php');

/**
 * Utility class to fire onPrepareContent for non-article based content.
 *
 * @package 	WSCMS.Framework
 * @subpackage	HTML
 * @since		1.5
 */
abstract class HTMLContent
{

    /**
     * Fire onPrepareContent for content that isn't part of an article.
     *
     * @param string The content to be transformed.
     * @param array The content params.
     * @return string The content after transformation.
     */
    public static function prepare($text, $params = null)
    {
        if($params === null)
        {
            $params = array();
        }
        /*
         * Create a skeleton of an article. This is a bit of a hack.
         */
        $nodb = null;
        $article = new TableContent($nodb);
        $article->text = $text;
        JPluginHelper::importPlugin('content');
        $dispatcher = JDispatcher::getInstance();
        $results = $dispatcher->trigger(
                'onPrepareContent', array($article, &$params, 0)
        );

        return $article->text;
    }

}
