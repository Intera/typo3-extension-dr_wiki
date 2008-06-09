<?php
// Returns a list of the pages that are in this category (default)
// or the category defined in the parameter.
// Alphabetical listing was inspired by MediaWiki's code

class tx_drwiki_pi1_categoryindex {
		
	function getDefaultParams(){
		return array('');
	}
	
	function main($object, $params){
    $entrykey = $params[0];
    if ($entrykey=='') { 
    	$entrykey = $object->piVars["keyword"];
    } else {
    	$entrykey = 'Category:'.$entrykey;
    }
     
		$results = $object->getWikiInfos("keyword" ,TRUE, FALSE, TRUE, " AND tx_drwiki_pages.body LIKE '%[[".$GLOBALS['TYPO3_DB']->quoteStr($entrykey,'tx_drwiki_pages')."%'");
        
        if ($results){
	        	$content = $this->formatList($results);
        }
	
	  	return $content;
	}

	/**
	 * Format a list of articles chunked by letter, either as a
	 * bullet list or a columnar format, depending on the length.
	 *
	 * @param array $articles
	 * @param int   $cutoff
	 * @return string
	 * @private
	 */
	function formatList( $articles, $cutoff = 6 ) {
		if ( count ( $articles ) > $cutoff ) {
			return $this->columnList( $articles );
		} elseif ( count($articles) > 0) {
			// for short lists of articles in categories.
			return $this->shortList( $articles );
		}
		return '';
	}

	/**
	 * Format a list of articles chunked by letter in a three-column
	 * list, ordered vertically.
	 *
	 * @param array $articles
	 * @return string
	 * @private
	 */
	function columnList( $articles ) {
		// divide list into three equal chunks
		$chunk = (int) (count ( $articles ) / 3);

		// get and display header
		$r = '<table width="100%"><tr valign="top">';

		$prev_start_char = 'none';

		// loop through the chunks
		for($startChunk = 0, $endChunk = $chunk, $chunkIndex = 0;
			$chunkIndex < 3;
			$chunkIndex++, $startChunk = $endChunk, $endChunk += $chunk + 1)
		{
			$r .= "<td>\n";
			$atColumnTop = true;

			// output all articles in category
			for ($index = $startChunk ;
				$index < $endChunk && $index < count($articles);
				$index++ )
			{
				// check for change of starting letter or begining of chunk
				if ( ($index == $startChunk) ||
					 (strtoupper($articles[$index]["keyword"]{0}) != strtoupper($articles[$index-1]["keyword"]{0})) )

				{
					if( $atColumnTop ) {
						$atColumnTop = false;
					} else {
						$r .= "</ul>\n";
					}
					$cont_msg = "";
					if ( strtoupper($articles[$index]["keyword"]{0}) == strtoupper($prev_start_char) )
						$cont_msg = " " . "<small>(continued)</small>";
					$r .= "<h4>" . htmlspecialchars( strtoupper($articles[$index]["keyword"]{0}) ) . "$cont_msg</h4>\n<ul>";
					$prev_start_char = strtoupper($articles[$index]["keyword"]{0});
				}

				$r .= "<li>[[".$articles[$index]["keyword"]."]]</li>\n";
			}
			if( !$atColumnTop ) {
				$r .= "</ul>\n";
			}
			$r .= "</td>\n";


		}
		$r .= "</tr></table>";
		return $r;
	}

	/**
	 * Format a list of articles chunked by letter in a bullet list.
	 * @param array $articles
	 * @return string
	 * @private
	 */
	function shortList( $articles ) {
		$r = "<h4>" . htmlspecialchars( strtoupper($articles[0]["keyword"]{0}) ) . "</h4>\n";
		$r .= "<ul><li>[[".$articles[0]["keyword"]."]]</li>\n";
		for ($index = 1; $index < count($articles); $index++ )
		{
			if (strtoupper($articles[$index]["keyword"]{0}) != strtoupper($articles[$index-1]["keyword"]{0}))
			{
				$r .= "</ul><h4>" . htmlspecialchars( strtoupper($articles[$index]["keyword"]{0}) ) . "</h4>\n<ul>";
			}

			$r .= "<li>[[".$articles[$index]["keyword"]."]]</li>\n";
		}
		$r .= "</ul>";
		return $r;
	}

}
	
?>