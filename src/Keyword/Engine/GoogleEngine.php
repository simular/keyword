<?php
/**
 * Part of keyword project. 
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Keyword\Engine;

use Joomla\Uri\Uri;
use PHPHtmlParser\Dom;

/**
 * The GoogleEngine class.
 * 
 * @since  {DEPLOY_VERSION}
 */
class GoogleEngine extends AbstractEngine
{
	/**
	 * Property host.
	 *
	 * @var  string
	 */
	protected $host = 'https://www.google.com.tw';

	/**
	 * Property path.
	 *
	 * @var  string
	 */
	protected $path = '/search';

	/**
	 * Property query.
	 *
	 * @var  array
	 */
	protected $query = [
		'q' => null,
		'num' => 100,
		'ie' => 'UTF-8'
	];

	/**
	 * getPage
	 *
	 * @param string $keyword
	 *
	 * @return  string
	 */
	public function getPage($keyword)
	{
		$uri = $this->prepareUri($keyword);

		return $this->get($uri->toString());
	}

	/**
	 * prepareUri
	 *
	 * @param string $keyword
	 *
	 * @return  \Windwalker\Uri\Uri
	 */
	public function prepareUri($keyword)
	{
		$uri = parent::prepareUri($keyword);

		$uri->setVar('q', htmlspecialchars($keyword));

		return $uri;
	}

	/**
	 * getOrdering
	 *
	 * @param string $url
	 * @param string $keyword
	 *
	 * @return  int|bool
	 */
	public function getOrdering($url, $keyword)
	{
		$body = $this->getPage($keyword);

		$dom = new Dom;
		$dom->load($body);

		$cites = $dom->find('.g h3.r a');

		$url = urldecode($url);

		$i = 1;
		$found = false;

		if (!count($cites))
		{
			throw new \RuntimeException('Google no response', 1201);
		}

		foreach ($cites as $k => $cite)
		{
			$link = $cite->href;
			$link = new Uri($link);
			$link = $link->getVar('q');

			if (!$link)
			{
				$link = $cite->href;
			}

			$link = urldecode($link);

			if (strpos($link, $url) !== false)
			{
				$found = true;

				break;
			}

			$i++;
		}

		if (!$found)
		{
			$i = false;
		}

		return $i;
	}
}
