<?php

namespace JchOptimize;

/**
 * This is a regular expressions based implementation of the JSMin algorithim as decsribed on 
 * Douglas Crockford's page at http://www.crockford.com/javascript/jsmin.html in PHP and also 
 * guided by the PHP port written by  Ryan Grove <ryan@wonko.com>
 * 
 * This was written to provide a PHP tool to minify javascript but with an emphasis on speed, 
 * in particular for tools that want to minify javascript on the fly such as http://www.jch-optimize.net. 
 * Based on independent comparison tests, this library consistently returns the same results as JSMin.php 
 * but on an average of 200 times faster.
 * 
 * Permission is hereby granted to use this version of the library under the
 * same terms as jsmin.c, which has the following license:
 * 
 *  -- 
 * Copyright (c) 2002 Douglas Crockford  (www.crockford.com)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * The Software shall be used for Good, not Evil.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 * --
 * 
 * @author Samuel Marshall <sdmarshall73@gmail.com>
 * @copyright (c) 2002, Douglas Crockford <douglas@crockford.com> (jsmin.c)
 * @copyright (c) 2014, Samuel Marshall <sdmarshall73@gmail.com> (JSMinRegex.php)
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * 
 */
class JSMinRegex
{

        protected $options = array();

        public static function minify($js, $options = array())
        {
                $oMinifyJs = new JSMinRegex($options);
                return $oMinifyJs->process($js);
        }

        private function __construct($options)
        {
                $this->options = $options;
        }

        protected function process($js)
        {
                //replace carriage return with line feeds
                $js = str_replace(array("\r\n", "\r"), "\n", $js);

                //convert all other control characters to space
                $js = preg_replace('#[\t\f]#S', ' ', $js);


                //regex for double quoted strings
                $s1 = '"(?>(?:\\\\.)?[^\\\\"]*+)+?"';

                //regex for single quoted string
                $s2 = "'(?>(?:\\\\.)?[^\\\\']*+)+?'";

                //regex for block comments
                $b = '/\*(?>[^/\*]++|//|\*(?!/)|(?<!\*)/)*+\*/';

                //regex for line comments
                $c = '//[^\n]*+';

                //We have to do some manipulating with regexp literals; Their pattern is a little 'irregular' but 
                //they need to be escaped
                //
                //characters that can precede a regexp literal
                $x1 = '[(,=:[!&|?+\-~*{;\n]';
                //keywords that can precede a regex literal
                $x2 = '\bcase|\belse|\bin|\breturn|\btypeof';
                //actual regexp literal
                $x3 = '/(?![/*])(?>(?(?=\\\\)\\\\.|\[(?>(?:\\\\.)?[^\]\n]*+)+?\])?[^\\\\/\n\[]*+)+?/';

                //spaces not followed by possible regexp
                $y = '[ ]*+(?!/(?![*/]))';
                //spaces not preceded by $x1 or $x2 and possibly followed by / that is not part of a comment
                $z = "(?<!$x1|$x2)[ ]*+(?:/(?![/*]))?";
                
                //Remove spaces before regexp literals preserving space after keywords
                $js = preg_replace("#(?>[^'\"/ ]*+(?>$s1|$s2|$b|$c|$y|$z)?)*?\K(?>(?<=$x1)[ ]*+($x3)|(?<=$x2)([ ])[ ]*+($x3)|$)#siS", '$1$2$3', $js);
              
                //regex for complete regexp literal
                $x = "(?<={$x1}|\bcase |\belse |\bin |\breturn |\btypeof ){$x3}";

                //remove comments
                $js = preg_replace("#(?>[^'\"/]*+(?>{$s1}|{$s2}|{$x}|/(?![*/]))?)*?\K(?>{$b}|{$c}|$)#siS", '', $js);

                //replace runs of whitespace with single space or linefeed
                $js = preg_replace("#(?>[^'\"/\n ]*+(?>{$s1}|{$s2}|{$x}|[ \n](?![ \n])|/)?)*?\K(?>[ ]++(?=\n)|\n\K\s++|[ ]\K[ ]++|$)#siS", '', $js);
                
                //if regex literal ends line (without modifiers) insert semicolon
                $js = preg_replace("#({$x})\n(?![!\#%&`*./,:;<=>?@\^|~}\])\"'])#siS", '$1;', $js);

                //regex for removing spaces
                //remove space except when a space is preceded and followed by a non-ASCII character or by an ASCII letter or digit, 
                //or by one of these characters \ $ _  ...ie., all ASCII characters except those listed.
                $sp = '(?:(?<=(?P<s>["\'!\#%&`()*./,:;<=>?@\[\]\^{}|~])) | (?=(?P>s))|(?<=(?P<p>[+\-])) (?!\k<p>)|(?<=[^+\-]) (?!([^+\-])))';
                
                //spaces to keep
                $k1 = '(?<=[a-z0-9\\\\$_]) (?=[a-z0-9\\\\$_])';

                //regex for removing linefeeds
                //remove linefeeds except if it precedes a non-ASCII character or an ASCII letter or digit or one of these 
                //characters: \ $ _ [ ( { + - and if it follows a non-ASCII character or an ASCII letter or digit or one of these 
                //characters: \ $ _ ] ) } + - " ' ...ie., all ASCII characters except those listed respectively
                $ln = '(?:(?<=[!\#%&`*./,:;<=>?@\^|~{\[(])\n|\n(?=[!\#%&`*./,:;<=>?@\^|~}\])"\']))';
                
                //line feeds to keep
                $k2 = '(?<=[a-z0-9\\\\$_\])}+\-"\'])\n(?=[a-z0-9\\\\$_\[({+\-])';
                
                //remove unnecessary linefeeds and spaces
                $js = preg_replace("#(?>[^'\"/\n ]*+(?>$s1|$s2|(?<={$x1}){$x3}|(?<={$x2})[ ]{$x3}|/|$k1|$k2)?)*?\K(?>$sp|$ln|$)#siS", '', $js);


                return trim($js);
        }
}
