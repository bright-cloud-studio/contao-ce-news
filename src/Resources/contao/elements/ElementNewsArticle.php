<?php

/**
 * @copyright  Bright Cliud Studio
 * @author     Bright Cloud Studio
 * @package    Contao CE News
 * @license    LGPL-3.0+
 * @see	       https://github.com/bright-cloud-studio/contao-ce-glide
 */

namespace Bcs\NewsArticleBundle;

use Contao\BackendTemplate;
use Contao\ContentText;
use Contao\NewsModel;
use Contao\System;

class ContentNewsArticle extends ContentText
{
	/* Template @var string */
	protected $strTemplate = 'ce_news_article';

	/* Generate the content element */
	public function compile()
	{
		$request = System::getContainer()->get('request_stack')->getCurrentRequest();

		if ($request && System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest($request))
		{
			$this->strTemplate = 'be_wildcard';
			$this->Template = new BackendTemplate($this->strTemplate);
			$this->Template->title = $this->headline;
		}

		// Slider configuration
		$this->Template->news_article = "SUCCESS";
	}

    public function generate()
    {
        // Get news item
		$objArticle = \NewsModel::findPublishedByParentAndIdOrAlias($this->news, array($this->news_archive));

		if ($objArticle === null)
		{
			return '';
		}

        echo "hit";

    }
    
}
