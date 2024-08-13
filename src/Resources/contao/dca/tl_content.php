<?php

/**
 * @copyright  Bright Cloud Studio
 * @author     Bright Cloud Studio
 * @package    Contao CE News
 * @license    LGPL-3.0+
 * @see	       https://github.com/bright-cloud-studio/contao-ce-news
 */

use Contao\Backend;
use Contao\BackendUser;
use Contao\Config;
use Contao\Controller;
use Contao\DataContainer;
use Contao\Image;
use Contao\StringUtil;
use Contao\System;

// Get the existing DCA
$dc = &$GLOBALS['TL_DCA']['tl_content'];
// Add our 'News Article' palette
$dc['palettes']['news_article'] = '{type_legend},type,headline;{news_legend},news_archive,news,news_template,size,news_metaFields;{protected_legend:hide},protected;{expert_legend:hide},guests,invisible,cssID,space';

// Add our 'News Article' fields
$GLOBALS['TL_DCA']['tl_content']['fields']['news_archive'] = array(
	'label'                 => &$GLOBALS['TL_LANG']['tl_content']['news_archive'],
	'exclude'               => true,
	'inputType'             => 'select',
	'options_callback'      => array('tl_content_newsarticle', 'getNewsArchives'),
	'eval'					=> array('submitOnChange'=>true, 'chosen'=>true, 'includeBlankOption' => true),
	'sql'					=> "int(10) unsigned NOT NULL default '0'",
);

$GLOBALS['TL_DCA']['tl_content']['fields']['news'] = array(
	'label'                 => &$GLOBALS['TL_LANG']['tl_content']['news'],
	'exclude'               => true,
	'search'                => true,
	'inputType'             => 'select',
	'reference'				=> &$GLOBALS['TL_LANG']['tl_content']['news'],
	'options_callback'      => array('tl_content_newsarticle', 'getNews'),
	'eval'                  => array('submitOnChange'=>true, 'chosen'=>true, 'includeBlankOption' => true),
	'wizard' => array
	(
		array('tl_content_newsarticle', 'editNewsLink')
	),
	'sql'					=> "int(10) unsigned NOT NULL default '0'",
);

$GLOBALS['TL_DCA']['tl_content']['fields']['news_template'] = array(
	'label'                 => &$GLOBALS['TL_LANG']['tl_content']['news'],
	'default'               => 'news_short',
	'exclude'               => true,
	'inputType'             => 'select',
	'options_callback'      => array('tl_content_newsarticle', 'getNewsTemplates'),
	'eval'                  => array('tl_class'=>'w50', 'chosen'=>true),
	'sql'					=> "varchar(64) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_content']['fields']['news_metaFields'] = array
(
	'label'                 => &$GLOBALS['TL_LANG']['tl_content']['news_metaFields'],
	'default'               => array('date', 'author'),
	'exclude'               => true,
	'inputType'             => 'checkbox',
	'options'               => array('date', 'author', 'comments'),
	'reference'             => &$GLOBALS['TL_LANG']['MSC'],
	'eval'                  => array('multiple'=>true, 'tl_class'=>'clr'),
	'sql'					=> "varchar(255) NOT NULL default ''",
);


class tl_content_newsarticle extends Backend
{

	public function __construct()
	{
		parent::__construct();
	}

	public function editNewsLink(DataContainer $dc)
	{
		return ($dc->value < 1) ? '' : ' <a href="contao/main.php?do=news&amp;table=tl_news&amp;act=edit&amp;id=' . $dc->value . '" title="'.sprintf(StringUtil::specialchars($GLOBALS['TL_LANG']['tl_content']['editalias'][1]), $dc->value).'" style="padding-left:3px;">' . Image::getHtml('alias.gif', $GLOBALS['TL_LANG']['tl_content']['editalias'][0], 'style="vertical-align:top;"') . '</a>';
	}

	public function getNewsArchives(DataContainer $dc)
	{
		$arrArchives = array();
		$objArchives = $this->Database->execute("SELECT id, title FROM tl_news_archive ORDER BY title");

		while ($objArchives->next())
		{
			$arrArchives[$objArchives->id] = $objArchives->title;
		}

		return $arrArchives;
	}

	public function getNews(DataContainer $dc)
	{
		$arrNews = array();
		$objNews = $this->Database->prepare('SELECT * FROM tl_news WHERE pid = ? ORDER BY date DESC')->execute($dc->activeRecord->news_archive);

		while ($objNews->next())
		{
            if($objNews->published == 0)
            {
                $arrNews['unpublished'][$objNews->id] = $objNews->headline;
                continue;
            }

            $arrNews[$objNews->id] = $objNews->headline;
		}

		return $arrNews;
	}

	public function getNewsTemplates(DataContainer $dc)
	{
		return $this->getTemplateGroup('news_');
	}

}
