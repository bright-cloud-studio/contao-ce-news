<?php

/**
 * @copyright  Bright Cloud Studio
 * @author     Bright Cloud Studio
 * @package    Contao CE News
 * @license    LGPL-3.0+
 * @see	       https://github.com/bright-cloud-studio/contao-ce-news
 */

namespace Bcs\NewsArticleBundle;

use Bcs\Module\ModuleNewsArticle;
use Contao\BackendTemplate;
use Contao\ContentText;
use Contao\NewsModel;
use Contao\System;

class ContentNewsArticle extends ContentText
{
	/* Template @var string */
	protected $strTemplate = 'ce_news_article';

	/* Generate the content element */
	public function generate()
	{
	    // Get the News Article selected in the Content Element
	    $objArticle = NewsModel::findPublishedByParentAndIdOrAlias($this->news, array($this->news_archive));

        // Find out where our Request is coming from
		$request = System::getContainer()->get('request_stack')->getCurrentRequest();
        // If we have a request, and it is a Backend request, setup our backend template
		if ($request && System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest($request))
		{
            // Load the "backend wildcard" template
			$objTemplate = new BackendTemplate('be_wildcard');
            
			// requires existing template from autoload.ini
			//$this->news_template = 'ce_news_article';

            // Create a News Article module, apply our article to it
			$newsarticle = new ModuleNewsArticle($objArticle, $this);

            // Generate the News Article module then apply the HTML to our backend wildcard
			$objTemplate->wildcard = $newsarticle->generate();
		}
		
		// If this isnt a backend request, create a News Article module, apply our article
		$newsarticle = new ModuleNewsArticle($objArticle, $this);
        // return the generated HTML
		return $newsarticle->generate();

		
	}

}
