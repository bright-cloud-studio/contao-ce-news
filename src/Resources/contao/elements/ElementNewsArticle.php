<?php

/**
 * @copyright  Bright Cliud Studio
 * @author     Bright Cloud Studio
 * @package    Contao CE News
 * @license    LGPL-3.0+
 * @see	       https://github.com/bright-cloud-studio/contao-ce-glide
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
	    
	    $objArticle = NewsModel::findPublishedByParentAndIdOrAlias($this->news, array($this->news_archive));
	    
		$request = System::getContainer()->get('request_stack')->getCurrentRequest();

		if ($request && System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest($request))
		{
		    
			//$this->strTemplate = 'be_wildcard';
			//$this->Template = new BackendTemplate($this->strTemplate);
			//$this->Template->title = $this->headline;
			
			$objTemplate = new BackendTemplate('be_wildcard');
			// requires existing template from autoload.ini
			$this->news_template = 'ce_news_article';
			$newsarticle = new ModuleNewsArticle($objArticle, $this);
			$objTemplate->wildcard = $newsarticle->generate();
			
			//return $objTemplate->parse();
			
			
		}
		
		$newsarticle = new ModuleNewsArticle($objArticle, $this);
		
		//$this->Template->article = $newsarticle->generate();
		return $newsarticle->generate();

		
	}
	
	protected function compile()
	{
		return;
	}

    
}
