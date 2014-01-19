<?php
/**
 * Baybayin Sript Generator and Translator.
 * @version 0.5
 * @date 2013.01.17
 * @author Marco San Andres (chitetskoy@gmail.com)
 * @license MIT License
 * 
 * 
 * The MIT License (MIT)
 * Copyright (c) 2014 Marco San Andres (chitetskoy@gmail.com)
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software 
 * and associated documentation files (the "Software"), to deal in the Software without restriction,
 * including without limitation the rights to use, copy, modify, merge, publish, distribute, 
 * sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is 
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or 
 * substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING 
 * BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND 
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, 
 * DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, 
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

/**
 * Definitions
 */
## character types for baybayin
define("TNH_BAYBAYIN_CHAR_NONE", 0);
define("TNH_BAYBAYIN_CHAR_VOWEL", 1);
define("TNH_BAYBAYIN_CHAR_CONSONANT", 2);
define("TNH_BAYBAYIN_CHAR_DIACRITIC", 3);


/**
 * Some default variables
 */
## Path to the Includes file
$wgTnhBaybayinIncludes = __DIR__ . '';
//$wgTnhBaybayinBaseUrl = $wgScriptPath . '/extensions/Baybayin';
 
## Options
$wgTnhBaybayinOptions = array();

## Option to include font CSS when this is called.
$wgTnhBaybayinOptions['include_font_css'] = true;

## Option to enclosed Baybayin text into some stylized span tag?
$wgTnhBaybayinOptions['enclose_in_span'] = true;



/**
 * Prepare Functions and Hooks
 */
$wgHooks['ParserFirstCallInit'][] = 'tnhBabyayinParserInit';
//$wgHooks['BeforePageDisplay'][] = 'tnhBaybayinParserBeforePageDisplay';

	
// Resource Modules 
$wgResourceModules['ext.tnhBaybayin'] = array(
	// JavaScript and CSS styles. To combine multiple files, just list them as an array.
	'styles' => 'Baybayin.css',
	
	// You need to declare the base path of the file paths in 'scripts' and 'styles'
	'localBasePath' => __DIR__,
	
	// remote path?
	'remoteExtPath' => 'Baybayin'
);
 
// Hook our callback function into the parser
function tnhBabyayinParserInit( Parser $parser ) {
	
	// When the parser sees the <baybayin> tag, it executes 
	// the tnhBabyayinRender function (see below)
	$parser->setHook( 'baybayin', 'tnhBabyayinRender' );
	
	
	// Always return true from this function. The return value does not denote
	// success or otherwise have meaning - it just must always be true.
	return true;
}
 
// Execution of the tag function
function tnhBabyayinRender( $input, array $args, Parser $parser, PPFrame $frame ) {
	global $wgOut;

	$parser->disableCache();
	
	// Add modules
	$wgOut->addModules( 'ext.tnhBaybayin' );

	// startom
	$trans = new tnhBaybayinTransParser();
	$trans->text_orig = $input;
	
	if( isset( $args['prespanish'] ) )
		$trans->mode_is_prespanish_ = true;

	// output
	$out = $trans->text_parse();
	
	// enclose
	$out = '<span class="tn-baybayin-render">'. $out . '</span>';
	
	// return
	return $out;
}



/**
 * -- the main parser class.
 */
class tnhBaybayinTransParser 
{
	/**
	 * -- the character mappings.
	 */
	public $_charmap = array();
	
	/**
	 * -- generates the character maps to be used in this
	 *	baybayin translator.
	 */
	function _charmap_gen()
	{
		// Alphabets
		$this->_charmap_add("a",	"&#x1700;",	TNH_BAYBAYIN_CHAR_VOWEL);
		$this->_charmap_add("e",	"&#x1701;",	TNH_BAYBAYIN_CHAR_VOWEL);
		$this->_charmap_add("i",	"&#x1701;",	TNH_BAYBAYIN_CHAR_VOWEL);
		$this->_charmap_add("o",	"&#x1702;",	TNH_BAYBAYIN_CHAR_VOWEL);
		$this->_charmap_add("u",	"&#x1702;",	TNH_BAYBAYIN_CHAR_VOWEL);
		
		$this->_charmap_add("b",	"&#x170A;",	TNH_BAYBAYIN_CHAR_CONSONANT);
		$this->_charmap_add("k",	"&#x1703;",	TNH_BAYBAYIN_CHAR_CONSONANT);
		$this->_charmap_add("d",	"&#x1707;",	TNH_BAYBAYIN_CHAR_CONSONANT);
		$this->_charmap_add("g",	"&#x1704;",	TNH_BAYBAYIN_CHAR_CONSONANT);
		$this->_charmap_add("h",	"&#x1711;",	TNH_BAYBAYIN_CHAR_CONSONANT);
		$this->_charmap_add("l",	"&#x170e;",	TNH_BAYBAYIN_CHAR_CONSONANT);
		$this->_charmap_add("m",	"&#x170B;",	TNH_BAYBAYIN_CHAR_CONSONANT);
		$this->_charmap_add("n",	"&#x1708;",	TNH_BAYBAYIN_CHAR_CONSONANT);
		$this->_charmap_add("ng",	"&#x1705;",	TNH_BAYBAYIN_CHAR_CONSONANT);
		$this->_charmap_add("p",	"&#x1709;",	TNH_BAYBAYIN_CHAR_CONSONANT);
		$this->_charmap_add("r",	"&#x170d;",	TNH_BAYBAYIN_CHAR_CONSONANT);
		$this->_charmap_add("s",	"&#x1710;",	TNH_BAYBAYIN_CHAR_CONSONANT);
		$this->_charmap_add("t",	"&#x1706;",	TNH_BAYBAYIN_CHAR_CONSONANT);
		$this->_charmap_add("w",	"&#x170f;",	TNH_BAYBAYIN_CHAR_CONSONANT);
		$this->_charmap_add("y",	"&#x170c;",	TNH_BAYBAYIN_CHAR_CONSONANT);
		
		// Kudlit
		$this->_charmap_add("a",	"",		TNH_BAYBAYIN_CHAR_DIACRITIC);
		$this->_charmap_add("e",	"&#x1712;",	TNH_BAYBAYIN_CHAR_DIACRITIC);
		$this->_charmap_add("i",	"&#x1712;",	TNH_BAYBAYIN_CHAR_DIACRITIC);
		$this->_charmap_add("o",	"&#x1713;",	TNH_BAYBAYIN_CHAR_DIACRITIC);
		$this->_charmap_add("u",	"&#x1713;",	TNH_BAYBAYIN_CHAR_DIACRITIC);
		
		$this->_charmap_add("#VIRAMA","&#x1714;",	TNH_BAYBAYIN_CHAR_DIACRITIC);
		
		// Extra
		$this->_charmap_add("~",	"");           

		//var_dump( $this->_charmap );
	}
	
	/**
	 * -- add a character to the #this->_charmap character table.
	 *	
	 *	@param string $char - the character from latin
	 *	@param string $equiv - the equivalent in baybayin, either plain or html-encoded
	 *	@param int $type the type of this, prefixed with TNH_BAYBAYIN_CHAR_{something} VARIABLE
	 */
	function _charmap_add($char, $equiv, $type=TNH_BAYBAYIN_CHAR_NONE)
	{
		$cpfx = $char;
	
		// -- character previx
		if( 	$type != TNH_BAYBAYIN_CHAR_VOWEL && $type != TNH_BAYBAYIN_CHAR_CONSONANT && $type != TNH_BAYBAYIN_CHAR_NONE )
		{
			$cpfx = $type.":".$char;
		}
		// -- add now
		$this->_charmap[$cpfx] = new tnhBaybayinChar($char, html_entity_decode($equiv,ENT_NOQUOTES,'UTF-8'), $type);
	}
	
	public $text_parsed;
	
	public $text_orig;
	
	public $_text_parse_pos = 0;
	
	
	/**
	 * -- Tells if pre-spanish mode is to be made.
	 */
	public $mode_is_prespanish_ = false;
	
	/**
	 * -- parse the text now
	 */
	function text_parse()
	{
		// -- reset
		$this->text_parsed = "";
		$this->_text_parse_pos = 0;
		
		// -- startom
		$this->_charmap_gen();
		
		// -- mode: pre-spanish mode?
		$mode_is_prespanish = $this->mode_is_prespanish_;
	
		// -- 
		do 
		{
			// -- current character info
			$charInfo = null;
		
			// -- for the letter combinations, let's start looking for letter by pairs.
			for( $x = 2; $x >= 1; $x-- )
			{
				// let's fix this later on.
				$curCharRaw = substr( $this->text_orig, $this->_text_parse_pos, $x );
				$curChar    = strtolower( $curCharRaw );
			
				// parse vars
				$fin_baseletter = null; // <- basevowel
				$fin_diacritic = null; // <- diacritic
			
				// -- check for the current character, if it exists.
				$charInfo = @$this->_charmap[$curChar];
				if( isset( $charInfo ) )
				{
					// basevowel
					$fin_baseletter = $charInfo->equiv_;
					
				
					// -- check if character is consonant
					if( $charInfo->type_ == TNH_BAYBAYIN_CHAR_CONSONANT )
					{
						// -- for the next character.
						$nextCharRaw = substr( $this->text_orig, $this->_text_parse_pos + $x, 1 );
						$nextChar    = strtolower( $nextCharRaw );
						$nextCharInfo = @$this->_charmap[$nextChar];
						
						// -- init values
						$parse_has_diacritic = false; // <- indicates that a diacritic has been processed.
						
						// -- there is a next character to scan?
						if( isset( $nextCharInfo ) )
						{
							// -- is the next character a vowel? then process it as a diacritic.
							//	$parse_has_diacritic will be set to true if there is a valid diacritic
							//	associated with this character.
							if( 	$nextCharInfo->type_ == TNH_BAYBAYIN_CHAR_VOWEL )
							{
								// there's a diacritic?
								$nextCharInfo = @$this->_charmap[TNH_BAYBAYIN_CHAR_DIACRITIC . ':'.$nextChar];
								if( isset( $nextCharInfo ) )
								{
									$parse_has_diacritic = true;
									$fin_diacritic = $nextCharInfo->equiv_;
									$this->_text_parse_pos++;
								}
							}
							// -- pre-spanish mode? the next character is not
							/*elseif( $mode_is_prespanish ) {
								$fin_baseletter = null;
								//$this->_text_parse_pos++;
							}*/
						}
						else {
							
						}
						
						// -- No diacritic processed, then process it as a virama.
						if( ! $parse_has_diacritic )
						{
							// prespanish mode? no more virama and cancel baseletter
							if(   $mode_is_prespanish ){
								$fin_baseletter = null;
							}
							// modern mode? add virama
							else {
								$nextCharInfo = $this->_charmap[TNH_BAYBAYIN_CHAR_DIACRITIC . ':#VIRAMA'];
								$fin_diacritic = $nextCharInfo->equiv_;
							}
						}
					}
					
					// final
					$this->text_parsed .= 
						  (isset($fin_baseletter) ? $fin_baseletter : '')
						. (isset($fin_diacritic) ? $fin_diacritic : '');
					
					// -- advanced to next.
					$this->_text_parse_pos += $x;
					break;
				}
			}
			
			// -- else let's add the character
			if( ! $charInfo )
			{
				$this->text_parsed .= $curCharRaw;
				
				// -- default move.
				$this->_text_parse_pos++;
			}
			
			// -- move
		} while( $this->_text_parse_pos <= strlen( $this->text_orig ) );
		
		// --
		return $this->text_parsed;
	}
}

/**
 * -- a typical baybayin character class.
 */
class tnhBaybayinChar 
{
	function __construct($char, $equiv, $type)
	{
		$this->char_  = $char;
		$this->equiv_ = $equiv;
		$this->type_  = $type;
	}
	
	public $char_;
	
	public $type_;
	
	public $equiv_;
}