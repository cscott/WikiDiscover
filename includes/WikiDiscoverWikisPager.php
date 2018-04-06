<?php

class WikiDiscoverWikisPager extends TablePager {
	function __construct( $wiki, $language, $category ) {
		$this->wiki = $wiki;
		$this->language = $language;
		$this->category = $category;
		$this->wikiDiscover = new WikiDiscover();
		parent::__construct( $this->getContext() );
	}

	function getFieldNames() {
		static $headers = null;

		$headers = [
			'wiki_dbname' => 'wikidiscover-table-wiki',
			'wiki_language' => 'wikidiscover-table-language',
			'wiki_closed' => 'wikidiscover-table-state',
			'wiki_private' => 'wikidiscover-table-visibility',
			'wiki_category' => 'wikidiscover-table-category',
		];

		foreach ( $headers as &$msg ) {
			$msg = $this->msg( $msg )->text();
		}

		return $headers;
	}

	function formatValue( $name, $value ) {
		global $wgCreateWikiCategories;

		$row = $this->mCurrentRow;

		$wikidiscover = $this->wikiDiscover;

		$wiki = $row->wiki_dbname;

		switch ( $name ) {
			case 'wiki_dbname':
				$url = $wikidiscover->getUrl( $wiki );
				$name = $wikidiscover->getSitename( $wiki );
				$formatted = "<a href=\"{$url}\">{$name}</a>";
				break;
			case 'wiki_language':
				$formatted = $wikidiscover->getLanguage( $wiki );
				break;
			case 'wiki_closed':
				if ($wikidiscover->isClosed( $wiki ) === true ) {
					$formatted = 'Closed';
				} elseif ( $wikidiscover->isInactive( $wiki ) === true ) {
					$formatted = 'Inactive';
				} else {
					$formatted = 'Open';
				}
				break;
			case 'wiki_private':
				if ( $wikidiscover->isPrivate( $wiki ) === true ) {
					$formatted = 'Private';
				} else {
					$formatted = 'Public';
				}
				break;
			case 'wiki_category':
				$wikicategories = array_flip( $wgCreateWikiCategories );
				$formatted = $wikicategories[$row->wiki_category];
				break;
			default:
				$formatted = "Unable to format $name";
				break;
		}

		return $formatted;
	}

	function getQueryInfo() {
		$info = [
			'tables' => [ 'cw_wikis' ],
			'fields' => [ 'wiki_dbname', 'wiki_language', 'wiki_private', 'wiki_closed', 'wiki_category' ],
			'conds' => [],
			'joins_conds' => [],
		];

		if ( $this->language ) {
			$info['conds']['wiki_language'] = $this->language;
		}

		if ( $this->category ) {
			$info['conds']['wiki_category'] = $this->category;
		}

		return $info;
	}

	function getDefaultSort() {
		return 'wiki_dbname';
	}

	function isFieldSortable( $name ) {
		return true;
	}
}
